<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use Sentinel;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Illuminate\Support\Facades\Input;
use Redirect;

class AuthController extends Controller
{
    public function subscribeEmail()
    {
        $email = Input::get('email');
        $res = \MailChimpSubscribe::get_inst()->addNewSubscriber($email);
        $this->sendJson($res, true);
    }

    public function _getSignUp()
    {
        return view('dashboard.sign-up', ['bodyClass' => 'authentication-bg authentication-bg-pattern']);
    }

    public function _postSignUp(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'email' => 'required',
                'password' => 'required|min:6',
                'term_condition' => 'required'
            ],
            [
                'email.required' =>  __('The email is required'),
                'password.required' => __('The password is required'),
                'password.min' => __('The password has at least 6 characters'),
                'term_condition.required' => __('Please agree with the Term and Condition')
            ]
        );
        if ($validator->fails()) {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'danger', 'message' => $validator->errors()->first()])->render()
            ]);
        }

        $credentials = [
            'email' => Input::get('email'),
            'password' => Input::get('password'),
            'first_name' => Input::get('first_name', ''),
            'last_name' => Input::get('last_name', ''),
        ];
        $user = Sentinel::findByCredentials($credentials);
        if ($user) {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'danger', 'message' => __('This user already exists')])->render()
            ]);
        }
        try {
            $user = Sentinel::registerAndActivate($credentials);

            $user_model = new \App\User();
            $role = $user_model->getRoleByName('customer');
            $user_model->updateUserRole($user->getUserId(), $role->id);

        } catch (Exception $e) {
            return $this->sendJson([
                'status' => 0,
                 'message' => view('common.alert', ['type' => 'danger', 'message' => $e->getMessage()])->render()
            ]);
        }
        if (!$user) {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type'
                => 'danger', 'message' => 'Can not create new user'])->render()
            ]);
        } else {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'success', 'message' => __('Registered successfully')])->render()
            ]);
        }
    }

    public function _getResetPassword()
    {
        return view('dashboard.reset-password', ['bodyClass' => 'authentication-bg authentication-bg-pattern']);
    }

    public function _postResetPassword(Request $request)
    {

        $validator = Validator::make($request->all(),
            [
                'email' => 'required|exists:users,email',
            ],
            [
                'email.required' => 'The email is required',
                'email.exists' => 'The email does not exist',
            ]
        );

        if ($validator->fails()) {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'danger', 'message' => $validator->errors()->first()])->render()
            ]);
        }

        $credentials = [
            'login' => Input::get('email'),
        ];

        $user = Sentinel::findByCredentials($credentials);
        if (is_null($user)) {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'danger', 'message' => __('The email does not exist')])->render()
            ]);
        } else {
            $password = createPassword(32);
            $credentials = [
                'password' => $password,
            ];

            $user = Sentinel::update($user, $credentials);

            if (!$user) {
                return $this->sendJson([
                    'status' => 0,
                    'message' => view('common.alert', ['type' => 'danger', 'message' => __('Can not reset password for this account. Try again!')])->render()
                ]);
            } else {
                $subject = sprintf('[%s] You have changed the password', get_option('site_name'));
                $content = view('frontend.email.reset-password', ['user' => $user, 'password' => $password])->render();
                send_mail('', '', $user->email, $subject, $content);
                return $this->sendJson([
                    'status' => 0,
                    'message' => view('common.alert', ['type' => 'success', 'message' => __('Successfully! Please check your email for a new password.')])->render(),
                    'redirect' => auth_url('login')
                ]);
            }
        }
    }

    public function _getLogin()
    {
        return view('dashboard.login', ['bodyClass' => 'authentication-bg authentication-bg-pattern']);
    }

    public function _postLogin(Request $request)
    {
        $input = Input::only('email', 'password');
        $redirect = get_referer(url('dashboard'));
        $validator = Validator::make($request->all(),
            [
                'email' => 'required|exists:users,email',
                'password' => 'required|min:6'
            ],
            [
                'email.required' => __('The email is required'),
                'email.exists' => __('The email does not exist'),
                'password.required' => __('The password is required'),
                'password.min' => __('The password has at least 6 characters')
            ]
        );
        if ($validator->fails()) {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'danger', 'message' => $validator->errors()->first()])->render()
            ]);
        }
        try {

            Sentinel::authenticate($input, Input::has('remember'));

        } catch (NotActivatedException $e) {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'danger', 'message' => __('User is not activated')])->render()
            ]);

        } catch (ThrottlingException $e) {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'danger', 'message' => __('Your account has been suspended due to 5 failed attempts. Try again after 15 minutes.')])->render()
            ]);

        }
        if (Sentinel::check()) {
            return $this->sendJson([
                'status' => 1,
                'message' => view('common.alert', ['type' => 'success', 'message' => __('Logged in successfully. Redirecting ...')])->render(),
                'redirect' => $redirect
            ]);
        } else {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'danger', 'message' => __('The email or password is incorrect')])->render()
            ]);
        }
    }

    public function _postLogout(Request $request)
    {
        $redirect_url = Input::get('redirect_url');

        Sentinel::logout();

        if (empty($redirect_url)) {
            $redirect_url = url('auth/login');
        }
        return $this->sendJson([
            'status' => 1,
            'title' => 'System Alert',
            'message' => __('Successfully Logged out'),
            'redirect' => $redirect_url
        ]);
    }

}
