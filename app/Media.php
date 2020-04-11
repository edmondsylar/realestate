<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Media extends Model
{
    protected $table = 'media';
    protected $primaryKey = 'media_id';

    public function deleteAttachment($attachment_id)
    {
        return DB::table($this->table)->where('media_id', $attachment_id)->delete();
    }

    public function issetAttachment($attachment_id)
    {
        $result = DB::table($this->table)->where('media_id', $attachment_id)->get(['media_id'])->first();
        return (!empty($result) && is_object($result)) ? $result->media_id : false;
    }

    public function listAttachments($filters = [])
    {
        $default = [
            'orderby' => 'created_at',
            'order' => 'desc',
            's' => '',
            'number' => -1,
            'paged' => 1,
        ];

        $filters = array_merge($default, $filters);
        $skip = ($filters['paged'] - 1) * $filters['number'];

        $sql = DB::table($this->table)->orderBy($filters['orderby'], $filters['order']);

        if ($filters['number'] != -1) {
            $sql->take($filters['number'])->skip($skip);
        }

        if (!is_admin()) {
            $user_id = get_current_user_id();
            $sql->where('author', $user_id);
        }

        return $sql->get();
    }

    public function getById($attachment_id)
    {
        return DB::table($this->table)->where('media_id', $attachment_id)->get()->first();
    }

    public function updateMedia($data, $media_id)
    {
        return DB::table($this->getTable())->where('media_id', $media_id)->update($data);
    }

    public function create($data = [])
    {
        return DB::table($this->getTable())->insertGetId($data);
    }
}
