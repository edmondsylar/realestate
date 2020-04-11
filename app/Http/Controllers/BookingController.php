<?php

namespace App\Http\Controllers;

use App\HomeAvailability;
use Illuminate\Http\Request;
use App\HomeBooking;
use Illuminate\Support\Facades\Input;
use Sentinel;

class BookingController extends Controller
{
    public function createBooking()
    {
        $cart = \CartHome::get_inst()->getCart();
        $paymentMethod = Input::get('payment');
        $payment = get_available_payments($paymentMethod);
        $serviceObject = unserialize($cart['serviceObject']);

        $user_data = [
            'email' => Input::get('email'),
            'firstName' => Input::get('firstName'),
            'lastName' => Input::get('lastName'),
            'phone' => Input::get('phone'),
            'address' => Input::get('address'),
            'city' => Input::get('city'),
            'postCode' => Input::get('postCode'),
        ];

        $cart['user_data'] = $user_data;
        $total_minutes = 1440;
        if ($serviceObject->booking_type == 'per_hour') {
            $total_minutes = hh_date_diff($cart['cartData']['startTime'], $cart['cartData']['endTime'], 'minute');
        }
        $data = [
            'booking_id' => $cart['serviceID'] . time(),
            'booking_description' => $serviceObject->post_title,
            'service_id' => $cart['serviceID'],
            'first_name' => Input::get('firstName', ''),
            'last_name' => Input::get('lastName', ''),
            'email' => Input::get('email', ''),
            'phone' => Input::get('phone', ''),
            'address' => Input::get('address', ''),
            'note' => Input::get('note', ''),
            'number_of_guest' => $cart['cartData']['numberGuest'],
            'total' => $cart['amount'],
            'token_code' => hh_encrypt($cart['serviceID']),
            'currency' => serialize(\Currencies::get_inst()->currentCurrency()),
            'buyer' => get_current_user_id(),
            'owner' => $serviceObject->author,
            'payment_type' => $payment::$paymentId,
            'start_date' => strtotime(date('Y-m-d', $cart['cartData']['startDate'])),
            'start_time' => $cart['cartData']['startTime'],
            'end_date' => strtotime(date('Y-m-d', $cart['cartData']['endDate'])),
            'end_time' => $cart['cartData']['endTime'],
            'total_minutes' => $total_minutes,
            'status' => 'pending',
            'checkout_data' => base64_encode(serialize($cart)),
            'created_date' => time()
        ];
        $booking_model = new HomeBooking();

        $new_booking_id = $booking_model->createBooking($data);


        return $new_booking_id;
    }


    public function getProjection($start_date, $end_date, $user_id = '')
    {
        $booking_model = new HomeBooking();
        return $booking_model->getProjection($start_date, $end_date, $user_id);
    }

    public function getBookingByID($booking_id)
    {
        global $booking, $old_booking;
        $booking_model = new HomeBooking();
        if (!is_null($booking)) {

            if (isset($booking->ID) && $booking->ID == $booking_id) {
                return $booking;
            } else {
                $old_booking = $booking;
                $booking = $booking_model->getBooking($booking_id);
            }
        } else {
            $booking = $booking_model->getBooking($booking_id);
        }

        return $booking;
    }

    public function updateBookingStatus($booking_id, $status, $created_booking = false)
    {
        $booking_model = new HomeBooking();
        $booking_model->updateBooking(['status' => $status], $booking_id);

        $has = $booking_model->getBooking($booking_id);

        $avai = new HomeAvailability();
        $has_avai = $avai->getItemByBooking($booking_id);
        if (in_array($status, ['canceled', 'pending'])) {
            if ($has_avai) {
                $avai->deleteAvailabilityByBooking($booking_id);
            }
        } else {
            if (!$has_avai) {
                $avai->createAvailability([
                    'home_id' => $has->service_id,
                    'start_time' => $has->start_time,
                    'start_date' => $has->start_date,
                    'end_time' => $has->end_time,
                    'end_date' => $has->end_date,
                    'booking_id' => $booking_id,
                    'total_minutes' => $has->total_minutes
                ]);
            }
        }
        EarningController::get_inst()->updateEarning($booking_id);

        do_action('hh_change_booking_status', $status, $booking_id, $created_booking);
    }

    public function deleteBooking($booking_id)
    {
        $booking_model = new HomeBooking();
        return $booking_model->deleteBooking($booking_id);
    }

    public function allBookings($data = [])
    {
        $booking_model = new HomeBooking();

        return $booking_model->allBookings($data);
    }

    public function _allBooking(Request $request, $page = 1)
    {
        $folder = $this->getFolder();

        $search = Input::get('_s');
        $orderBy = Input::get('orderby', 'ID');
        $order = Input::get('order', 'desc');
        $status = Input::get('status', '');

        $data = [
            'search' => $search,
            'orderby' => $orderBy,
            'order' => $order,
            'status' => $status,
            'page' => $page
        ];
        if (is_partner()) {
            $data['user_type'] = 'owner';
            $data['user_id'] = get_current_user_id();
        }
        if (is_customer()) {
            $data['user_type'] = 'buyer';
            $data['user_id'] = get_current_user_id();
        }

        $allBooking = $this->allBookings($data);
        return view("dashboard.screens.{$folder}.all-booking", ['role' => $folder, 'bodyClass' => 'hh-dashboard', 'allBooking' => $allBooking]);
    }

    public function _changeBookingStatus(Request $request)
    {
        $booking_id = Input::get('bookingID');
        $booking_encrypt = Input::get('bookingEncrypt');
        $status = Input::get('status', 'incomplete');

        $booking = get_booking($booking_id);
        if (hh_compare_encrypt($booking_id, $booking_encrypt) || is_null($booking)) {
            $this->updateBookingStatus($booking_id, $status);
            $this->sendJson([
                'status' => 1,
                'title' => __('System Alert'),
                'message' => __('This booking is changed'),
                'reload' => true
            ], true);
        } else {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This booking is not available')
            ], true);
        }
    }

    public function _getBookingInvoice(Request $request)
    {
        $booking_id = Input::get('bookingID');
        $booking_encrypt = Input::get('bookingEncrypt');

        $booking = get_booking($booking_id);
        if (hh_compare_encrypt($booking_id, $booking_encrypt) || is_null($booking)) {
            $html = view('dashboard.components.invoice', ['bookingID' => $booking_id])->render();
            $this->sendJson([
                'status' => 1,
                'html' => $html,
                'message' => __('Get the invoice successfully')
            ], true);
        } else {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This booking is not available')
            ], true);
        }
    }

    public function _bookingConfirmation(Request $request)
    {
        $token = Input::get('token');
        $code = Input::get('code');
        $booking_model = new HomeBooking();
        $booking = $booking_model->getBookingByToken($token);

        $status = 0;
        if ($booking) {
            $encrypt = create_confirmation_code($booking);
            if ($encrypt === $code) {
                if ($booking->confirm == 'confirmed') {
                    $status = 2;
                } else {
                    $status = 1;
                    $booking_model->updateBooking(['confirm' => 'confirmed'], $booking->ID);
                    do_action('hh_confirmed_booking', $booking->ID);
                }
            }
        }

        return view('frontend.confirmation-detail', ['status' => $status]);

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
