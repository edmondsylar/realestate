<?php

namespace App\Http\Controllers;

use App\HomeBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function _checkoutAction(Request $request)
    {
        $paymentMethod = Input::get('payment');

        if (!is_user_logged_in()) {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'danger', 'message' => __('You need to login before making payments')])->render(),
                'need_login' => true
            ]);
        }
        $validator = Validator::make($request->all(),
            [
                'firstName' => 'required',
                'lastName' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'address' => 'required',
                'payment' => 'required',
                'term_condition' => 'required'
            ],
            [
                'firstName.required' => __('First name is required'),
                'lastName.required' => __('Last name is required'),
                'email.required' => __('Email is required'),
                'email.email' => __('Email is invalid'),
                'phone.required' => __('Phone is required'),
                'address.required' => __('Address is required'),
                'payment.required' => __('Payment gateway is required'),
                'term_condition.required' => __('Please agree with the Term and Condition')
            ]
        );
        if ($validator->fails()) {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'danger', 'message' => $validator->errors()->first()])->render()
            ]);
        }

        if (get_option('use_google_captcha', 'off') == 'on') {
            $recaptcha = new \ReCaptcha\ReCaptcha(get_option('google_captcha_secret_key'));
            $gRecaptchaResponse = Input::get('g-recaptcha-response');
            $resp = $recaptcha->verify($gRecaptchaResponse, \Illuminate\Support\Facades\Request::ip());
            if (!$resp->isSuccess()) {
                return $this->sendJson([
                    'status' => 0,
                    'message' => view('common.alert', ['type' => 'danger', 'message' => __('Your request was denied')])->render()
                ]);
            }
        }

        $this->saveUserCheckoutData();

        $payment = get_available_payments($paymentMethod);
        if (!$payment) {
            $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'danger', 'message' => __('This payment gateway is not available')])->render()
            ]);
        }

        $cart = \CartHome::get_inst()->getCart();
        if (!$cart) {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'danger', 'message' => __('Cart is empty')])->render()
            ]);
        }

        do_action('hh_before_create_booking');

        $new_booking_id = BookingController::get_inst()->createBooking();

        if (!$new_booking_id) {
            if (!$cart) {
                return $this->sendJson([
                    'status' => 0,
                    'message' => view('common.alert', ['type' => 'danger', 'message' => __('Can not create new booking. Please try again!')])->render()
                ]);
            }
        }

        do_action('hh_after_create_booking', $new_booking_id);

        if (method_exists($payment, 'purchase')) {
            $paymentObject = new $payment;
            $responsive = $paymentObject->purchase($new_booking_id);
            if ($responsive['status'] == 'pending') {
                BookingController::get_inst()->deleteBooking($new_booking_id);
                return $this->sendJson([
                    'status' => 0,
                    'message' => view('common.alert', ['type' => 'danger', 'message' => $responsive['message']])->render()
                ]);
            } else {
                \CartHome::get_inst()->deleteCart();

                BookingController::get_inst()->updateBookingStatus($new_booking_id, $responsive['status'], true);

                do_action('hh_after_created_booking', $new_booking_id);

                return $this->sendJson([
                    'status' => 1,
                    'message' => view('common.alert', ['type' => 'success', 'message' => $responsive['message']])->render(),
                    'redirect' => $responsive['redirectUrl']
                ]);
            }
        } else {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'danger', 'message' => __('This payment gateway is missing purchase() method')])->render()
            ]);
        }
    }

    public function completePurchase($request)
    {
        $orderID = Input::get('orderID');
        $order_encrypt = Input::get('orderEncrypt');
        $paymentMethod = Input::get('payment');
        $status = Input::get('status', '-1');

        if ($this->checkIsResponsive()) {
            if (hh_compare_encrypt($orderID, $order_encrypt)) {
                $orderObject = get_booking($orderID);
                $oldStatus = $orderObject->status;
                if ($oldStatus == 'incomplete') {
                    if ($status == 0) {
                        BookingController::get_inst()->updateBookingStatus($orderID, 'canceled');
                        do_action('hh_completed_booking', $orderObject);
                    } else {
                        $payment = get_available_payments($paymentMethod);
                        if ($payment && method_exists($payment, 'completePurchase')) {
                            $paymentObject = new $payment;
                            $responsive = $paymentObject->completePurchase($orderID);
                            do_action('hh_before_check_complete_booking', $orderObject);

                            if ($responsive['status'] == 'completed') {
                                BookingController::get_inst()->updateBookingStatus($orderID, 'completed');
                            } elseif ($responsive['status'] == 'canceled') {
                                BookingController::get_inst()->updateBookingStatus($orderID, 'canceled');
                            } elseif ($responsive['status'] == 'incomplete') {
                                BookingController::get_inst()->updateBookingStatus($orderID, 'incomplete');
                            }
                            if (!empty($responsive['message'])) {
                                Log::debug($responsive['message']);
                            }
                            do_action('hh_completed_booking', $orderObject);
                        }
                    }
                }
            }
        }
    }


    public function checkIsResponsive()
    {
        $orderID = Input::get('orderID');
        $paymentMethod = Input::get('payment');
        $status = Input::get('status', '-1');
        $token_code = Input::get('token_code');
        $hash = Input::get('hash');
        $newHash = md5($paymentMethod . '|' . $orderID . '|' . $status . '|' . $token_code);
        if ($newHash !== $hash) {
            return false;
        }
        return true;
    }

    public function saveUserCheckoutData()
    {
        $fields = [
            'email' => Input::get('email'),
            'firstName' => Input::get('firstName'),
            'lastName' => Input::get('lastName'),
            'phone' => Input::get('phone'),
            'address' => Input::get('address'),
            'city' => Input::get('city'),
            'postCode' => Input::get('postCode'),
        ];

        $fields = apply_filters('hh_user_checkout_data', $fields);

        update_user_meta(get_current_user_id(), 'user_checkout_information', $fields);
    }

    public function _checkoutPage(Request $request)
    {
        $cart = \CartHome::get_inst()->getCart();
        return view('frontend.checkout', ['cart' => $cart]);
    }

    public function _thankyouPage(Request $request)
    {
        $orderID = Input::get('orderID');
        $isResponsive = $this->checkIsResponsive();
        if (!$isResponsive) {
            return redirect(url('/'));
        }
        $this->completePurchase($request);
        reset_booking_data();
        $bookingObject = BookingController::get_inst()->getBookingByID($orderID);
        return view('frontend.thank-you', ['bookingObject' => $bookingObject]);
    }
}
