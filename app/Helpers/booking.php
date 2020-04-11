<?php

use App\Http\Controllers\BookingController;

function create_confirmation_code($booking)
{
    $encrypt = hh_encrypt($booking->token_code . $booking->created_date . $booking->buyer);

    return $encrypt;
}

function get_booking_data($booking_id, $column = '', $type = 'home')
{
    $booking = get_booking($booking_id, $type);
    $data = unserialize(base64_decode($booking->checkout_data));
    if (!empty($column)) {
        return isset($data[$column]) ? maybe_unserialize($data[$column]) : null;
    }
    return $data;
}

function get_booking($booking_id, $type = 'home')
{
    $booking = null;
    switch ($type) {
        case 'home':
        default:
            $booking = BookingController::get_inst()->getBookingByID($booking_id);
            break;
    }

    return $booking;
}

function reset_booking_data()
{
    global $booking, $old_booking;

    $booking = $old_booking;
}
