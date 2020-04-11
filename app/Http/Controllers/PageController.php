<?php

namespace App\Http\Controllers;

use App\Page;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use App\Providers\HHelper;
use Sentinel;
use Illuminate\Support\Facades\Input;
use Str;


class PageController extends Controller
{
    public function __construct()
    {
        add_action('hh_dashboard_breadcrumb', [$this, '_addCreatePageButton']);
    }

    public function viewPage($post_slug){
    	$pageModel = new Page();
    	$page = $pageModel->getBySlug($post_slug, 'publish');
    	if($page == null){
            return redirect('/');
        }
	    return view("frontend.page.default", ['page' => $page]);
    }

    public static function getById($home_id)
    {
        $page_object = new Page();

        global $post, $old_post;

        if (!is_null($post)) {
            if (isset($post->post_id) && $post->post_id == $home_id) {
                return $post;
            } else {
                $old_post = $post;
                $post = $page_object->getById($home_id);
            }
        } else {
            $post = $page_object->getById($home_id);
        }

        return $post;
    }

    public static function getByName($home_name)
    {
        $page_object = new Page();

        global $post, $old_post;

        if (!is_null($post)) {
            if (isset($post->post_slug) && $post->post_slug == $home_name) {
                return $post;
            } else {
                $old_post = $post;
                $post = $page_object->getByName($home_name);
            }
        } else {
            $post = $page_object->getByName($home_name);
        }
        return $post;
    }

    public function _deletePageAction(Request $request)
    {
        $post_id = Input::get('pageID');
        $page_encrypt = Input::get('pageEncrypt');
        $delete_type = Input::get('type');

        if (!hh_compare_encrypt($post_id, $page_encrypt)) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This page is invalid')
            ], true);
        }
        $page = new Page();
        $pageObject = $page->getById($post_id);

        if (!empty($pageObject) && is_object($pageObject)) {
            $deleted = $page->deletePage($post_id);

            if ($deleted) {
                $res = [
                    'status' => 1,
                    'title' => __('System Alert'),
                    'message' => __('This Page is deleted')
                ];
                if ($delete_type == 'in-page') {
                    $res['redirect'] = dashboard_url('all-page');
                } else {
                    $res['reload'] = true;
                }
                $this->sendJson($res, true);
            } else {
                $this->sendJson([
                    'status' => 0,
                    'title' => __('System Alert'),
                    'message' => __('Can not delete this page')
                ], true);
            }
        } else {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This Page is invalid')
            ], true);
        }
    }

    public function _editPageAction()
    {
        $post_id = Input::get('postID');
        $page_encrypt = Input::get('postEncrypt');

        if (!hh_compare_encrypt($post_id, $page_encrypt)) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This page is invalid')
            ], true);
        }
        $page = new Page();
        $pageObject = $page->getById($post_id);

        if (!empty($pageObject) && is_object($pageObject)) {
            $post_title = set_translate('post_title');
            $post_content = set_translate('post_content');
            $page_template = Input::get('page_template', 'default');
            $thumbnail_id = set_translate('thumbnail_id');
            $post_status = Input::get('post_status');
            $post_slug = Input::get('post_slug');

            if (empty($post_slug)) {
                $post_slug = $pageObject->post_slug;
            }

            if ($post_title) {
                $data = [
                    'post_title' => $post_title,
                    'post_content' => $post_content,
                    'thumbnail_id' => $thumbnail_id,
                    'page_template' => $page_template,
                    'status' => $post_status
                ];

                $post_slug_convert = Str::slug($post_slug, '-');
                if ($post_slug_convert != $pageObject->post_slug) {
                    $data['post_slug'] = render_service_slug($post_title, $post_slug);
                }

                $updated = $page->updatePage($data, $post_id);

                if (!is_null($updated)) {
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
                        'message' => __('Can not update this page')
                    ], true);
                }
            } else {
                $this->sendJson([
                    'status' => 0,
                    'title' => __('System Alert'),
                    'message' => __('Some fields is incorrect')
                ], true);
            }

        } else {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This page is invalid')
            ], true);
        }
    }

    public function _editPage($post_id = '')
    {
        $folder = $this->getFolder();
        if (!empty($post_id)) {
            $page = new Page();
            $currentPage = $page->getById($post_id);
            if (!empty($currentPage)) {
                return view("dashboard.screens.{$folder}.page-add-new", ['bodyClass' => 'hh-dashboard', 'post_id' => $post_id, 'current_page' => $currentPage]);
            }
        }
        return view("dashboard.components.not-found", ['bodyClass' => 'hh-dashboard']);
    }

    public function _addNewPageAction(Request $request)
    {
        $post_title = set_translate('post_title');
        $post_content = set_translate('post_content');
        $page_template = Input::get('page_template', 'default');
        $thumbnail_id = set_translate('thumbnail_id');
        $post_status = Input::get('post_status');
        $post_slug = Input::get('post_slug');

        if ($post_title) {
            $data = [
                'post_title' => $post_title,
                'post_content' => $post_content,
                'thumbnail_id' => $thumbnail_id,
                'page_template' => $page_template,
                'post_slug' => render_service_slug($post_title, $post_slug),
                'status' => $post_status,
                'created_at' => time()
            ];

            $page = new Page();

            $newPage = $page->createPage($data);
            if ($newPage) {
                $this->sendJson([
                    'status' => 1,
                    'title' => __('System Alert'),
                    'message' => __('Created Successfully'),
                    'redirect' => dashboard_url('edit-page/' . $newPage)
                ], true);
            } else {
                $this->sendJson([
                    'status' => 0,
                    'title' => __('System Alert'),
                    'message' => __('Can not create this page')
                ], true);
            }
        } else {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('Some fields is incorrect')
            ], true);
        }
    }

    public function _addCreatePageButton()
    {
        $screen = current_screen();
        if ($screen == 'all-page') {
            echo view('dashboard.components.add-page')->render();
        }
    }

    public function _allPage($page = 1)
    {
        $folder = $this->getFolder();
        $search = \Illuminate\Support\Facades\Input::get('_s');
        $orderBy = \Illuminate\Support\Facades\Input::get('orderby', 'post_id');
        $order = \Illuminate\Support\Facades\Input::get('order', 'desc');
        $status = Input::get('status', '');

        $pageModel = new Page();

        $allPages = $pageModel->getAllPages(
            [
                'search' => $search,
                'page' => $page,
                'orderby' => $orderBy,
                'order' => $order,
                'status' => $status
            ]
        );
        return view("dashboard.screens.{$folder}.page", ['bodyClass' => 'hh-dashboard', 'allPages' => $allPages]);
    }

    public function _addNewPage()
    {
        $folder = $this->getFolder();
        return view("dashboard.screens.{$folder}.page-add-new", ['bodyClass' => 'hh-dashboard', 'post_id' => '-1']);
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
