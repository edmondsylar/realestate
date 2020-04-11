<?php

use Illuminate\Database\Seeder;
use App\Taxonomy;

class TaxonomyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('taxonomy')->truncate();
        $tax = new Taxonomy();
        $data = [
            [
                'taxonomy_title' => 'Home Type',
                'taxonomy_name' => 'home-type',
                'created_at' => time()
            ],
            [
                'taxonomy_title' => 'Home Amenity',
                'taxonomy_name' => 'home-amenity',
                'created_at' => time()
            ],
            [
                'taxonomy_title' => 'Categories',
                'taxonomy_name' => 'post-category',
                'created_at' => time()
            ],
            [
                'taxonomy_title' => 'Tags',
                'taxonomy_name' => 'post-tag',
                'created_at' => time()
            ]
        ];

        foreach ($data as $args) {
            $tax->create($args);
        }
    }
}
