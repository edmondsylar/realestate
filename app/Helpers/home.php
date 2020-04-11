<?php

use App\Home;

function get_homes_by_hometype_id($hometype_id){
    $homeModel = new Home();
    return $homeModel->getHomeByHomeTypeID($hometype_id);
}

function render_home_comment_list($comments)
{
    echo '<ul>';
    foreach ($comments as $k => $v) {
        ?>
        <li id="comment-<?php echo esc_attr($v->comment_id); ?>" class="comment odd alt thread-odd thread-alt depth-1">
            <div id="div-comment-<?php echo esc_attr($v->comment_id) ?>" class="article comment  clearfix"
                 inline_comment="comment">
                <div class="comment-item-head">
                    <div class="media">
                        <div class="media-left">
                            <img alt="" src="<?php echo get_user_avatar($v->comment_author) ?>"
                                 class="avatar avatar-50 photo avatar-default" height="50" width="50">
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">
                                <?php
                                if (is_user_logged_in()) {
                                    echo esc_html(get_username($v->comment_author));
                                } else {
                                    echo esc_html($v->comment_name);
                                }
                                ?>
                            </h4>
                            <div class="date"><?php echo esc_html(date(hh_date_format(), $v->created_at)) ?></div>
                        </div>
                    </div>
                </div>
                <div class="comment-item-body">
                    <div class="comment-content">
                        <p class="comment-title"><?php echo esc_html($v->comment_title); ?></p>
                        <?php review_rating_star($v->comment_rate); ?>
                        <p><?php echo esc_html($v->comment_content); ?></p>
                    </div>
                </div>
            </div>
        </li>
        <?php
    }
    echo '</ul>';
}

function set_home_thumbnail($home_id, $thumbnail_id)
{
    $home = new Home();
    $data = [
        'thumbnail_id' => $thumbnail_id
    ];
    return $home->updateHome($data, $home_id);
}

function get_home_booking_type($home_object){
    if($home_object->booking_type == 'per_night'){
        return __('night');
    }elseif($home_object->booking_type == 'per_hour'){
        return __('hour');
    }


    return '';
}

function get_home_permalink($home_id, $home_slug = '', $type = 'home')
{
    if (empty($home_slug)) {
        $home = get_post($home_id);
        $home_slug = $home->post_slug;
    }
    $home_info = post_type_info($type);
    return url($home_info['slug'] . '/' . $home_slug);
}

function has_home_thumbnail($home_id)
{
    $home = get_post($home_id, 'home');
    return (isset($home->thumbnail_id) && $home->thumbnail_id) ? true : false;

}

function get_home_thumbnail_id($home_id)
{
    $home = get_post($home_id, 'home');
    return (isset($home->thumbnail_id) && $home->thumbnail_id) ? $home->thumbnail_id : false;
}

function get_home_galleries_url($gallery)
{
    $res = [];
    if (!empty($gallery)) {
        $gallery = explode(',', $gallery);
        if (!empty($gallery)) {
            foreach ($gallery as $k => $v) {
                array_push($res, get_attachment_url($v, [300, 200]));
            }
        }
    }
    return $res;
}

function get_filter_items()
{
    $res = [];
    $filter_type = [
        'home-type' => 'Home Type',
        'home-amenity' => 'Home Amenity'
    ];
    foreach ($filter_type as $k => $v) {
        $res[$k] = [
            'label' => $v,
            'items' => get_terms($k)
        ];
    }
    return $res;
}

function get_list_homes($data)
{
    $home = new Home();
    return $home->getAllHomes($data);
}