<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TermRelation extends Model
{
    protected $table = 'term_relation';

    public function deleteRelationByTermID($term_id)
    {
        $deleted = DB::table($this->getTable())->where('term_id', $term_id)->delete();
        return $deleted ? $deleted : false;
    }

    public function get_the_terms($post_id, $taxonomy = false){
        if($taxonomy){
            return DB::table($this->getTable())->join('term', 'term_relation.term_id', '=', 'term.term_id', 'inner')->join('taxonomy', 'term.taxonomy_id', '=', 'taxonomy.taxonomy_id', 'inner')->where('service_id', $post_id)->where('taxonomy.taxonomy_id', $taxonomy)->get();
        }else{
            return DB::table($this->getTable())->join('term', 'term.term_id', 'term_relation.term_id')->where('term_relation.service_id', $post_id)->get();
        }
    }

    public function deleteRelationByServiceID($service_id, $type = '')
    {
        if(empty($type)){
            $deleted = DB::table($this->getTable())->where('service_id', $service_id)->delete();
        }else{
            $deleted = DB::table($this->getTable())->join('term', 'term_relation.term_id', '=', 'term.term_id', 'inner')->join('taxonomy', 'term.taxonomy_id', '=', 'taxonomy.taxonomy_id', 'inner')->where('service_id', $service_id)->where('taxonomy_name', $type)->delete();
        }
        return $deleted ? $deleted : false;
    }

    public function createRelation($term_id, $service_id)
    {
        $data = [
            'term_id' => (int)$term_id,
            'service_id' => $service_id
        ];
        return DB::table($this->getTable())->insertGetId($data);
    }
}
