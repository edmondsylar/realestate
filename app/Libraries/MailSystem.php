<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 12/13/2019
 * Time: 10:05 AM
 */

class MailSystem
{
    public function __construct()
    {
        add_action('hh_after_created_booking', [$this, '_sendConfirmationEmail']);
        add_action('hh_confirmed_booking', [$this, '_sendBookingDetail']);
        add_action('hh_change_booking_status', [$this, '_sendBookingDetailWhenChangeStatus'], 10, 3);
        //add_action('init', [$this, 'test']);
    }

    public function test()
    {
        $this->_sendBookingDetail(21);
    }

    public function _sendConfirmationEmail($booking_id)
    {
        reset_booking_data();
        $booking = get_booking($booking_id);
        if ($booking) {
            $customer_email = $booking->email;
            $subject = sprintf('[%s] Booking Confirmation', get_option('site_name'));
            $content = view('frontend.email.confirmation', ['booking' => $booking])->render();

            $from = get_option('email_from_address');
            $from_name = get_option('email_from');
            send_mail($from, $from_name, $customer_email, $subject, $content);
        }
        return false;
    }

    public function _sendBookingDetailWhenChangeStatus($status, $booking_id, $created_booking)
    {
        if (!$created_booking) {
            $this->_sendBookingDetail($booking_id);
        }
    }

    public function _sendBookingDetail($booking_id)
    {
        reset_booking_data();
        $booking = get_booking($booking_id);
        if ($booking) {
            $from = get_option('email_from_address');
            $from_name = get_option('email_from');

            $customer_email = $booking->email;
            $service_data = get_booking_data($booking->ID, 'serviceObject');
            $subject = sprintf('[%s] Your booking (%s) at %s', get_option('site_name'), $booking->ID, $service_data->post_title);
            $content = view('frontend.email.booking-detail', ['booking' => $booking, 'for' => 'customer'])->render();
            send_mail($from, $from_name, $customer_email, $subject, $content);

            $partner_data = get_user_by_id($booking->owner);
            $partner_email = $partner_data->email;
            $subject = sprintf('[%s] %s has booked your service - Booking ID: %s', get_option('site_name'), $booking->first_name . ' ' . $booking->last_name, $booking->ID);
            $content = view('frontend.email.booking-detail', ['booking' => $booking, 'for' => 'partner'])->render();
            send_mail($from, $from_name, $partner_email, $subject, $content);

            $admin_data = get_admin_user();
            $admin_email = $admin_data->email;
            $subject = sprintf('[%s] There is a new booking on your system - Booking ID: %s', get_option('site_name'), $booking->ID);
            $content = view('frontend.email.booking-detail', ['booking' => $booking, 'for' => 'admin'])->render();
            send_mail($from, $from_name, $admin_email, $subject, $content);

        }
        return false;

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