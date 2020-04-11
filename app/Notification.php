<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Notification extends Model
{
    protected $table = 'notification';
    protected $primaryKey = 'ID';

    public function allNotifications($data)
    {
        $default = [
            'page' => 1,
            'user_id' => get_current_user_id(),
            'user_type' => 'user_to',
            'number' => posts_per_page()
        ];

        $data = array_merge($default, $data);
        $number = intval($data['number']);

        $sql = DB::table($this->getTable())->selectRaw("SQL_CALC_FOUND_ROWS *");

        if ($data['user_id']) {
            $sql->where($data['user_type'], $data['user_id']);
        }

        $offset = ($data['page'] - 1) * $number;

        $sql->limit($number)->offset($offset);

        $sql->orderBy('ID', 'desc');

        $results = $sql->get();
        $count = DB::select("SELECT FOUND_ROWS() as `row_count`")[0]->row_count;

        return [
            'total' => $count,
            'results' => $results
        ];
    }

    public function getLatestNotificationByUser($user_id, $type = 'to')
    {
        $userdata = get_user_by_id($user_id);
        $last_time = $userdata->last_check_notification;
        if(!$last_time){
            $last_time = 0;
        }
        $number = posts_per_page();
        if ($type == 'to') {

            $results = DB::table($this->getTable())->selectRaw("SQL_CALC_FOUND_ROWS notification.*")->where('user_to', $user_id)->whereRaw("created_at >= {$last_time}")->limit($number)->orderBy('ID', 'desc')->get();
        } else {

            $results = DB::table($this->getTable())->selectRaw("SQL_CALC_FOUND_ROWS notification.*")->where('user_from', $user_id)->whereRaw("created_at >= {$last_time}")->limit($number)->orderBy('ID', 'desc')->get();
        }

        $count = DB::select("SELECT FOUND_ROWS() as `row_count`")[0]->row_count;

        return [
            'total' => $count,
            'results' => $results
        ];
    }

    public function updateLastCheckNoti($user_id, $data)
    {
        return DB::table('users')->where('id', $user_id)->update($data);
    }

    public function deleteNotification($noti_id)
    {
        return DB::table($this->getTable())->where('ID', $noti_id)->delete();
    }

    public function insertNotification($data = [])
    {
        return DB::table($this->getTable())->insertGetId($data);
    }
}
