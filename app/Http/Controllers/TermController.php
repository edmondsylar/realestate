<?php

namespace App\Http\Controllers;

use App\Term;
use App\Taxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Sentinel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;

class TermController extends Controller
{
    public function __construct()
    {
        add_action('hh_dashboard_breadcrumb', [$this, '_addCreateTermButton']);
    }

    public function _addCreateTermButton()
    {
        $screen = current_screen();
        if ($screen == 'home-amenity') {
            echo view('dashboard.components.quick-add-home-amenity')->render();
        } elseif ($screen == 'home-type') {
            echo view('dashboard.components.quick-add-home-type')->render();
        } elseif ($screen == 'post-category') {
            echo view('dashboard.components.quick-add-post-category')->render();
        } elseif ($screen == 'post-tag') {
            echo view('dashboard.components.quick-add-post-tag')->render();
        }
    }

    public function _addNewTerm(Request $request)
    {
        $termName = set_translate('term_name');
        $termDescription = set_translate('term_description');
        $termImage = Input::get('term_image');
        $termIcon = Input::get('term_icon');
        $taxonomyName = Input::get('taxonomy_name', '');

        if (empty(trim($termName))) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('the Term Name is required')
            ], true);
        }
        $taxonomy = new Taxonomy();
        $taxObject = $taxonomy->getByName($taxonomyName);

        if (!$taxObject) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('Taxonomy is not available')
            ], true);
        }

        $data = [
            'term_title' => $termName,
            'term_name' => Str::slug(get_translate($termName)),
            'term_description' => $termDescription,
            'term_image' => $termImage,
            'term_icon' => $termIcon,
            'taxonomy_id' => $taxObject->taxonomy_id,
            'created_at' => time(),
            'author' => get_current_user_id(),
        ];

        $term = new Term();

        $created = $term->createTerm($data);

        if ($created) {
            $this->sendJson([
                'status' => 1,
                'title' => __('System Alert'),
                'message' =>__( 'Created Successfully'),
                'reload' => true
            ], true);
        } else {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('Can not create new term')
            ], true);
        }
    }

    public function _deleteTermItem(Request $request)
    {
        $termID = Input::get('termID');
        $termEncrypt = Input::get('termEncrypt');

        if (!hh_compare_encrypt($termID, $termEncrypt)) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This term is invalid')
            ], true);
        }

        $term = new Term();
        $termObject = $term->getById($termID);
        if (is_null($termObject)) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' =>__( 'This term is invalid')
            ], true);
        }

        $deleted = $term->deleteTerm($termID);
        if ($deleted) {
            $this->sendJson([
                'status' => 1,
                'title' => __('System Alert'),
                'message' => __('Deleted successfully'),
                'reload' => true
            ], true);
        } else {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('Can not delete this term')
            ], true);
        }
    }

    public function _updateTermItem(Request $request)
    {
        $termID = Input::get('term_id');
        $termEncrypt = Input::get('term_encrypt');
        $termName = set_translate('term_name');
        $termDescription = set_translate('term_description');
        $termImage = Input::get('term_image');
        $termIcon = Input::get('term_icon');

        if (!hh_compare_encrypt($termID, $termEncrypt)) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This term is invalid')
            ], true);
        }

        $term = new Term();
        $termObject = $term->getById($termID);
        if (is_null($termObject)) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This term is invalid')
            ], true);
        }

        $data = [
            'term_title' => $termName,
            'term_name' => Str::slug(get_translate($termName)),
            'term_description' => $termDescription,
            'term_image' => $termImage,
            'term_icon' => $termIcon
        ];

        $termUpdated = $term->updateTerm($data, $termID);

        if (is_null($termUpdated)) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('Can not update this term')
            ], true);
        } else {
            $this->sendJson([
                'status' => 1,
                'title' => __('System Alert'),
                'message' => __('This term is updated')
            ], true);
        }
    }

    public function _getHomeTypeItem(Request $request)
    {
        $termID = Input::get('termID');
        $termEncrypt = Input::get('termEncrypt');

        if (!hh_compare_encrypt($termID, $termEncrypt)) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This term is invalid')
            ], true);
        }

        $term = new Term();
        $termObject = $term->getById($termID);
        if (is_null($termObject)) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This term is invalid')
            ], true);
        }

        $html = view('dashboard.components.home-type-form', ['termObject' => $termObject])->render();

        $this->sendJson([
            'status' => 1,
            'html' => $html
        ], true);

    }

    public function _getPostCategoryItem(Request $request)
    {
        $termID = Input::get('termID');
        $termEncrypt = Input::get('termEncrypt');

        if (!hh_compare_encrypt($termID, $termEncrypt)) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This category is invalid')
            ], true);
        }

        $term = new Term();
        $termObject = $term->getById($termID);
        if (is_null($termObject)) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' =>__( 'This category is invalid')
            ], true);
        }

        $html = view('dashboard.components.post-category-form', ['termObject' => $termObject])->render();

        $this->sendJson([
            'status' => 1,
            'html' => $html
        ], true);

    }

    public function _getPostTagItem(Request $request)
    {
        $termID = Input::get('termID');
        $termEncrypt = Input::get('termEncrypt');

        if (!hh_compare_encrypt($termID, $termEncrypt)) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This tag is invalid')
            ], true);
        }

        $term = new Term();
        $termObject = $term->getById($termID);
        if (is_null($termObject)) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This tag is invalid')
            ], true);
        }

        $html = view('dashboard.components.post-tag-form', ['termObject' => $termObject])->render();

        $this->sendJson([
            'status' => 1,
            'html' => $html
        ], true);

    }

    public function _getHomeAmenityItem(Request $request)
    {
        $termID = Input::get('termID');
        $termEncrypt = Input::get('termEncrypt');

        if (!hh_compare_encrypt($termID, $termEncrypt)) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This term is invalid')
            ], true);
        }

        $term = new Term();
        $termObject = $term->getById($termID);
        if (is_null($termObject)) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This term is invalid')
            ], true);
        }

        $html = view('dashboard.components.home-amenity-form', ['termObject' => $termObject])->render();

        $this->sendJson([
            'status' => 1,
            'html' => $html
        ], true);
    }

    public function _homeType(Request $request, $page = 1)
    {
        $folder = $this->getFolder();

        $search = Input::get('_s');
        $orderBy = Input::get('orderby', 'term_id');
        $order = Input::get('order', 'desc');
        $data = [
            'tax' => 'home-type',
            'search' => $search,
            'page' => $page,
            'orderby' => $orderBy,
            'order' => $order,
        ];

        if (!is_admin()) {
            $data['author'] = get_current_user_id();
        }
        $allHomeTypes = $this->getAllTerms(
            $data
        );

        return view("dashboard.screens.{$folder}.home-type", ['role' => $folder, 'bodyClass' => 'hh-dashboard', 'allHomeTypes' => $allHomeTypes]);
    }

    public function _homeAmenity(Request $request, $page = 1)
    {
        $folder = $this->getFolder();
        $search = Input::get('_s');
        $orderBy = Input::get('orderby', 'term_id');
        $order = Input::get('order', 'asc');

        $data = [
            'tax' => 'home-amenity',
            'search' => $search,
            'page' => $page,
            'orderby' => $orderBy,
            'order' => $order,
        ];

        if (!is_admin()) {
            $data['author'] = get_current_user_id();
        }
        $allHomeAmenities = $this->getAllTerms(
            $data
        );

        return view("dashboard.screens.{$folder}.home-amenity", ['role' => $folder, 'bodyClass' => 'hh-dashboard', 'allHomeAmenities' => $allHomeAmenities]);
    }

    public function _postCategory(Request $request, $page = 1)
    {
        $folder = $this->getFolder();
        $search = Input::get('_s');
        $orderBy = Input::get('orderby', 'term_id');
        $order = Input::get('order', 'desc');
        $data = [
            'tax' => 'post-category',
            'search' => $search,
            'page' => $page,
            'orderby' => $orderBy,
            'order' => $order,
        ];

        if (!is_admin()) {
            $data['author'] = get_current_user_id();
        }
        $allCategories = $this->getAllTerms(
            $data
        );
        return view("dashboard.screens.{$folder}.post-category", ['role' => $folder, 'bodyClass' => 'hh-dashboard', 'allCategories' => $allCategories]);
    }

    public function _postTag(Request $request, $page = 1)
    {
        $folder = $this->getFolder();
        $current_params = Route::current()->parameters();
        $search = Input::get('_s');
        $orderBy = Input::get('orderby', 'term_id');
        $order = Input::get('order', 'desc');

        $data = [
            'search' => $search,
            'page' => isset($current_params['page']) ? $current_params['page'] : 1,
            'orderby' => $orderBy,
            'order' => $order,
            'tax' => 'post-tag'
        ];
        if (!is_admin()) {
            $data['author'] = get_current_user_id();
        }
        $allTags = $this->getAllTerms(
            $data
        );

        return view("dashboard.screens.{$folder}.post-tag", ['role' => $folder, 'bodyClass' => 'hh-dashboard', 'allTags' => $allTags]);
    }

    public function getAllTerms($data = [])
    {
        $term = new Term();
        return $term->getAllTerms($data);
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
