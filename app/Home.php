<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Sentinel;

class Home extends Model
{
    protected $table = 'home';
    protected $primaryKey = 'post_id';

    public function getHomeByHomeTypeID($hometype_id)
    {
        $sql = DB::table($this->getTable())->selectRaw("COUNT(*)");
        $sql->where('home_type', $hometype_id);
        $sql->whereRaw("status = 'publish'");
        $results = $sql->count();
        return $results;
    }

    public function getSearchResult($data)
    {
        $default = [
            'page' => 1,
            'lat' => '0',
            'lng' => '0',
            'address' => '',
            'checkIn' => '',
            'checkOut' => '',
            'checkInTime' => '',
            'checkOutTime' => '',
            'startTime' => '12:00 AM',
            'endTime' => '11:30 PM',
            'bookingType' => 'per_night',
            'num_adults' => 0,
            'num_children' => 0,
            'num_infants' => 0,
            'price_filter' => '',
            'home-type' => '',
            'home-amenity' => '',
            'number' => 6
        ];
        $data = wp_parse_args($data, $default);
        $number = intval($data['number']);

        $sql = DB::table($this->getTable())->selectRaw("SQL_CALC_FOUND_ROWS *");

        if (!empty($data['lat']) && !empty($data['lng'])) {
            $distance = get_option('search_radius', '25');
            $data['lat'] = esc_sql($data['lat']);
            $data['lng'] = esc_sql($data['lng']);
            $sql->selectRaw("( 6371 * acos( cos( radians({$data['lat']}) ) * cos( radians( home.location_lat ) ) * cos( radians( home.location_lng ) - radians({$data['lng']}) ) + sin( radians({$data['lat']}) ) * sin( radians( home.location_lat ) ) ) ) AS distance");
            $sql->orHavingRaw("distance <= " . $distance);
            $sql->orderByDesc('distance');
        } elseif (!empty($data['address'])) {
            $address = urldecode($data['address']);
            $data['address'] = esc_sql($data['address']);
            $sql->whereRaw("home.location_address LIKE '%{$address}%'");
            $sql->orderByDesc('home.post_id');
        }

        if (!empty($data['num_adults']) || !empty($data['num_children'])) {
            $number_of_guest = intval($data['num_adults']) + intval($data['num_children']);
            $sql->whereRaw("number_of_guest >= {$number_of_guest}");
        }

        if (!empty($data['price_filter'])) {
            $min_max = explode(';', $data['price_filter']);
            $min = 0;
            $max = 0;
            if (isset($min_max[0])) {
                $min = floatval($min_max[0]);
            }
            if (isset($min_max[1])) {
                $max = floatval($min_max[1]);
            }
            $sql->whereRaw("base_price >= {$min} AND base_price <= {$max}");
        }

        if (!empty($data['checkIn']) && !empty($data['checkOut']) && $data['bookingType'] == 'per_night') {
            $check_in = strtotime($data['checkIn']);
            $check_out = strtotime($data['checkOut']);
            $sql->selectRaw("COUNT(home_availability.home_id) as total_id");
            $sql->leftJoin('home_availability', function ($join) use ($check_in, $check_out) {
                $join->on('home.post_id', '=', 'home_availability.home_id');
                $join->whereRaw("
                (
                    (
                        start_date >= {$check_in}
                        AND
                        end_date <= {$check_out}
                    )
                    OR
                    (
                        start_date < {$check_in}
                        AND
                        end_date > {$check_out}
                    )
                    OR
                    (
                        start_date < {$check_in}
                        AND
                        end_date > {$check_in}
                        AND
                        end_date < {$check_out}
                    )
                    OR
                    (
                        start_date > {$check_in}
                        AND
                        start_date < {$check_out}
                        AND
                        end_date > {$check_out}
                    )
                    OR
                    (
                        booking_id = 0
                        AND
                        start_date <= {$check_in}
                        AND
                        end_date = {$check_in}
                    )
                    OR
                    (
                        booking_id = 0
                        AND
                        start_date = {$check_out}
                        AND
                        end_date >= {$check_out}
                    )
                )");
            });
            $sql->where('home.booking_type', 'per_night');
            $sql->groupBy(['home.post_id']);
            $sql->havingRaw("total_id = 0");
        }
        if (!empty($data['checkInTime']) && !empty($data['checkOutTime']) && $data['bookingType'] == 'per_hour') {
            $check_in = strtotime($data['checkInTime'] . ' ' . urldecode($data['startTime']));
            $check_out = strtotime($data['checkOutTime'] . ' ' . urldecode($data['endTime']));
            if($check_in < $check_out){
                $sql->selectRaw("COUNT(home_availability.home_id) as total_id");
                $sql->leftJoin('home_availability', function ($join) use ($check_in, $check_out) {
                    $join->on('home.post_id', '=', 'home_availability.home_id');
                    $join->whereRaw("
                (
                    (
                        start_time >= {$check_in}
                        AND
                        end_time <= {$check_out}
                    )
                    OR
                    (
                        start_time >= {$check_in}
                        AND
                        end_time >= {$check_out}
                    )
                    OR
                    (
                        start_time < {$check_in}
                        AND
                        end_time > {$check_out}
                    )
                    OR
                    (
                        start_time < {$check_in}
                        AND
                        end_time > {$check_in}
                        AND
                        end_time < {$check_out}
                    )
                    OR
                    (
                        start_time > {$check_in}
                        AND
                        start_time < {$check_out}
                        AND
                        end_time > {$check_out}
                    )
                    OR
                    (
                        booking_id = 0
                        AND
                        start_date <= {$check_in}
                        AND
                        end_date = {$check_in}
                    )
                    OR
                    (
                        booking_id = 0
                        AND
                        start_date = {$check_out}
                        AND
                        end_date >= {$check_out}
                    )
                )");
                });
                $sql->where('home.booking_type', 'per_hour');
                $sql->groupBy(['home.post_id']);
                $sql->havingRaw("total_id = 0");
            }
        }


        if (!empty($data['home-type'])) {
            $data['home-type'] = esc_sql($data['home-type']);
            $sql->whereRaw("home.home_type IN ({$data['home-type']})");
        }

        if (!empty($data['home-amenity'])) {
            $amen_arr = explode(',', $data['home-amenity']);
            $sql_amen = [];
            foreach ($amen_arr as $k => $v) {
                array_push($sql_amen, "( FIND_IN_SET({$v}, home.amenities) )");
            }
            if (!empty($sql_amen)) {
                $sql->whereRaw("(" . implode(' OR ', $sql_amen) . ")");
            }
        }

        $sql->whereRaw("status = 'publish'");

        $offset = ($data['page'] - 1) * $number;
        $sql->limit($number)->offset($offset);

        $results = $sql->get();
        $count = DB::select("SELECT FOUND_ROWS() as `row_count`")[0]->row_count;

        return [
            'total' => $count,
            'results' => $results
        ];
    }

    public function listOfHomes($data)
    {
        $default = [
            'id' => '',
            'location' => [],
            'page' => 1,
            'orderby' => 'post_id',
            'order' => 'desc',
            'is_featured' => '',
            'not_in' => [],
            'number' => posts_per_page(),
        ];

        $data = wp_parse_args($data, $default);
        $number = $data['number'];
        $is_featured = $data['is_featured'];

        $sql = DB::table($this->getTable())->selectRaw("SQL_CALC_FOUND_ROWS home.*")->where('home.status', 'publish')->orderBy($data['orderby'], $data['order']);

        if (!empty($data['id'])) {
            $sql->whereRaw("home.post_id IN ({$data['id']})");
        }
        if (!empty($data['not_in']) && is_array($data['not_in'])) {
            $not_in = implode(',', $data['not_in']);
            $sql->whereRaw("home.post_id NOT IN ({$not_in})");
        }
        if (!empty($data['location'])) {
            $lat = (isset($data['location']['lat'])) ? (float)$data['location']['lat'] : 0;
            $lng = (isset($data['location']['lng'])) ? (float)$data['location']['lng'] : 0;
            $radius = (isset($data['location']['radius'])) ? (float)$data['location']['radius'] : get_option('search_radius', '25');
            $sql->selectRaw("( 6371 * acos( cos( radians({$lat}) ) * cos( radians( home.location_lat ) ) * cos( radians( home.location_lng ) - radians({$lng}) ) + sin( radians({$lat}) ) * sin( radians( home.location_lat ) ) ) ) AS distance");
            $sql->groupBy('home.post_id')->having("distance", '<=', $radius);
        }
        if (!empty($is_featured)) {
            $sql->where('is_featured', 'on');
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

    public function getAllHomes($data)
    {
        $default = [
            'search' => '',
            'page' => 1,
            'status' => '',
            'orderby' => 'post_id',
            'order' => 'desc',
            'author' => get_current_user_id(),
            'number' => posts_per_page()
        ];
        $data = wp_parse_args($data, $default);
        $number = $data['number'];

        $sql = DB::table($this->getTable())->selectRaw("SQL_CALC_FOUND_ROWS home.*")->orderBy($data['orderby'], $data['order']);
        if (!is_admin()) {
            $sql->where('author', get_current_user_id());
        }
        if ($number != -1) {
            $offset = ($data['page'] - 1) * $number;
            $sql->limit($number)->offset($offset);
        }

        if (!empty($data['search'])) {
            $data['search'] = esc_sql($data['search']);
            if (is_numeric($data['search'])) {
                $sql->where('home.post_id', $data['search']);
            } else {
                $sql->whereRaw("(home.post_title LIKE '%{$data['search']}%' OR home.post_content LIKE '%{$data['search']}%')");
            }
        }

        if (!empty($data['status'])) {
            $sql->where('home.status', $data['status']);
        }

        $results = $sql->get();
        $count = DB::select("SELECT FOUND_ROWS() as `row_count`")[0]->row_count;

        return [
            'total' => $count,
            'results' => $results
        ];

    }

    public function getById($home_id)
    {
        return DB::table($this->table)->where('post_id', $home_id)->get()->first();
    }

    public function getByName($home_name)
    {
        return DB::table($this->table)->where('post_slug', $home_name)->get()->first();
    }

    public function getMinMaxPrice()
    {
        $result = DB::table($this->getTable())->selectRaw("min(base_price) as min, max(base_price) as max")->get()->first();
        if (!empty($result) && is_object($result)) {
            if ($result->min == $result->max) {
                $result->min = 0;
            }
            return (array)$result;
        }
        return ['min' => 0, 'max' => 500];
    }

    public function review_count()
    {
        return 0;
    }

    public function updateStatus($home_id, $new_status = '')
    {
        return DB::table($this->getTable())->where('post_id', $home_id)->update(['status' => $new_status]);
    }

    public function updateHome($data, $home_id)
    {
        return DB::table($this->getTable())->where('post_id', $home_id)->update($data);
    }

    public function createHome($data = [])
    {
        $default = [
            'post_title' => 'New Home - ' . time(),
            'post_slug' => 'new-home-' . time(),
            'created_at' => time(),
            'author' => get_current_user_id(),
            'status' => 'pending'
        ];

        $data = wp_parse_args($data, $default);
        return DB::table($this->getTable())->insertGetId($data);
    }

    public function deleteHomeItem($home_id)
    {
        $delete_avai = DB::table('home_availability')->where('home_id', $home_id)->where('booking_id', '=', 0)->delete();
        $delete_price = DB::table('home_price')->where('home_id', $home_id)->delete();

        return DB::table($this->getTable())->where('post_id', $home_id)->delete();
    }
}
