<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Home;
use Illuminate\Http\Request;
use Sentinel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    private function __updateHomeRating($post_id)
    {
        $comments = get_comment_list($post_id, [
            'number' => -1,
            'type' => 'home',
        ]);
        if ($comments['count'] > 0) {
            $ratings = [];
            foreach ($comments['results'] as $item) {
                array_push($ratings, $item->comment_rate);
            }
            $ratings = array_filter($ratings);
            if (count($ratings)) {
                $average = array_sum($ratings) / count($ratings);
                $average = round($average, 1);
                $home = new Home();
                $home->updateHome(['rating' => $average], $post_id);
            }
        }
    }

    public function _changeReviewStatusAction()
    {
        $review_id = Input::get('serviceID');
        $review_encrypt = Input::get('serviceEncrypt');
        $status = Input::get('status', '');

        if (!hh_compare_encrypt($review_id, $review_encrypt) || !$status) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('The data is invalid')
            ], true);
        }

        $review_model = new Comment();
        $updated = $review_model->updateStatus($review_id, $status);
        if (!is_null($updated)) {

            //Update review home
            $comment = new Comment();
            $commentObject = $comment->getById($review_id);
            if (!empty($commentObject)) {
                if ($commentObject->post_type == 'home') {
                    $home_id = $commentObject->post_id;
                    $this->__updateHomeRating($home_id);
                }
            }

            $this->sendJson([
                'status' => 1,
                'title' => __('System Alert'),
                'message' => __('Updated Successfully'),
                'reload' => true
            ], true);
        } else {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('Have error when saving')
            ], true);
        }
    }

    public function _deleteReviewAction(Request $request)
    {
        $review_id = Input::get('serviceID');
        $review_encrypt = Input::get('serviceEncrypt');

        if (!hh_compare_encrypt($review_id, $review_encrypt)) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This data is invalid')
            ], true);
        }

        $text = 'comment';
        $comment = new Comment();
        $commentObject = $comment->getById($review_id);

        if (!empty($commentObject) && is_object($commentObject)) {

            $deleted = $comment->deleteComment($review_id);

            //Update review home
            if ($commentObject->post_type == 'home') {
                $text = 'review';
                $home_id = $commentObject->post_id;
                $this->__updateHomeRating($home_id);
            }

            if ($deleted) {
                $this->sendJson([
                    'status' => 1,
                    'title' => __('System Alert'),
                    'message' => sprintf(__('This %s is deleted'), $text),
                    'reload' => true
                ], true);
            } else {
                $this->sendJson([
                    'status' => 0,
                    'title' => __('System Alert'),
                    'message' => sprintf(__('Can not delete this %s'), $text)
                ], true);
            }
        } else {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => sprintf(__('This %s is invalid'), $text)
            ], true);
        }
    }

    public function addCommentAction()
    {
        $post_id = Input::get('post_id');
        $comment_name = Input::get('comment_name');
        $comment_email = Input::get('comment_email');
        $comment_content = Input::get('comment_content');
        $comment_title = Input::get('comment_title');
        $comment_rate = Input::get('review_star', 5);
        $parent_id = Input::get('comment_id', 0);
        $comment_type = Input::get('comment_type', 'posts');
        $status = 'publish';

        if (!in_array($comment_type, ['posts', 'home'])) {
            $comment_type = 'posts';
        }

        if (get_option('use_google_captcha', 'off') == 'on') {
            $recaptcha = new \ReCaptcha\ReCaptcha(get_option('google_captcha_secret_key'));
            $gRecaptchaResponse = Input::get('g-recaptcha-response');
            $resp = $recaptcha->verify($gRecaptchaResponse, \Illuminate\Support\Facades\Request::ip());
            if (!$resp->isSuccess()) {
                return $this->sendJson([
                    'status' => 0,
                    'title' => __('System Alert'),
                    'message' => view('common.alert', ['type' => 'danger', 'message' => __('Your request was denied')])->render()
                ]);
            }
        }

        $text = 'comment';
        if ($comment_type == 'home') {
            if (!enable_review()) {
                $this->sendJson([
                    'status' => 0,
                    'title' => __('System Alert'),
                    'message' => '<div class="alert alert-warning">' . __('Review function was closed') . '</div>'
                ], true);
            }
            $text = 'review';
            if (need_approve_review() && !is_admin()) {
                $status = 'pending';
            }
        }

        if (is_user_logged_in()) {
            $user_data = get_current_user_data();
            $comment_email = $user_data->getUserLogin();
            $comment_name = get_username($user_data->getUserId());
        }

        if ($comment_name) {
            $data = [
                'post_id' => intval($post_id),
                'comment_name' => $comment_name,
                'comment_title' => $comment_title,
                'comment_content' => $comment_content,
                'comment_rate' => $comment_rate,
                'comment_email' => $comment_email,
                'comment_author' => get_current_user_id(),
                'post_type' => $comment_type,
                'parent' => $parent_id,
                'status' => $status,
                'created_at' => time()
            ];

            $comment = new Comment();

            $newPost = $comment->createPost($data);

            if ($newPost) {

                $this->__updateHomeRating($post_id, $comment_rate);

                $success_text = '<div class="alert alert-success">' . sprintf(__('Add new %s successfully'), $text) . '</div>';
                if ($comment_type == 'home' && need_approve_review() && !is_admin()) {
                    $success_text = '<div class="alert alert-success">' . sprintf(__('Add new %s successfully. Your review needs to be moderated before publishing'), $text) . '</div>';
                }

                $this->sendJson([
                    'status' => 1,
                    'title' => __('System Alert'),
                    'message' => $success_text,
                    'reload' => true
                ], true);
            } else {
                $this->sendJson([
                    'status' => 0,
                    'title' => __('System Alert'),
                    'message' => '<div class="alert alert-warning">' . sprintf(__('Can not add this %s'), $text) . '</div>'
                ], true);
            }
        } else {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => '<div class="alert alert-warning">' . __('Some fields is incorrect') . '</div>'
            ], true);
        }
    }
}