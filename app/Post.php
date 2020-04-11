<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Sentinel;

class Post extends Model
{
    protected $table = 'post';
    protected $primaryKey = 'post_id';

    public function insertData($sql){
        DB::table($this->getTable())->insertUsing([`post_title`, `post_slug`, `post_content`, `thumbnail_id`, `status`, `author`, `created_at`], "INSERT INTO `post` (`post_title`, `post_slug`, `post_content`, `thumbnail_id`, `status`, `author`, `created_at`) VALUES ('Talk about your business', 'talk-about-your-business', '123', '1', 'publish', 1, '1577177668')");
    }

    public function getBySlug($post_slug, $status = '')
    {
    	$sql = DB::table($this->table)->where('post_slug', $post_slug);
    	if(!empty($status)){
    		if(in_array($status, ['publish', 'draft'])) {
			    $sql->where( 'status', $status );
		    }
	    }
        return $sql->get()->first();
    }

    public function getTermByPostID($post_id, $tax = 'post-category'){
	    $result = DB::select("SELECT * FROM term_relation INNER JOIN term ON term_relation.term_id  = term.term_id INNER JOIN taxonomy ON term.taxonomy_id = taxonomy.taxonomy_id WHERE service_id = {$post_id} AND taxonomy_name = '{$tax}'");

	    return $result;
    }
    public function getNumberSuffixes($slug)
    {
        $result = DB::select("SELECT COUNT(*) AS Number_Suffixes FROM {$this->table} WHERE  post_slug  LIKE '$slug%'");

        return $result[0]->Number_Suffixes;
    }

    public function deletePost($post_id)
    {
	    DB::table('term_relation')->where('service_id', $post_id)->delete();
	    DB::table('comments')->where('post_id', $post_id)->delete();
	    return DB::table($this->table)->where('post_id', $post_id)->delete();
    }

    public function updatePost($data, $post_id)
    {
        return DB::table($this->getTable())->where('post_id', $post_id)->update($data);
    }

    public function getById($post_id)
    {
        global $post, $old_post;

        if (!is_null($post)) {
            if (isset($post->post_id) && $post->post_id == $post_id) {
                return $post;
            } else {
                $old_post = $post;
                $post = DB::table($this->table)->where('post_id', $post_id)->get()->first();
            }
        } else {
            $post = DB::table($this->table)->where('post_id', $post_id)->get()->first();
        }

        return $post;
    }

    public function createPost($data = [])
    {
        return DB::table($this->getTable())->insertGetId($data);
    }

    public function getAllPosts($data = [])
    {
        $default = [
            'search' => '',
            'page' => 1,
            'orderby' => 'post_id',
            'order' => 'desc',
	        'number' => posts_per_page(),
            'term_slug' => '',
            'status' => ''
        ];
        $data = wp_parse_args($data, $default);

        $number = $data['number'];

        $sql = DB::table($this->getTable())->selectRaw("SQL_CALC_FOUND_ROWS post.*")
            ->orderBy($data['orderby'], $data['order']);

        if($number != -1){
	        $offset = ($data['page'] - 1) * $number;
	        $sql->limit($number)->offset($offset);
        }
        if (!empty($data['search'])) {
            $data['search'] = esc_sql($data['search']);
            $sql->whereRaw("post.post_id = '{$data['search']}' OR post.post_title LIKE '%{$data['search']}%'");
        }

        if(!empty($data['term_slug'])){
            $term_slug = $data['term_slug'];
            $sql->join('term_relation', 'post.post_id', '=', 'term_relation.service_id', 'inner')->join('term', 'term_relation.term_id', '=', 'term.term_id', 'inner');
            $sql->where('term.term_name', $term_slug);
        }

        if(!empty($data['status']) && in_array($data['status'], ['publish', 'draft'])){
            $sql->where('status', $data['status']);
        }

        $results = $sql->get();

        $count = DB::select("SELECT FOUND_ROWS() as `row_count`")[0]->row_count;

        return [
            'total' => $count,
            'results' => $results
        ];

    }

    public function listOfPosts($data)
    {
        $default = [
            'id' => '',
            'page' => 1,
            'orderby' => 'post_id',
            'order' => 'desc',
            'number' => posts_per_page()
        ];

        $data = wp_parse_args($data, $default);
        $number = $data['number'];

        $sql = DB::table($this->getTable())->selectRaw("SQL_CALC_FOUND_ROWS post.*")->where('post.status', 'publish')->orderBy($data['orderby'], $data['order']);

        if (!empty($data['id'])) {
            $sql->whereRaw("post.post_id IN ({$data['id']})");
        }

        if ($number != -1) {
            $offset = ($data['page'] - 1) * $number;
            $sql->limit($number)->offset($offset);
        }

        $results = $sql->get();
        $count = DB::select("SELECT FOUND_ROWS() as `row_count`")[0]->row_count;

        return [
            'total' => $count,
            'results' => $results
        ];
    }

}
