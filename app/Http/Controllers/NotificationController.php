<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Sentinel;

class NotificationController extends Controller
{
    public function _updateLastcheckNoti(Request $request)
    {
        $user_id = Input::get('user_id');
        $user_encrypt = Input::get('user_encrypt');

        if (hh_compare_encrypt($user_id, $user_encrypt)) {
            $time = time();
            $user = get_user_by_id($user_id);
            if ($user) {
                $noti_model = new Notification();
                $noti_model->updateLastCheckNoti($user_id, ['last_check_notification' => $time]);
            }
        }

        return $this->sendJson([
            'status' => 1,
            'message' => 'Updated'
        ]);
    }

    public function _deleteNotification(Request $request)
    {
        $notiID = Input::get('notiID');
        $notiEncrypt = Input::get('notiEncrypt');

        if (hh_compare_encrypt($notiID, $notiEncrypt)) {
            $noti_model = new Notification();
            $deleted = $noti_model->deleteNotification($notiID);
            if ($deleted) {
                return $this->sendJson([
                    'status' => 1,
                    'title' => __('System Alert'),
                    'message' => __('Deleted successfully'),
                ]);
            }
        }
        return $this->sendJson([
            'status' => 0,
            'title' => __('System Alert'),
            'message' => __('Can not delete this notification')
        ]);
    }

    public function allNotifications($args)
    {
        $noti_model = new Notification();
        $allNotifications = $noti_model->allNotifications($args);

        return $allNotifications;
    }

    public function _allNotifications(Request $request, $page = 1)
    {
        $folder = $this->getFolder();

        $args = [
            'page' => $page
        ];

        $noti_model = new Notification();
        $allNotifications = $noti_model->allNotifications($args);
        return view("dashboard.screens.{$folder}.notifications", ['role' => $folder, 'bodyClass' => 'hh-dashboard', 'allNotifications' => $allNotifications]);
    }

    private function getFolder()
    {
        $folder = 'customer';
        if (Sentinel::inRole('administrator')) {
            $folder = 'administrator';
        } elseif (Sentinel::inRole('partner')) {
            $folder = 'partner';
        }

        return $folder;
    }

    public static function get_inst()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}
