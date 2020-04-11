<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class HomePageController extends Controller
{

    public function _404page(Request $request){
        return view('frontend.404');
    }
    public function _contactUsPost(Request $request)
    {
        $title = esc_html(Input::get('title'));
        $email = esc_html(Input::get('email'));
        $message = balanceTags(Input::get('message'));

        if (!is_email($email)) {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'danger', 'message' => __('The email is incorrect')])->render()
            ]);
        }
        if (empty($title)) {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'danger', 'message' => __('Please enter the title')])->render()
            ]);
        }
        if(get_option('use_google_captcha', 'off') == 'on') {
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

        $admin_data = get_admin_user();

        $subject = sprintf(__('[%s] Has new message from %s'), get_option('site_name'), $email);

        send_mail($email, $title, $admin_data->email, $subject, $message);

        return $this->sendJson([
            'status' => 1,
            'message' => view('common.alert', ['type' => 'success', 'message' => __('Successfully sent')])->render()
        ]);
    }

    public function _contactPage()
    {
        return view('frontend.contact-us');
    }

    public function index()
    {
        return view('frontend.homepage.default');
    }
}
