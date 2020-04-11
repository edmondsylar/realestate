<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Sentinel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        add_action('hh_dashboard_breadcrumb', [$this, '_addCreateUserButton']);
    }

    public function _updateUserItem(Request $request)
    {
        $user_id = Input::get('userID');
        $user_encrypt = Input::get('userEncrypt');
        if (!hh_compare_encrypt($user_id, $user_encrypt)) {
            return $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This user does not exist')
            ]);
        }
        $user = get_user_by_id($user_id);
        if (!$user) {
            return $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This user does not exist')
            ]);
        }
        $first_name = Input::get('user_first_name');
        $last_name = Input::get('user_last_name');
        $role_id = Input::get('user_role', 3);

        $password = trim(Input::get('user_password'));

        $validation = [
            'user_role' => 'required|numeric',
        ];

        if (!empty($password)) {
            $validation['user_password'] = 'required|min:6';
        }

        $validator = Validator::make($request->all(), $validation);

        if ($validator->fails()) {
            return $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => $validator->errors()->first()
            ]);
        }
        if (!empty($password)) {

            $credentials = [
                'password' => $password,
            ];
            $user = Sentinel::update($user, $credentials);
        }

        if ($user) {
            $user_model = new User();
            $data = [
                'first_name' => $first_name,
                'last_name' => $last_name
            ];
            $user_model->updateUser($user->getUserId(), $data);

            $update = $user_model->updateUserRole($user_id, $role_id);

            if ($update) {
                return $this->sendJson([
                    'status' => 1,
                    'title' => __('System Alert'),
                    'message' => __('Successfully updated'),
                    'reload' => true
                ]);
            }
        }

        return $this->sendJson([
            'status' => 0,
            'title' => __('System Alert'),
            'message' => __('Has error when updating')
        ]);
    }

    public function _getUserItem(Request $request)
    {
        $user_id = Input::get('userID');
        $user_encrypt = Input::get('userEncrypt');
        if (!hh_compare_encrypt($user_id, $user_encrypt)) {
            return $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This user does not exist')
            ]);
        }
        $user = get_user_by_id($user_id);
        if (!$user) {
            return $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This user does not exist')
            ]);
        }
        return $this->sendJson([
            'status' => 1,
            'html' => view('dashboard.components.user-edit-form', ['user' => $user])->render()
        ]);
    }

    public function _deleteUser(Request $request)
    {
        $userID = Input::get('userID');
        $userEncrypt = Input::get('userEncrypt');

        if (!hh_compare_encrypt($userID, $userEncrypt)) {
            return $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This user does not exist')
            ]);
        }
        $user = Sentinel::findById($userID);
        if (!$user) {
            return $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' =>__( 'This user does not exist')
            ]);
        }

        $user->delete();

        return $this->sendJson([
            'status' => 1,
            'title' => __('System Alert'),
            'message' => __('This member has been deleted'),
            'reload' => true
        ]);
    }

    public function _addNewUser(Request $request)
    {
        $email = Input::get('user_email');
        $first_name = Input::get('user_first_name');
        $last_name = Input::get('user_last_name');
        $role = Input::get('user_role', 3);
        $password = Input::get('user_password');

        $validator = Validator::make($request->all(), [
            'user_email' => 'required|email',
            'user_role' => 'required|numeric',
            'user_password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => $validator->errors()->first()
            ]);
        }

        $user_exist = $user = Sentinel::findByCredentials([
            'login' => $email
        ]);

        if ($user_exist) {
            return $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This email has been registered')
            ]);
        }
        $user_model = new User();
        $role = Sentinel::findRoleById($role);
        $user_data = [
            'email' => $email,
            'password' => $password,
        ];

        $user = Sentinel::registerAndActivate($user_data);
        $user->roles()->attach($role);

        $data = [
            'last_check_notification' => time(),
            'first_name' => $first_name,
            'last_name' => $last_name
        ];
        $user_model->updateUser($user->getUserId(), $data);

        return $this->sendJson([
            'status' => 1,
            'title' => __('System Alert'),
            'message' => __('Successfully created new user'),
            'reload' => true
        ]);
    }

    public function _addCreateUserButton()
    {
        $screen = current_screen();
        if ($screen == 'user-management') {
            echo view('dashboard.components.quick-add-user')->render();
        }
    }

    public function _userManagement(Request $request, $page = 1)
    {
        $user_model = new User();
        $data = [
            'search' => Input::get('_s'),
            'page' => $page,
            'orderby' => Input::get('orderby', 'id'),
            'order' => Input::get('order', 'desc'),
            'role' => Input::get('role', ''),
            'tax' => 'post-tag'
        ];
        $allUsers = $user_model->allUsers($data);
        $folder = $this->getFolder();
        return view("dashboard.screens.{$folder}.user-management", ['role' => $folder, 'bodyClass' => 'hh-dashboard', 'allUsers' => $allUsers]);
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
}
