<?php

namespace App\Http\Controllers;

use App\Home;
use Sentinel;
use Illuminate\Support\Facades\Input;


class SearchController extends Controller
{
    public function getSearchResult()
    {
        $home = new Home();
        $post_data = Input::post();
        $data = $home->getSearchResult($post_data);
        $search_string = view('frontend.search.search_string', [
            'count' => $data['total'],
            'address' => Input::post('address'),
            'check_in' => Input::post('checkIn'),
            'check_out' => Input::post('checkOut')
        ])->render();

        if (isset($post_data['_token'])) {
            unset($post_data['_token']);
        }

        $url_temp = http_build_query($post_data);

        $html = view('frontend.home.loop.list', ['data' => $data])->render();

        $pag = view('frontend.search.search_pag', [
            'total' => $data['total'],
            'query_string' => '?' . $url_temp,
            'current_url' => $post_data['current_url'],
            'page' => $post_data['page'],
            'number' => isset($post_data['number']) ? intval($post_data['number']) : 6
        ])->render();;

        $locations = [];
        if ($data['total'] > 0) {
            foreach ($data['results'] as $k => $v) {
                $locations[$k] = [
                    'lat' => $v->location_lat,
                    'lng' => $v->location_lng,
                    'price' => convert_price($v->base_price),
                    'post_id' => $v->post_id,
                    'title' => $v->post_title,
                    'url' => get_home_permalink($v->post_id, $v->post_slug, 'home'),
                    'thumbnail' => get_attachment_url($v->thumbnail_id, [75, 75])
                ];
            }
        }else{
            $lat = Input::post('lat', 0);
            $lng = Input::post('lng', 0);
            $locations[0] = [
                'lat' => $lat,
                'lng' => $lng
            ];
        }

        $this->sendJson([
            'status' => true,
            'search_string' => $search_string,
            'html' => $html,
            'pag' => $pag,
            'locations' => $locations,
            'total' => $data['total']
        ], true);
    }

    public function searchPage($page = '1')
    {
        return view("frontend.search", ['page' => $page]);
    }
}
