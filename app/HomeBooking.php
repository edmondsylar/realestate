<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Sentinel;

class HomeBooking extends Model
{
    protected $table = 'home_booking';
    protected $primaryKey = 'ID';

    public function allBookings($data = [])
    {
        $default = [
            'search' => '',
            'page' => 1,
            'orderby' => 'ID',
            'order' => 'desc',
            'status' => '',
            'user_type' => '',
            'user_id' => '',
            'number' => posts_per_page()
        ];
        $data = wp_parse_args($data, $default);
        $number = $data['number'];
        $offset = ($data['page'] - 1) * $number;

        $sql = DB::table($this->getTable())->selectRaw("SQL_CALC_FOUND_ROWS *")
            ->orderBy($data['orderby'], $data['order'])->limit($number)->offset($offset);

        if (!empty($data['search'])) {
            $data['search'] = esc_sql($data['search']);
            $search = esc_sql($data['search']);
            $sql->whereRaw("(ID = '{$search}' OR booking_id = '{$search}' OR email like '%{$search}%') OR booking_description LIKE '%{$search}%'");
        }
        if (!empty($data['status'])) {
            $sql->where('status', $data['status']);
        }
        if (!empty($data['user_type']) && !empty($data['user_id'])) {
            $sql->where($data['user_type'], $data['user_id']);
        }
        $results = $sql->get();

        $count = DB::select("SELECT FOUND_ROWS() as `row_count`")[0]->row_count;

        return [
            'total' => $count,
            'results' => $results
        ];
    }

    public function getBookingHourItems($home_id, $start)
    {
        $start = esc_sql($start);
        $sql = DB::table($this->getTable())->whereRaw("start_date = {$start} AND service_id = {$home_id} AND `status` IN ('pending','incomplete', 'completed')");
        $results = $sql->get();
        $count = DB::select("SELECT FOUND_ROWS() as `row_count`")[0]->row_count;
        return [
            'total' => $count,
            'results' => $results
        ];
    }

    public function getBookingItems($home_id, $start, $end, $group = false)
    {
        $start = esc_sql($start);
        $end = esc_sql($end);
        $sql = DB::table($this->getTable())->whereRaw("((start_date >= {$start} AND end_date <= {$end}) OR (start_date <= {$start} AND end_date >= {$start}) OR (start_date <= {$end} AND end_date >= {$end})) AND service_id = {$home_id} AND `status` IN ('pending','incomplete', 'completed')");
        if ($group) {
            $sql->selectRaw('home_booking.*, count(home_booking.booking_id) as total')->groupBy('home_booking.start_date');
        }
        $results = $sql->get();
        $count = DB::select("SELECT FOUND_ROWS() as `row_count`")[0]->row_count;
        return [
            'total' => $count,
            'results' => $results
        ];
    }

    public function getTotalAmountByStatus($status = ['completed'], $userID = '', $userType = 'owner')
    {
        $status = preg_filter('/^/', '\'', (array)$status);
        $status = preg_filter('/$/', '\'', $status);
        $status = implode(',', $status);
        $sql = DB::table($this->getTable())->selectRaw("sum(total) as total")->whereRaw("status IN ({$status})");
        if (!empty($userID)) {
            if ($userType == 'owner') {
                $sql->where('owner', $userID);
            } else {
                $sql->where('_od.buyer', $userID);
            }
        }
        $result = $sql->get()->first();
        if (!empty($result) && is_object($result)) {
            return $result->total;
        }
        return 0;
    }

    public function getNetAmount($userID = '', $userType = 'owner')
    {
        $sql = DB::table($this->getTable())->selectRaw("sum(total) as total")->where('status', 'completed')->whereRaw("payment_type <> 'bank_transfer'");
        if (!empty($userID)) {
            if ($userType == 'owner') {
                $sql->where('owner', $userID);
            } else {
                $sql->where('_od.buyer', $userID);
            }
        }

        $result = $sql->get()->first();
        if (!empty($result) && is_object($result)) {
            return $result->total;
        }
        return 0;
    }

    public function getProjection($startDate, $endDate, $userID = '')
    {
        $startDate = esc_sql($startDate);
        $endDate = esc_sql($endDate);
        $sql = DB::table($this->getTable())->whereRaw("created_date between {$startDate} and {$endDate}")->whereRaw("status IN ('incomplete', 'completed', 'canceled', 'refunded')");
        if (!empty($userID)) {
            $sql->where('owner', $userID);
        }
        $result = $sql->get();
        return (!empty($result) && is_object($result)) ? $result : null;
    }

    public function getBooking($booking_id)
    {
        $booking = DB::table($this->getTable())->where('ID', $booking_id)->get()->first();
        return (!empty($booking) && is_object($booking)) ? $booking : null;
    }

    public function getBookingByToken($token_id)
    {
        $booking = DB::table($this->getTable())->where('token_code', $token_id)->get()->first();
        return (!empty($booking) && is_object($booking)) ? $booking : null;
    }

    public function deleteBooking($booking_id)
    {
        return DB::table($this->table)->where('ID', $booking_id)->delete();
    }

    public function updateBooking($data, $booking_id)
    {
        return DB::table($this->getTable())->where('ID', $booking_id)->update($data);
    }

    public function createBooking($data = [])
    {
        return DB::table($this->getTable())->insertGetId($data);
    }
}
