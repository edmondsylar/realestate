<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests;
use Illuminate\Support\Str;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Media;
use Illuminate\Support\Facades\Input;

class MediaController extends Controller
{

    public function _getAttachments(Request $request)
    {
        $attachments = Input::get('attachments');
        $attachments = explode(',', $attachments);
        $size = Input::get('size', 'full');
        if (is_numeric($size)) {
            $size = [$size, $size];
        }
        $html = '';
        $url = [];
        if (!empty($attachments)) {
            $media = new Media();
            foreach ($attachments as $attachment_id) {
                $mediaObject = $media->getById($attachment_id);
                if ($mediaObject) {
                    $url = $mediaObject->media_url;
                    if (\App::environment('production_ssl')) {
                        $url = str_replace('http:', 'https:', $url);
                    }
                    $html .= '<div class="attachment-item"><div class="thumbnail"><img src="' . esc_attr($url) . '" alt="' . $mediaObject->media_description . '"></div></div>';
                }
            }
        }

        $this->sendJson([
            'status' => 1,
            'html' => $html,
            'url' => $url
        ], true);
    }

    public function _getAdvanceAttachments(Request $request)
    {
        $attachments = Input::get('attachments');
        $attachments = explode(',', $attachments);

        $html = '';

        if (!empty($attachments) && is_array($attachments)) {
            $postID = Input::get('postID');
            $post = get_post($postID);
            $isFeatured = $post->thumbnail_id;
            foreach ($attachments as $id) {
                if (!$isFeatured) {
                    set_home_thumbnail($postID, $id);
                    $isFeatured = $id;
                }
                $img = get_attachment_url($id);
                $classFeatured = ($id == $isFeatured) ? 'is-featured' : '';
                $html .= '<div class="col-6 col-md-3 item"><div class="gallery-item">
                    <div class="gallery-image">
                        <div class="hh-loading ">
                            <div class="lds-ellipsis loading-gallery">
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </div>
                        <div class="gallery-action">
                            <a href="javascript: void(0)" class="hh-gallery-add-featured ' . $classFeatured . '" data-post-id="' . $postID . '" data-id="' . $id . '" title="set is featured"><i class="fe-bookmark"></i></a>
                            <a href="javascript: void(0)" class="hh-gallery-delete" data-post-id="' . $postID . '" data-id="' . $id . '" title="'.__('Delete').'"><i class="dripicons-trash"></i></a>
                        </div>
                        <img src="' . $img . '" alt="' . get_attachment_alt($id) . '"
                             class="img-responsive">
                    </div>
                </div></div>';
            }
        }

        $this->sendJson([
            'status' => 1,
            'html' => $html,
            'featured_image' => get_attachment_url($isFeatured, [450, 320])
        ], true);
    }

    public function _updateMediaItemDetail(Request $request)
    {
        $attachment_id = Input::get('media_id');
        $media_title = Input::get('media_title');
        $media_description = Input::get('media_description');

        $media = new Media();
        $mediaObject = $media->issetAttachment($attachment_id);
        if ($mediaObject) {
            $data = [
                'media_title' => $media_title,
                'media_description' => $media_description
            ];
            $updated = $media->updateMedia($data, $attachment_id);
            if ($updated) {
                $this->sendJson([
                    'status' => 1,
                    'title' => __('System Alert'),
                    'message' => __('Updated successfully')
                ], true);
            } else {
                $this->sendJson([
                    'status' => 0,
                    'title' => __('System Alert'),
                    'message' => __('Can not update this attachment')
                ], true);
            }
        } else {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('Not found this attachment')
            ], true);
        }
    }

    public function _mediaItemDetail(Request $request)
    {
        $attachment_id = Input::get('attachment_id');
        $media = new Media();
        $mediaObject = $media->getById($attachment_id);
        $html = '';
        if (!empty($mediaObject) && is_object($mediaObject)) {
            $html = view('dashboard.components.media-item-detail', (array)$mediaObject)->render();
            $this->sendJson([
                'status' => 1,
                'message' => __('Loaded successfully'),
                'html' => $html
            ], true);
        }

        $this->sendJson([
            'status' => 0,
            'title' => __('System Alert'),
            'message' => __('Not found this attachment'),
        ], true);
    }

    public function _allMedia(Request $request)
    {
        $type = Input::get('type', '');

        $media = new Media();
        $allMedia = $media->listAttachments();
        $html = '';
        if (!empty($allMedia) && is_object($allMedia)) {
            foreach ($allMedia as $key => $attachment) {
                $attachment = (array)$attachment;
                $attachment['type'] = $type;
                $html .= view('dashboard.components.media-item', $attachment)->render();
            }
            $this->sendJson([
                'status' => 1,
                'title' => __('System Alert'),
                'message' => __('Loaded Media'),
                'html' => $html
            ], true);
        } else {
            $this->sendJson([
                'status' => 1,
                'title' => __('System Alert'),
                'message' => __('Not found media'),
                'html' => $html
            ], true);
        }
    }

    public function _addMedia(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]
        );
        if ($validator->fails()) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => $validator->errors()->first()
            ]);
        }
        $file = $request->file('file');
        $name = $file->getClientOriginalName();
        if (!empty($name)) {
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $title = pathinfo($name, PATHINFO_FILENAME);
            $size = $file->getSize();
            $name = Str::slug($title);
            $savedName = $name . '-' . time() . '.' . $ext;
            $folder = $this->getMediaFolder();
            $saved = $file->move(storage_path($folder), $savedName);
            if (!empty($saved) && is_object($saved)) {
                $data = [
                    'media_title' => $title,
                    'media_name' => $name,
                    'media_url' => $this->getMediaFolder(true) . '/' . $savedName,
                    'media_path' => $saved->getPathname(),
                    'media_size' => $size,
                    'media_type' => $saved->getExtension(),
                    'media_description' => $title,
                    'author' => Sentinel::getUser()->getUserId(),
                    'created_at' => time()
                ];

                $media = new Media();
                $media_id = $media->create($data);
                if ($media_id) {
                    $this->sendJson([
                        'status' => 2,
                        'title' => __('System Alert'),
                        'message' => sprintf(__('The attachment %s is uploaded successfully'), $title),
                    ], true);
                } else {
                    $this->sendJson([
                        'status' => 0,
                        'title' => __('System Alert'),
                        'message' => __('Have error when saving')
                    ], true);
                }
            } else {
                $this->sendJson([
                    'status' => 0,
                    'title' => __('System Alert'),
                    'message' => __('Have error when uploading')
                ], true);
            }
        } else {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This file is invalid')
            ], true);
        }
    }

    public function _deleteMediaItem(Request $request)
    {
        $attachment_id = Input::get('attachment_id');
        $media = new Media();
        $mediaObject = $media->getById($attachment_id);
        if (!empty($mediaObject) && is_object($mediaObject)) {
            $path = $mediaObject->media_path;
            if (is_file($path)) @unlink($path);
            $deleted = $media->deleteAttachment($attachment_id);
            if ($deleted) {
                $this->sendJson([
                    'status' => 1,
                    'title' => __('System Alert'),
                    'message' => __('Deleted successfully')
                ], true);
            } else {
                $this->sendJson([
                    'status' => 0,
                    'title' => __('System Alert'),
                    'message' => __('Can not delete this attachment')
                ], true);
            }
        } else {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('Not found this attachment')
            ], true);
        }
    }

    public function _getMedia()
    {
        $folder = $this->getFolder();
        return view("dashboard.screens.{$folder}.media", ['role' => $folder, 'bodyClass' => 'hh-dashboard']);
    }

    public function getMediaFolder($storage = false)
    {
        $user = Sentinel::getUser();
        $email = $user->getUserLogin();
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        if ($storage) {
            return asset('storage/' . $email . '/' . $year . '/' . $month . '/' . $day);
        } else {
            return 'app/public/' . $email . '/' . $year . '/' . $month . '/' . $day;
        }
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
