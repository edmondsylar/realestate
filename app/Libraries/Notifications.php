<?php

use App\Notification;

class Notifications
{
    public function __construct()
    {
        add_action('hh_after_created_booking', [$this, '_addNotiWhenCreatedBooking'], 10, 1);
    }

    public function getLatestNotificationByUser($user_id, $type = 'to')
    {
        $noti_model = new Notification();
        return $noti_model->getLatestNotificationByUser($user_id, $type);
    }

    public function _addNotiWhenCreatedBooking($booking_id)
    {
        $booking = get_booking($booking_id);
        $serviceObject = get_booking_data($booking_id, 'serviceObject');
        $admin = get_admin_user();

        $this->addNotification(0, $admin->getUserId(),
            sprintf('A new booking <a href="%s">#%s</a>', dashboard_url('all-booking/?_s=' . $booking_id), $booking->booking_id),
            sprintf('at <strong>%s</strong> from %s to %s', $serviceObject->post_title, date(hh_date_format(), $booking->start_date), date(hh_date_format(), $booking->end_date)),
            'booking');

        $partner = get_user_by_id($booking->owner);
        $this->addNotification(0, $partner->getUserId(),
            sprintf('A new booking <a href="%s">#%s</a>', dashboard_url('all-booking/?_s=' . $booking_id), $booking->booking_id),
            sprintf('at <strong>%s</strong> from %s to %s', $serviceObject->post_title, date(hh_date_format(), $booking->start_date), date(hh_date_format(), $booking->end_date)),
            'booking');

        $customer = get_user_by_id($booking->buyer);
        $this->addNotification(0, $customer->getUserId(),
            sprintf('Your booking <a href="%s">#%s</a>', dashboard_url('all-booking/?_s=' . $booking_id), $booking->booking_id),
            sprintf('at <strong>%s</strong> from %s to %s', $serviceObject->post_title, date(hh_date_format(), $booking->start_date), date(hh_date_format(), $booking->end_date)),
            'booking');
    }

    public function addNotification($from, $to, $title = '', $message = '', $type = 'global')
    {
        $noti_model = new Notification();
        $data = [
            'user_from' => $from,
            'user_to' => $to,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'created_at' => time()
        ];

        return $noti_model->insertNotification($data);
    }

    public function deleteNotification($noti_id)
    {
        $noti_model = new Notification();
        return $noti_model->deleteNotification($noti_id);
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
