<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Sentinel;

class Taxonomy extends Model
{
    protected $table = 'taxonomy';
    protected $primaryKey = 'taxonomy_id';


    public function getById($taxonomy_id)
    {
        return DB::table($this->table)->where('taxonomy_id', $taxonomy_id)->get()->first();
    }

    public function getByName($taxonomy_name)
    {
        return DB::table($this->table)->where('taxonomy_name', $taxonomy_name)->get()->first();
    }

    public function getAll()
    {
        return DB::table($this->table)->get();
    }

    public function updateTaxonomy($data, $taxonomy_id)
    {
        return DB::table($this->getTable())->where('taxonomy_id', $taxonomy_id)->update($data);
    }

    public function create($data = [])
    {
        return DB::table($this->getTable())->insertGetId($data);
    }
}
