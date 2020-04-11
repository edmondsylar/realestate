<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Sentinel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
	public function _updatePassword(Request $request){
		$user_id = $request->post('user_id');
		$user_encrypt = $request->post('user_encrypt');
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

		$validator = Validator::make($request->all(), [
			'password' => ['required', 'string', 'min:6', 'confirmed'],
		]);

		if($validator->fails()){
			return $this->sendJson([
				'status' => 0,
				'title' => __('System Alert'),
				'message' => $validator->errors()->first()
			]);
		}else{
			$password = trim($request->post('password'));
			$credentials = [
				'password' => $password,
			];
			$user_updated = Sentinel::update($user, $credentials);
			return $this->sendJson([
				'status' => 1,
				'title' => __('System Alert'),
				'message' => __('Update password successfully')
			]);
		}
	}

    public function _getFontIcon(Request $request)
    {
        global $text;
        $text = Input::get('text', '');
        $text = strtolower(trim($text));
        if (empty($text)) {
            $this->sendJson(
                [
                    'status' => 0,
                    'data' => __('Not found icons')
                ]
                , true);
        }
        include public_path('fonts/fonts.php');
        if (!isset($fonts)) {
            $this->sendJson([
                'status' => 0,
                'data' => __('Not found icons data')
            ], true);
        }
        $results = array_filter($fonts, function ($key) {
            global $text;
            if (strpos(strtolower($key), $text) === false) {
                return false;
            } else {
                return true;
            }
        }, ARRAY_FILTER_USE_KEY);
        if (empty($results)) {
            $this->sendJson([
                'status' => 0,
                'data' => __('Not found icons')
            ], true);
        } else {
            $this->sendJson([
                'status' => 1,
                'data' => $results
            ], true);
        }
    }

    public function _updateYourAvatar(Request $request)
    {
        $user_id = Input::get('user_id');
        $user_encrypt = Input::get('user_encrypt');
        $avatar = Input::get('avatar');
        if (hh_compare_encrypt($user_id, $user_encrypt) && $user_id == get_current_user_id()) {
            $user_model = new User();
            $updated = $user_model->updateUser($user_id, ['avatar' => $avatar]);
            if (!is_null($updated)) {
                return $this->sendJson([
                    'status' => 1,
                    'title' => __('System Alert'),
                    'message' => __('Updated successfully')
                ]);
            } else {
                return $this->sendJson([
                    'status' => 0,
                    'title' => __('System Alert'),
                    'message' => __('Can not update this user. Try again!')
                ]);
            }
        } else {
            return $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This user is invalid')
            ]);
        }
    }

    public function _updateYourProfile(Request $request)
    {
        $user_id = Input::get('user_id');
        $user_encrypt = Input::get('user_encrypt');
        $first_name = Input::get('first_name');
        $last_name = Input::get('last_name');
        $mobile = Input::get('mobile');
        $location = Input::get('location');
        $address = Input::get('address');
        $description = Input::get('description');

        if (hh_compare_encrypt($user_id, $user_encrypt) && $user_id == get_current_user_id()) {
            $args = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'mobile' => $mobile,
                'location' => $location,
                'address' => $address,
                'description' => $description,
            ];

            $user_model = new User();
            $updated = $user_model->updateUser($user_id, $args);
            if (!is_null($updated)) {
                return $this->sendJson([
                    'status' => 1,
                    'title' => __('System Alert'),
                    'message' => __('Updated successfully')
                ]);
            } else {
                return $this->sendJson([
                    'status' => 0,
                    'title' => __('System Alert'),
                    'message' => __('Can not update this user. Try again!')
                ]);
            }
        } else {
            return $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This user is invalid')
            ]);
        }
    }

    public function _getProfile()
    {
        $folder = $this->getFolder();
        return view("dashboard.screens.{$folder}.profile", ['role' => $folder, 'bodyClass' => 'hh-dashboard']);
    }

    public function index()
    {
        $folder = $this->getFolder();
        return view("dashboard.screens.{$folder}.dashboard", ['role' => $folder, 'bodyClass' => 'hh-dashboard']);
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
