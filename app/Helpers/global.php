<?php

use App\Home;
use App\Page;
use App\Post;
use App\Media;
use \Gumlet\ImageResize;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MailController;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

function list_hours($step = 30)
{
    $return = [];
    $start = 1;
    $end = 12;
    $time = ['AM', 'PM'];
    foreach ($time as $subfix) {
        if ($subfix == 'PM') {
            $start = 1;
            $end = 11;
        }
        for ($i = $start; $i <= $end; $i++) {
            for ($j = 0; $j <= 45; $j += $step) {
                if ($i == 12 && $subfix == 'AM') {
                    $time = sprintf('%02d:%02d %s', $i, $j, 'PM');
                } else {
                    $time = sprintf('%02d:%02d %s', $i, $j, $subfix);
                }

                $return[$time] = $time;
            }
        }
    }
    $time = sprintf('%02d:%02d %s', '12', '00', 'AM');
    $return[$time] = $time;

    return $return;
}

function referer_field($echo = true)
{
    $referer_field = '<input type="hidden" name="_hh_http_referer" value="' . esc_attr(stripslashes($_SERVER['REQUEST_URI'])) . '" />';

    if ($echo) {
        echo balanceTags($referer_field);
    }
    return $referer_field;
}

function get_referer($default = '')
{
    $referer = \Illuminate\Support\Facades\Input::get('_hh_http_referer', $default);
    return $referer;
}

function need_approve_review()
{
    $enable_review = get_option('review_approval');
    if ($enable_review == 'on') {
        return true;
    }
    return false;
}

function enable_review()
{
    $enable_review = get_option('enable_review', 'on');
    if ($enable_review == 'on') {
        return true;
    }
    return false;
}

function get_permalink_by_id($post_id, $post_type = 'post')
{
    $link = '';
    switch ($post_type) {
        case 'page':
            $model = new Page();
            break;
        case 'home':
            $model = new Home();
            break;
        case 'post':
        default:
            $model = new Post();
            break;
    }
    $postObject = $model->getById($post_id);
    if (!is_null($postObject)) {
        $link = get_the_permalink($post_id, $postObject->post_slug, $post_type);
    }
    return $link;
}

function get_menu_dashboard()
{
    $menu = Config::get('awebooking.customer_menu');

    if (Sentinel::inRole('administrator')) {
        $menu = Config::get('awebooking.admin_menu');
    } elseif (Sentinel::inRole('partner')) {
        $menu = Config::get('awebooking.partner_menu');
    } else {
        $menu = Config::get('awebooking.customer_menu');
    }

    return $menu;

}

function updateEnv($key = 'APP_KEY', $key_value = '')
{
    $path = base_path('.env');
    if (file_exists($path)) {
        file_put_contents($path, str_replace(
            $key . '=' . env($key), $key . '=' . $key_value, file_get_contents($path)
        ));
    }
}

function send_mail($email_from = '', $from_label = '', $email_to, $subject, $body, $email_reply = '')
{
    $mail = new MailController();
    if (empty($email_from)) {
        $user = get_users_by_role('administrator');
        foreach ($user as $u) {
            $email_from = $u;
            break;
        }
    }
    if (empty($from_label)) {
        $from_label = get_option('email_from');
    }
    $mail->setEmailFrom($email_from, $from_label);
    $mail->setEmailTo($email_to);
    if (!empty($email_reply)) {
        $mail->setReplyTo($email_reply);
    }

    return $mail->sendMail($subject, $body);
}

function comment_status_info($name = '')
{
    $status = [
        'publish' => [
            'name' => 'Publish'
        ],
        'pending' => [
            'name' => 'Pending'
        ]
    ];

    if (!empty($name) && isset($status[$name])) {
        return $status[$name];
    } else {
        return $status;
    }
}

function review_rating_star($rate)
{
    if (!empty($rate)) {
        echo '<div class="star-rating">';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rate) {
                echo '<i class="fa fa-star"></i>';
            } else {
                echo '<i class="fa fa-star star-none"></i>';
            }
        }
        echo '</div>';
    }
}

function get_blog_image_url()
{
    $image_id = get_option('blog_image');
    return get_attachment_url($image_id);
}


function short_content($text, $num_words = 55, $more = null)
{
    if (null === $more) {
        $more = '&hellip;';
    }

    $original_text = $text;
    $text = strip_all_tags($text);

    $words_array = preg_split("/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY);
    $sep = ' ';
    if (count($words_array) > $num_words) {
        array_pop($words_array);
        $text = implode($sep, $words_array);
        $text = $text . $more;
    } else {
        $text = implode($sep, $words_array);
    }

    return apply_filters('trim_words', $text, $num_words, $more, $original_text);
}

function strip_all_tags($string, $remove_breaks = false)
{
    $string = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $string);
    $string = strip_tags($string);

    if ($remove_breaks) {
        $string = preg_replace('/[\r\n\t ]+/', ' ', $string);
    }

    return trim($string);
}

function balanceTags($text, $force = false)
{
    $text = str_replace('<script>', '&lt;script&gt;', $text);
    $text = str_replace('</script>', '&lt;/script&gt;', $text);
    return force_balance_tags($text);
}

function force_balance_tags($text)
{
    $tagstack = array();
    $stacksize = 0;
    $tagqueue = '';
    $newtext = '';
    // Known single-entity/self-closing tags
    $single_tags = array('area', 'base', 'basefont', 'br', 'col', 'command', 'embed', 'frame', 'hr', 'img', 'input', 'isindex', 'link', 'meta', 'param', 'source');
    // Tags that can be immediately nested within themselves
    $nestable_tags = array('blockquote', 'div', 'object', 'q', 'span');

    // WP bug fix for comments - in case you REALLY meant to type '< !--'
    $text = str_replace('< !--', '<    !--', $text);
    // WP bug fix for LOVE <3 (and other situations with '<' before a number)
    $text = preg_replace('#<([0-9]{1})#', '&lt;$1', $text);

    while (preg_match('/<(\/?[\w:]*)\s*([^>]*)>/', $text, $regex)) {
        $newtext .= $tagqueue;

        $i = strpos($text, $regex[0]);
        $l = strlen($regex[0]);

        // clear the shifter
        $tagqueue = '';
        // Pop or Push
        if (isset($regex[1][0]) && '/' == $regex[1][0]) { // End Tag
            $tag = strtolower(substr($regex[1], 1));
            // if too many closing tags
            if ($stacksize <= 0) {
                $tag = '';
                // or close to be safe $tag = '/' . $tag;

                // if stacktop value = tag close value then pop
            } elseif ($tagstack[$stacksize - 1] == $tag) { // found closing tag
                $tag = '</' . $tag . '>'; // Close Tag
                // Pop
                array_pop($tagstack);
                $stacksize--;
            } else { // closing tag not at top, search for it
                for ($j = $stacksize - 1; $j >= 0; $j--) {
                    if ($tagstack[$j] == $tag) {
                        // add tag to tagqueue
                        for ($k = $stacksize - 1; $k >= $j; $k--) {
                            $tagqueue .= '</' . array_pop($tagstack) . '>';
                            $stacksize--;
                        }
                        break;
                    }
                }
                $tag = '';
            }
        } else { // Begin Tag
            $tag = strtolower($regex[1]);

            // Tag Cleaning

            // If it's an empty tag "< >", do nothing
            if ('' == $tag) {
                // do nothing
            } elseif (substr($regex[2], -1) == '/') { // ElseIf it presents itself as a self-closing tag...
                // ...but it isn't a known single-entity self-closing tag, then don't let it be treated as such and
                // immediately close it with a closing tag (the tag will encapsulate no text as a result)
                if (!in_array($tag, $single_tags)) {
                    $regex[2] = trim(substr($regex[2], 0, -1)) . "></$tag";
                }
            } elseif (in_array($tag, $single_tags)) { // ElseIf it's a known single-entity tag but it doesn't close itself, do so
                $regex[2] .= '/';
            } else { // Else it's not a single-entity tag
                // If the top of the stack is the same as the tag we want to push, close previous tag
                if ($stacksize > 0 && !in_array($tag, $nestable_tags) && $tagstack[$stacksize - 1] == $tag) {
                    $tagqueue = '</' . array_pop($tagstack) . '>';
                    $stacksize--;
                }
                $stacksize = array_push($tagstack, $tag);
            }

            // Attributes
            $attributes = $regex[2];
            if (!empty($attributes) && $attributes[0] != '>') {
                $attributes = ' ' . $attributes;
            }

            $tag = '<' . $tag . $attributes . '>';
            //If already queuing a close tag, then put this tag on, too
            if (!empty($tagqueue)) {
                $tagqueue .= $tag;
                $tag = '';
            }
        }
        $newtext .= substr($text, 0, $i) . $tag;
        $text = substr($text, $i + $l);
    }

    // Clear Tag Queue
    $newtext .= $tagqueue;

    // Add Remaining text
    $newtext .= $text;

    // Empty Stack
    while ($x = array_pop($tagstack)) {
        $newtext .= '</' . $x . '>'; // Add remaining tags to close
    }

    // WP fix for the bug with HTML comments
    $newtext = str_replace('< !--', '<!--', $newtext);
    $newtext = str_replace('<    !--', '< !--', $newtext);

    return $newtext;
}

function wp_redirect($location, $status = 302, $x_redirect_by = 'Laravel')
{

    if (!$location) {
        return false;
    }

    if (is_string($x_redirect_by)) {
        header("X-Redirect-By: $x_redirect_by");
    }

    header("Location: $location", true, $status);

    return true;
}

function get_short_address($item)
{
    $html = '';
    if (get_translate($item->location_city)) {
        $html .= get_translate($item->location_city) . ', ';
    }
    if (get_translate($item->location_country)) {
        $html .= get_translate($item->location_country);
    }

    return $html;
}

function _n($single = '%s', $multi = '%s', $var = 0)
{
    if ($var > 1 || $var == 0) {
        return str_replace('%s', $var, $multi);
    } else {
        return str_replace('%s', $var, $single);
    }
}

function posts_per_page($type = 'global')
{
    $posts_per_page_conf = Config::get('awebooking.posts_per_page');
    if (isset($posts_per_page_conf[$type])) {
        return $posts_per_page_conf[$type];
    } else {
        return 12;
    }
}

function comments_per_page($type = 'blog')
{
    $comment_per_page_conf = Config::get('awebooking.comments_per_page');
    if (isset($comment_per_page_conf[$type])) {
        return $comment_per_page_conf[$type];
    } else {
        return 5;
    }
}

function booking_status_info($name = '')
{
    $booking_status = Config::get('awebooking.booking_status');
    if (empty($name)) {
        return isset($booking_status) ? $booking_status : false;
    }
    return isset($booking_status[$name]) ? $booking_status[$name] : false;
}

function get_payments($key = '')
{
    $allPayments = apply_filters('hh_payment_gateways', Config::get('awebooking.payment_gateways'));
    if ($key) {
        return (isset($allPayments[$key])) ? $allPayments[$key] : false;
    }
    return (!empty($allPayments)) ? $allPayments : false;
}

function get_available_payments($payment = '')
{
    $allPayments = get_payments($payment);
    if (!$allPayments) {
        return false;
    }
    if (is_array($allPayments)) {
        $return = [];
        foreach ($allPayments as $key => $_payment) {
            $enable = get_option('enable_' . $key, 'off');
            if ($enable == 'on') {
                $return[] = $_payment;
            }
        }

        return $return;
    } else {
        $enable = get_option('enable_' . $payment, 'off');
        return ($enable == 'on') ? $allPayments : false;
    }
}

function get_payment_options($type = 'title')
{
    $allPayments = get_payments();
    $return = [];
    foreach ($allPayments as $key => $payment) {
        $return['title'][] = $payment::getOptions()['title'];
        foreach ($payment::getOptions()['content'] as $item) {
            $return['content'][] = $item;
        }
    }
    if (!empty($type)) {
        return $return[$type];
    }
    return $return;
}

function page_title($is_dashboard = false)
{
    $title = get_option('site_name', Config::get('app.name', 'Laravel App'));
    $current_route = Route::current();
    $name = $current_route->getName();
    if ($is_dashboard) {
        $menu = Config::get('awebooking.customer_menu');
        if (is_admin()) {
            $menu = Config::get('awebooking.admin_menu');
        } elseif (is_partner()) {
            $menu = Config::get('awebooking.partner_menu');
        }
        foreach ($menu as $item) {
            if ($item['type'] == 'item') {
                if ($item['route'] === $name) {
                    $title = $item['label'] . ' - ' . $title;
                    break;
                }
            } elseif ($item['type'] == 'parent') {
                foreach ($item['child'] as $sub_item) {
                    if ($sub_item['route'] === $name) {
                        $title = $sub_item['label'] . ' - ' . $title;
                        break;
                    }
                }
            }
        }
    } else {
        $params = $current_route->parameters();

        if (isset($params['home_name'])) {
            global $post;
            $title = get_translate($post->post_title) . ' - ' . $title;
        } else {
            $name = $current_route->getName();
            $pages_name = Config::get('awebooking.pages_name');
            foreach ($pages_name as $item) {
                if ($item['route'] === $name) {
                    $title = $item['label'] . ' - ' . $title;
                }
            }
        }
    }


    return $title;
}

function dashboard_url($url = '', $id = '', $page = '')
{
    if (empty($id) && empty($page)) {
        return url(Config::get('awebooking.prefix_dashboard') . '/' . $url);
    } else {
        if (!empty($id) && !empty($page)) {
            return url(Config::get('awebooking.prefix_dashboard') . '/' . $url . '/' . $id . '/' . $page);
        } elseif (!empty($id)) {
            return url(Config::get('awebooking.prefix_dashboard') . '/' . $url . '/' . $id);
        } elseif (!empty($page)) {
            return url(Config::get('awebooking.prefix_dashboard') . '/' . $url . '/' . $page);
        }
    }
}

function auth_url($name = '')
{
    return url(Config::get('awebooking.prefix_auth') . '/' . $name);
}

function checkout_url()
{
    return url(Config::get('awebooking.checkout_slug'));
}

function thankyou_url()
{
    return url(Config::get('awebooking.after_checkout_slug'));
}

function current_url()
{
    return url()->current();
}

function plugin_path($path = '')
{
    $base_path = base_path('plugins');
    return $base_path . ($path ? '/' . $path : '');
}

function current_screen()
{
    return Route::currentRouteName();
}

function start_get_view()
{
    ob_start();
}

function end_get_view()
{
    return @ob_get_clean();
}

function post_type_info($name = '')
{
    $post_types = Config::get('awebooking.post_types');
    if (empty($name)) {
        return isset($post_types) ? $post_types : false;
    }
    return isset($post_types[$name]) ? $post_types[$name] : false;
}

function service_status_info($name = '')
{
    $service_status = Config::get('awebooking.service_status');
    if (empty($name)) {
        return isset($service_status) ? $service_status : false;
    }
    return isset($service_status[$name]) ? $service_status[$name] : false;
}

function dashboard_pagination($args = [])
{
    $defaults = [
        'range' => 4,
        'total' => 0,
        'previous_string' => '<i class="icon-arrow-left"></i>',
        'next_string' => '<i class="icon-arrow-right"></i>',
        'before_output' => '<nav aria-label="navigation"><ul class="pagination">',
        'after_output' => '</ul></nav>',
        'posts_per_page' => posts_per_page(),
    ];

    $args = wp_parse_args($args, $defaults);
    $args['range'] = (int)$args['range'] - 1;
    $posts_per_page = $args['posts_per_page'];

    $count = ceil($args['total'] / $posts_per_page);

    $current_params = \Illuminate\Support\Facades\Route::current()->parameters();
    $totalParams = count($current_params);

    $page = isset($current_params['page']) ? $current_params['page'] : 1;
    $ceil = ceil($args['range'] / 2);
    if ($count <= 1)
        return false;
    if (!$page)
        $page = 1;
    if ($count > $args['range']) {
        if ($page <= $args['range']) {
            $min = 1;
            $max = $args['range'] + 1;
        } elseif ($page >= ($count - $ceil)) {
            $min = $count - $args['range'];
            $max = $count;
        } elseif ($page >= $args['range'] && $page < ($count - $ceil)) {
            $min = $page - $ceil;
            $max = $page + $ceil;
        }
    } else {
        $min = 1;
        $max = $count;
    }
    $echo = '';
    $url = dashboard_url(Route::currentRouteName());
    $previous_num = intval($page) - 1;


    $previous = $url;
    switch ($totalParams) {
        case 0:
        case 1:
            $previous = $url . '/' . $previous_num . '/';
            break;
        case 2:
            $previous = $url . '/' . $current_params['id'] . '/' . $previous_num . '/';
            break;
    }
    if ($previous && (1 == $page)) {
        $echo .= '<li class="disabled"><a class="page-link" href="javascript:void(0);" title="previous">' . $args['previous_string'] . '</a></li>';
    }
    if ($previous && (1 != $page)) {
        $echo .= '<li class="page-item"><a class="page-link" data-pagination="' . $previous_num . '" href="' . $previous . '" title="previous">' . $args['previous_string'] . '</a></li>';
    }
    if (!empty($min) && !empty($max)) {
        for ($i = $min; $i <= $max; $i++) {
            if ($page == $i) {
                $echo .= '<li class="active page-item"><a class="page-link" data-pagination="' . $i . '" href="javascript:void(0);">' . str_pad((int)$i, 1, '0', STR_PAD_LEFT) . '</a></li>';
            } else {
                $_url = $url;
                switch ($totalParams) {
                    case 0:
                    case 1:
                        $_url = $url . '/' . $i . '/';
                        break;
                    case 2:
                        $_url = $url . '/' . $current_params['id'] . '/' . $i . '/';
                        break;
                }
                $echo .= sprintf('<li class="page-item"><a class="page-link" data-pagination="' . $i . '" href="%s">%2d</a></li>', $_url, $i);
            }
        }
    }
    $next_num = intval($page) + 1;

    $next = $url;
    switch ($totalParams) {
        case 0:
        case 1:
            $next = $url . '/' . $next_num . '/';
            break;
        case 2:
            $next = $url . '/' . $current_params['id'] . '/' . $next_num . '/';
            break;
    }

    if ($next && ($count == $page)) {
        $echo .= '<li class="disabled"><a class="page-link" href="javascript:void(0);" title="next">' . $args['next_string'] . '</a></li>';
    }
    if ($next && ($count != $page)) {
        $echo .= '<li class="page-item"><a class="page-link" data-pagination="' . $next_num . '" href="' . $next . '" title="next">' . $args['next_string'] . '</a></li>';
    }
    if (isset($echo))
        echo balanceTags($args['before_output'] . $echo . $args['after_output']);
}

function star_rating_render($rate = 0)
{
    $html = '<div class="hh-rating">';
    for ($i = 1; $i <= $rate; $i++) {
        if ($rate >= $i) {
            $html .= '<i class="fas fa-star"></i>';
        } else {
            $html .= '<i class="fas fa-star no-rate"></i>';
        }
    }
    $html .= '</div>';

    return $html;
}

function render_service_slug($title, $by_slug = '', $service = 'page')
{
    switch ($service) {
        case 'home':
            $model = new Home();
            break;

        case 'post':
            $model = new Post();
            break;

        default:
            $model = new Page();
            break;
    }

    if (!empty($by_slug)) {
        $title = $by_slug;
    } else {
        $title = get_translate($title);
    }

    $slug = Str::slug($title, '-');

    $numberSuffixes = $model->getNumberSuffixes($slug);

    return ($numberSuffixes > 0) ? ($slug . '-' . $numberSuffixes) : $slug;
}

function get_all_posts($post_type = 'post', $number = '-1', $status = 'publish')
{
    switch ($post_type) {
        case 'page':
            $page = new Page();
            $res = $page->getAllPages([
                'number' => $number,
                'status' => $status
            ]);
            break;
        case 'home':
            $home = new Home();
            $res = $home->getAllHomes([
                'number' => $number,
                'status' => $status
            ]);
            break;
        default:
            $post = new Post();
            $res = $post->getAllPosts([
                'number' => $number,
                'status' => $status
            ]);
            break;
    }
    return $res;
}

function get_post($post_id, $post_type = 'home')
{
    switch ($post_type) {
        case 'home':
        default:
            $post = HomeController::get_inst()->getById($post_id);
            break;
        case 'page':
            $page = new Page();
            $post = $page->getById($post_id);
            break;
        case 'post':
            $page = new Post();
            $post = $page->getById($post_id);
            break;
    }

    return $post;
}

function reset_post_data()
{
    global $post, $old_post;
    $post = $old_post;
}

function get_the_permalink($post_id, $post_slug = '', $type = 'home')
{
    if (empty($post_slug)) {
        $post = get_post($post_id, $type);
        $post_slug = $post->post_slug;
    }
    $post_info = post_type_info($type);
    return url($post_info['slug'] . '/' . $post_slug);
}

function get_attachment_url($attachment_id, $size = 'full', $default = true)
{
    $attachment = get_attachment($attachment_id);
    if ($attachment) {
        $url = $attachment->media_url;
        if (\App::environment('production_ssl')) {
            $url = str_replace('http:', 'https:', $url);
        }
        if ($size == 'full') {
            return $url;
        }
        $media_url = $url;
        $url_info = pathinfo($media_url);
        $url = $url_info['dirname'];

        $media_path = $attachment->media_path;
        $path_info = pathinfo($media_path);
        $name = $path_info['filename'];
        $extension = $path_info['extension'];
        $path = $path_info['dirname'];

        switch ($size) {
            case 'medium':
                $file = $path . '/' . $name . '-800x600' . '.' . $extension;
                break;
            case 'small':
                $file = $path . '/' . $name . '-400x300' . '.' . $extension;
                break;
            default:
                $file = $path . '/' . $name . '-' . $size[0] . 'x' . $size[1] . '.' . $extension;
                break;
        }
        if (file_exists($file)) {
            return $url . '/' . basename($file);
        } else {
            if (file_exists($media_path)) {
                $detectedType = exif_imagetype($media_path);
                if ($detectedType) {
                    crop_image($media_path, $size);
                    if (is_file($file)) {
                        return $url . '/' . basename($file);
                    } else {
                        return placeholder_image($size);
                    }
                } else {
                    return placeholder_image($size);
                }
            } else {
                return placeholder_image($size);
            }
        }
    }

    if ($default) {
        return placeholder_image($size);
    }
    return '';
}

function placeholder_image($size = 'full')
{
    switch ($size) {
        case 'full':
            $url = '//via.placeholder.com/1200x900';
            break;
        case 'medium':
            $url = '//via.placeholder.com/800x600';
            break;
        case 'small':
            $url = '//via.placeholder.com/400x300';
            break;
        default:
            $url = '//via.placeholder.com/' . $size[0] . 'x' . $size[1];
            break;
    }
    return $url;
}

function get_file_size($size = 0)
{
    if ($size >= 1073741824) {
        $size = number_format($size / 1073741824, 2) . ' GB';
    } elseif ($size >= 1048576) {
        $size = number_format($size / 1048576, 2) . ' MB';
    } elseif ($size >= 1024) {
        $size = number_format($size / 1024, 2) . ' KB';
    } elseif ($size > 1) {
        $size = $size . ' bytes';
    } elseif ($size == 1) {
        $size = $size . ' byte';
    } else {
        $size = '0 bytes';
    }

    return $size;
}

function crop_image($path, $size = [150, 150])
{
    switch ($size) {
        case 'full':
            $size = [1200, 900];
            break;
        case 'medium':
            $size = [800, 600];
            break;
        case 'small':
            $size = [400, 300];
            break;
    }
    $image = new ImageResize($path);
    $image->crop($size[0], $size[1]);
    $pathinfo = pathinfo($path);
    $name = $pathinfo['filename'] . '-' . $size[0] . 'x' . $size[1];
    $newpath = $pathinfo['dirname'] . '/' . $name . '.' . $pathinfo['extension'];
    $image->save($newpath);
}

function get_attachment_alt($attachment_id)
{
    $attachment = get_attachment($attachment_id);
    if ($attachment) {
        return esc_attr($attachment->media_description);
    }
    return '';
}

function get_attachment($attachment_id)
{
    $media = new Media();
    return $media->getById($attachment_id);
}

function get_option($key = '', $default = '')
{
    return \ThemeOptions::getOption($key, $default);
}

function get_opt($option_name = '', $default = '')
{
    $option = new \App\Option();
    $value = $option->getOption($option_name);
    if (!$value) {
        return $default;
    } else {
        return maybe_unserialize($value->option_value);
    }
}

function update_opt($option_name = '', $option_value = '')
{
    $option = new \App\Option();
    $has_option = $option->hasOption($option_name);
    if ($has_option) {

        return $option->updateOption($option_name, maybe_serialize($option_value));
    } else {

        return $option->createOption($option_name, maybe_serialize($option_value));
    }
}

function enqueue_style($name)
{
    $enqueue = \EnqueueScripts::get_inst();
    $enqueue->_enqueueStyle($name);
}

function enqueue_script($name)
{
    $enqueue = \EnqueueScripts::get_inst();
    $enqueue->_enqueueScript($name);
}

function is_timestamp($timestamp)
{
    return ((int)$timestamp === $timestamp)
        && ($timestamp <= PHP_INT_MAX)
        && ($timestamp >= ~PHP_INT_MAX);
}

function hh_date_diff($start, $end, $type = 'date')
{
    switch ($type) {
        case 'date':
            $start = date_create(date('Y-m-d', $start));
            $end = date_create(date('Y-m-d', $end));
            $diff = date_diff($start, $end);

            return $diff->format("%a");
            break;
        case 'hour':
            $diff = $end - $start;
            $hour = (int)($diff / 3600);
            if ($hour <= 0) {
                $hour = 1;
            }
            if ($diff % 3600) {
                $hour += 1;
            }

            return $hour;
            break;
        case 'minute':
            $diff = $end - $start;
            $minute = (int)($diff / 60);
            if ($minute <= 0) {
                $minute = 1;
            }
            if ($diff % 60) {
                $minute += 1;
            }

            return $minute;
            break;
        case 'second':
            return $end - $start;
            break;
    }

}

function is_weekend($timestamp, $rule = 'sun')
{
    $rules = [
        'sun' => [0],
        'sat_sun' => [0, 6],
        'fri_sat' => [5, 6],
        'fri_sat_sun' => [0, 5, 6]
    ];

    $weekDay = date('w', $timestamp);
    if (isset($rules[$rule])) {
        return (in_array($weekDay, $rules[$rule])) ? true : false;
    } else {
        return false;
    }

}

function hh_encrypt($string)
{
    $key_encrypt = Config::get('awebooking.key_encrypt');

    return md5(md5($key_encrypt) . md5($string));
}

function hh_compare_encrypt($string = '', $encrypt = '')
{
    $key_encrypt = Config::get('awebooking.key_encrypt');
    $string = md5(md5($key_encrypt) . md5($string));
    if (!empty($string) && !empty($encrypt) && $string === $encrypt) {
        return true;
    }

    return false;
}

function maybe_unserialize($original)
{
    if (is_serialized($original)) {
        return @unserialize($original);
    }
    return $original;
}

function maybe_serialize($data)
{
    if (is_array($data) || is_object($data)) {
        return serialize($data);
    }

    if (is_serialized($data, false)) {
        return serialize($data);
    }

    return $data;
}

function is_serialized($data, $strict = true)
{
    if (!is_string($data)) {
        return false;
    }
    $data = trim($data);
    if ('N;' == $data) {
        return true;
    }
    if (strlen($data) < 4) {
        return false;
    }
    if (':' !== $data[1]) {
        return false;
    }
    if ($strict) {
        $lastc = substr($data, -1);
        if (';' !== $lastc && '}' !== $lastc) {
            return false;
        }
    } else {
        $semicolon = strpos($data, ';');
        $brace = strpos($data, '}');
        // Either ; or } must exist.
        if (false === $semicolon && false === $brace) {
            return false;
        }
        // But neither must be in the first X characters.
        if (false !== $semicolon && $semicolon < 3) {
            return false;
        }
        if (false !== $brace && $brace < 4) {
            return false;
        }
    }
    $token = $data[0];
    switch ($token) {
        case 's':
            if ($strict) {
                if ('"' !== substr($data, -2, 1)) {
                    return false;
                }
            } elseif (false === strpos($data, '"')) {
                return false;
            }
        case 'a':
        case 'O':
            return (bool)preg_match("/^{$token}:[0-9]+:/s", $data);
        case 'b':
        case 'i':
        case 'd':
            $end = $strict ? '$' : '';
            return (bool)preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
    }
    return false;
}

function get_icon($name = '', $color = '', $width = '', $height = '', $stroke = false)
{
    global $hh_fonts;
    if (!$hh_fonts) {
        include_once public_path('fonts/fonts.php');
        if (isset($fonts)) {
            $hh_fonts = $fonts;
        }
    }
    if (empty($hh_fonts)) {
        return '';
    }
    if (!isset($hh_fonts[$name])) {
        return '';
    }
    $icon = $hh_fonts[$name];
    if (!empty($color)) {
        if ($stroke) {
            $icon = preg_replace('/stroke="(.{7})"/', 'stroke="' . $color . '"', $icon);
            $icon = preg_replace('/stroke:(.{7})/', 'stroke:' . $color, $icon);
        } else {
            $icon = preg_replace('/fill="(.{7})"/', 'fill="' . $color . '"', $icon);
            $icon = preg_replace('/fill:(.{7})/', 'fill:' . $color, $icon);
        }
    }

    if (!empty($width)) {
        $icon = preg_replace('/width="(\d{2}[a-z]{2})"/', 'width="' . $width . '"', $icon);
    }

    if (!empty($height)) {
        $icon = preg_replace('/height="(\d{2}[a-z]{2})"/', 'height="' . $height . '"', $icon);
    }

    return '<i class="hh-icon fa">' . $icon . '</i>';
}

function hh_date_format()
{
    return Config::get('awebooking.date_format');
}

function hh_date_format_moment()
{
    $format = hh_date_format();
    $format = str_replace('j', 'd', $format);
    $format = str_replace('S', 'd', $format);
    $format = str_replace('n', 'm', $format);

    $ori_format = [
        'd' => 'DD',
        'm' => 'MM',
        'M' => 'MM',
        'Y' => 'YYYY',
        'y' => 'YY',
        'F' => 'MM',
    ];
    preg_match_all("/[a-zA-Z]/", $format, $out);

    $out = $out[0];
    foreach ($out as $key => $val) {
        foreach ($ori_format as $ori_key => $ori_val) {
            if ($val == $ori_key) {
                $format = str_replace($val, $ori_val, $format);
            }
        }
    }

    return $format;
}

function hh_time_format()
{
    return Config::get('awebooking.time_format');
}

function remove_query_arg($key, $query = false)
{
    if (is_array($key)) { // removing multiple keys
        foreach ($key as $k) {
            $query = add_query_arg($k, false, $query);
        }
        return $query;
    }
    return add_query_arg($key, false, $query);
}

function add_query_arg(...$args)
{
    $args = func_get_args();
    if (is_array($args[0])) {
        if (count($args) < 2 || false === $args[1]) {
            $uri = $_SERVER['REQUEST_URI'];
        } else {
            $uri = $args[1];
        }
    } else {
        if (count($args) < 3 || false === $args[2]) {
            $uri = $_SERVER['REQUEST_URI'];
        } else {
            $uri = $args[2];
        }
    }

    if ($frag = strstr($uri, '#')) {
        $uri = substr($uri, 0, -strlen($frag));
    } else {
        $frag = '';
    }

    if (0 === stripos($uri, 'http://')) {
        $protocol = 'http://';
        $uri = substr($uri, 7);
    } elseif (0 === stripos($uri, 'https://')) {
        $protocol = 'https://';
        $uri = substr($uri, 8);
    } else {
        $protocol = '';
    }

    if (strpos($uri, '?') !== false) {
        list($base, $query) = explode('?', $uri, 2);
        $base .= '?';
    } elseif ($protocol || strpos($uri, '=') === false) {
        $base = $uri . '?';
        $query = '';
    } else {
        $base = '';
        $query = $uri;
    }

    wp_parse_str($query, $qs);
    $qs = urlencode_deep($qs); // this re-URL-encodes things that were already in the query string
    if (is_array($args[0])) {
        foreach ($args[0] as $k => $v) {
            $qs[$k] = $v;
        }
    } else {
        $qs[$args[0]] = $args[1];
    }

    foreach ($qs as $k => $v) {
        if ($v === false) {
            unset($qs[$k]);
        }
    }

    $ret = build_query($qs);
    $ret = trim($ret, '?');
    $ret = preg_replace('#=(&|$)#', '$1', $ret);
    $ret = $protocol . $base . $ret . $frag;
    $ret = rtrim($ret, '?');
    return $ret;
}

function urlencode_deep($value)
{
    return map_deep($value, 'urlencode');
}

function build_query($data)
{
    return _http_build_query($data, null, '&', '', false);
}

function wp_parse_str($string, &$array)
{
    parse_str($string, $array);
    if (get_magic_quotes_gpc()) {
        $array = stripslashes_deep($array);
    }
    /**
     * Filters the array of variables derived from a parsed string.
     *
     * @param array $array The array populated with variables.
     * @since 2.3.0
     *
     */
    $array = apply_filters('wp_parse_str', $array);
}

function stripslashes_deep($value)
{
    return map_deep($value, 'stripslashes_from_strings_only');
}

function stripslashes_from_strings_only($value)
{
    return is_string($value) ? stripslashes($value) : $value;
}

function map_deep($value, $callback)
{
    if (is_array($value)) {
        foreach ($value as $index => $item) {
            $value[$index] = map_deep($item, $callback);
        }
    } elseif (is_object($value)) {
        $object_vars = get_object_vars($value);
        foreach ($object_vars as $property_name => $property_value) {
            $value->$property_name = map_deep($property_value, $callback);
        }
    } else {
        $value = call_user_func($callback, $value);
    }

    return $value;
}

function _http_build_query($data, $prefix = null, $sep = null, $key = '', $urlencode = true)
{
    $ret = array();

    foreach ((array)$data as $k => $v) {
        if ($urlencode) {
            $k = urlencode($k);
        }
        if (is_int($k) && $prefix != null) {
            $k = $prefix . $k;
        }
        if (!empty($key)) {
            $k = $key . '%5B' . $k . '%5D';
        }
        if ($v === null) {
            continue;
        } elseif ($v === false) {
            $v = '0';
        }

        if (is_array($v) || is_object($v)) {
            array_push($ret, _http_build_query($v, '', $sep, $k, $urlencode));
        } elseif ($urlencode) {
            array_push($ret, $k . '=' . urlencode($v));
        } else {
            array_push($ret, $k . '=' . $v);
        }
    }

    if (null === $sep) {
        $sep = ini_get('arg_separator.output');
    }

    return implode($sep, $ret);
}

function wp_parse_args($args, $defaults = '')
{
    if (is_object($args)) {
        $r = get_object_vars($args);
    } elseif (is_array($args)) {
        $r =& $args;
    } else {
        wp_parse_str($args, $r);
    }

    if (is_array($defaults)) {
        foreach ($defaults as $key => $value) {
            if (isset($r[$key]) && !empty($r[$key])) {
                $defaults[$key] = $r[$key];
            }
        }
        return $defaults;
    }
    return $r;
}

function frontend_pagination($args = [], $comment = false)
{
    $defaults = [
        'range' => 4,
        'total' => 0,
        'previous_string' => '<i class="icon-arrow-left"></i>',
        'next_string' => '<i class="icon-arrow-right"></i>',
        'before_output' => '<nav aria-label="navigation"><ul class="pagination">',
        'after_output' => '</ul></nav>',
        'posts_per_page' => 6,
        'query_string' => '',
        'current_url' => '',
        'page' => 1,
        'query_page' => true,
        'slug' => '',
        'type' => ''
    ];

    if ($comment) {
        $defaults['posts_per_page'] = comments_per_page();
    } else {
        $page_slug = 'page';
        $defaults['posts_per_page'] = posts_per_page();
    }

    $args = wp_parse_args($args, $defaults);

    if ($comment) {
        if ($args['type'] == 'home') {
            $page_slug = 'review_page';
        } else {
            $page_slug = 'comment_page';
        }
    }

    $query_page = $args['query_page'];

    $args['range'] = (int)$args['range'] - 1;
    $posts_per_page = $args['posts_per_page'];

    $count = ceil($args['total'] / $posts_per_page);

    $current_params = \Illuminate\Support\Facades\Route::current()->parameters();
    $totalParams = count($current_params);
    if (isset($args['slug']) && !empty($args['slug']) && isset($current_params[$args['slug']])) {
        $totalParams++;
    }

    if (isset($current_params['page'])) {
        $page = $current_params['page'];
    } else {
        $page = $args['page'];
    }

    $ceil = ceil($args['range'] / 2);
    if ($count <= 1)
        return false;
    if ($count > $args['range']) {
        if ($page <= $args['range']) {
            $min = 1;
            $max = $args['range'] + 1;
        } elseif ($page >= ($count - $ceil)) {
            $min = $count - $args['range'];
            $max = $count;
        } elseif ($page >= $args['range'] && $page < ($count - $ceil)) {
            $min = $page - $ceil;
            $max = $page + $ceil;
        }
    } else {
        $min = 1;
        $max = $count;
    }
    $echo = '';

    $paramTemp = '';
    if (empty($args['query_string'])) {
        $current_params = \Illuminate\Support\Facades\Route::current()->parameters();
        if (!empty($current_params)) {
            $paramTemp = '/' . implode('/', $current_params);
        }
        if (!$comment) {
            $query_str = \Request::getRequestUri();
            if (strpos($query_str, '?')) {
                $query_str = substr($query_str, strpos($query_str, '?'), strlen($query_str));
            } else {
                $query_str = '';
            }
        } else {
            $query_str = $paramTemp;
        }
    } else {
        $query_str = $args['query_string'];
    }

    if (!empty($args['current_url'])) {
        $url = $args['current_url'];
    } else {
        $url = url(Route::currentRouteName());
    }

    $previous = $url;

    if ($previous && (1 == $page)) {
        $echo .= '<li class="disabled"><a class="page-link previous" href="javascript:void(0);" title="previous">' . $args['previous_string'] . '</a></li>';
    }
    if ($previous && (1 != $page)) {
        if (!$query_page) {
            $previous_num = intval($page) - 1;
            $query_str_temp = '';
            switch ($totalParams) {
                case 0:
                case 1:
                    $previous = $url . '/' . $previous_num . '/';
                    break;
                case 2:
                case 3:
                    $previous = $url . '/' . $current_params[$args['slug']] . '/' . $previous_num . '/';
                    break;
            }
        } else {
            $query_str_temp = $query_str;
            $previous_num = intval($page) - 1;
            if (strpos($query_str, '?')) {
                $query_str_temp .= '&' . $page_slug . '=' . $previous_num;
            } else {
                $query_str_temp .= '?' . $page_slug . '=' . $previous_num;
            }
        }
        $echo .= '<li class="page-item"><a class="page-link previous" data-pagination="' . $previous_num . '" href="' . $previous . $query_str_temp . '" title="previous">' . $args['previous_string'] . '</a></li>';
    }
    if (!empty($min) && !empty($max)) {
        for ($i = $min; $i <= $max; $i++) {
            if ($page == $i) {
                $echo .= '<li class="active page-item"><a class="page-link" data-pagination="' . $i . '" href="javascript:void(0);">' . str_pad((int)$i, 1, '0', STR_PAD_LEFT) . '</a></li>';
            } else {
                $_url = $url;
                if (!$query_page) {
                    $query_str_temp = '';
                    switch ($totalParams) {
                        case 0:
                        case 1:
                            $_url = $url . '/' . $i . '/';
                            break;
                        case 2:
                        case 3:
                            $_url = $url . '/' . $current_params[$args['slug']] . '/' . $i . '/';
                            break;
                    }
                } else {
                    $query_str_temp = $query_str;
                    if (strpos($query_str, '?')) {
                        $query_str_temp .= '&' . $page_slug . '=' . $i;
                    } else {
                        $query_str_temp .= '?' . $page_slug . '=' . $i;
                    }
                }
                $echo .= sprintf('<li class="page-item"><a class="page-link" data-pagination="' . $i . '" href="%s">%2d</a></li>', $_url . $query_str_temp, $i);
            }
        }
    }

    $next = $url;

    if ($next && ($count == $page)) {
        $echo .= '<li class="disabled"><a class="page-link next" href="javascript:void(0);" title="next">' . $args['next_string'] . '</a></li>';
    }
    if ($next && ($count != $page)) {
        if (!$query_page) {
            $query_str_temp = '';
            $next_num = intval($page) + 1;
            switch ($totalParams) {
                case 0:
                case 1:
                    $next = $url . '/' . $next_num . '/';
                    break;
                case 2:
                case 3:
                    $next = $url . '/' . $current_params[$args['slug']] . '/' . $next_num . '/';
                    break;
            }
        } else {
            $query_str_temp = $query_str;
            $next_num = intval($page) + 1;
            if (strpos($query_str, '?')) {
                $query_str_temp .= '&' . $page_slug . '=' . $next_num;
            } else {
                $query_str_temp .= '?' . $page_slug . '=' . $next_num;
            }
        }
        $echo .= '<li class="page-item"><a class="page-link next" data-pagination="' . $next_num . '" href="' . $next . $query_str_temp . '" title="next">' . $args['next_string'] . '</a></li>';
    }
    if (isset($echo))
        echo balanceTags($args['before_output'] . $echo . $args['after_output']);
}

function get_time_since($older_date, $newer_date = false)
{
    $unknown_text = 'sometime';
    $right_now_text = 'right now';
    $ago_text = '%s ago';
    $chunks = [
        [60 * 60 * 24 * 365, 'year', 'years'],
        [60 * 60 * 24 * 30, 'month', 'months'],
        [60 * 60 * 24 * 7, 'week', 'weeks'],
        [60 * 60 * 24, 'day', 'days'],
        [60 * 60, 'hour', 'hours'],
        [60, 'minute', 'minutes'],
        [1, 'second', 'seconds']
    ];
    if (!empty($older_date) && !is_numeric($older_date)) {
        $time_chunks = explode(':', str_replace(' ', ':', $older_date));
        $date_chunks = explode('-', str_replace(' ', '-', $older_date));
        $older_date = gmmktime((int)$time_chunks[1], (int)$time_chunks[2], (int)$time_chunks[3], (int)$date_chunks[1], (int)$date_chunks[2], (int)$date_chunks[0]);
    }
    $newer_date = (!$newer_date) ? time() : $newer_date;
    $since = $newer_date - $older_date;

    if (0 > $since) {
        $output = $unknown_text;
    } else {
        for ($i = 0, $j = count($chunks); $i < $j; ++$i) {
            $seconds = $chunks[$i][0];
            $count = floor($since / $seconds);
            if (0 != $count) {
                break;
            }
        }

        if (!isset($chunks[$i])) {
            $output = $right_now_text;
        } else {
            $output = (1 == $count) ? '1 ' . $chunks[$i][1] : $count . ' ' . $chunks[$i][2];
            if ($i + 2 < $j) {
                $seconds2 = $chunks[$i + 1][0];
                $name2 = $chunks[$i + 1][1];
                $count2 = floor(($since - ($seconds * $count)) / $seconds2);

                if (0 != $count2) {
                    $output .= (1 == $count2) ? ',' . ' 1 ' . $name2 : ',' . ' ' . $count2 . ' ' . $chunks[$i + 1][2];
                }
            }
            if (!(int)trim($output)) {
                $output = $right_now_text;
            }
        }
    }

    if ($output != $right_now_text) {
        $output = sprintf($ago_text, $output);
    }

    return $output;
}

function get_country_by_code($code)
{
    $countries = list_countries();
    return (isset($countries[$code])) ? $countries[$code] : ['name' => '', 'flag' => ''];
}

function list_countries($key = null)
{
    $countries = [];
    $countries_data = file_get_contents(public_path('vendors/countries/countries.json'));
    $countries_data = json_decode($countries_data, true);
    if (!empty($countries_data) && is_array($countries_data)) {
        foreach ($countries_data as $country) {
            $countries[$country['id']] = [
                'name' => $country['name'],
                'flag128' => '<img class="mr-1" src="' . asset('vendors/countries/flag/128x128/' . $country['alpha2'] . '.png') . '">',
                'flag64' => '<img class="mr-1" src="' . asset('vendors/countries/flag/64x64/' . $country['alpha2'] . '.png') . '">',
                'flag48' => '<img class="mr-1" src="' . asset('vendors/countries/flag/48x48/' . $country['alpha2'] . '.png') . '">',
                'flag32' => '<img class="mr-1" src="' . asset('vendors/countries/flag/32x32/' . $country['alpha2'] . '.png') . '">',
                'flag24' => '<img class="mr-1" src="' . asset('vendors/countries/flag/24x24/' . $country['alpha2'] . '.png') . '">',
                'flag16' => '<img class="mr-1" src="' . asset('vendors/countries/flag/16x16/' . $country['alpha2'] . '.png') . '">',
            ];
        }
    }

    if ($key) {
        return (isset($countries[$key])) ? $countries[$key] : ['name' => '', 'flag' => ''];
    }
    return $countries;
}

function is_email($email)
{

    if (strlen($email) < 6) {
        return false;
    }

    if (strpos($email, '@', 1) === false) {
        return false;
    }

    list($local, $domain) = explode('@', $email, 2);

    if (!preg_match('/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]+$/', $local)) {
        return false;
    }

    if (preg_match('/\.{2,}/', $domain)) {
        return false;
    }

    if (trim($domain, " \t\n\r\0\x0B.") !== $domain) {
        return false;
    }

    $subs = explode('.', $domain);

    if (2 > count($subs)) {
        return false;
    }

    foreach ($subs as $sub) {
        if (trim($sub, " \t\n\r\0\x0B-") !== $sub) {
            return false;
        }

        if (!preg_match('/^[a-z0-9-]+$/i', $sub)) {
            return false;
        }
    }

    return $email;
}

function esc_html($text)
{
    $text = trim($text);
    $safe_text = _check_invalid_utf8($text);
    $safe_text = _specialchars($safe_text, ENT_QUOTES);
    return $safe_text;
}

function esc_attr($text)
{
    $safe_text = _check_invalid_utf8($text);
    $safe_text = _specialchars($safe_text, ENT_QUOTES);
    return $safe_text;
}

function esc_sql($text)
{
    return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $text);
}

function _check_invalid_utf8($string, $strip = false)
{
    $string = (string)$string;

    if (0 === strlen($string)) {
        return '';
    }

    // Store the site charset as a static to avoid multiple calls to get_option()
    static $is_utf8 = null;
    if (!isset($is_utf8)) {
        $is_utf8 = in_array('utf-8', array('utf8', 'utf-8', 'UTF8', 'UTF-8'));
    }
    if (!$is_utf8) {
        return $string;
    }

    // Check for support for utf8 in the installed PCRE library once and store the result in a static
    static $utf8_pcre = null;
    if (!isset($utf8_pcre)) {
        $utf8_pcre = @preg_match('/^./u', 'a');
    }
    // We can't demand utf8 in the PCRE installation, so just return the string in those cases
    if (!$utf8_pcre) {
        return $string;
    }

    // preg_match fails when it encounters invalid UTF8 in $string
    if (1 === @preg_match('/^./us', $string)) {
        return $string;
    }

    // Attempt to strip the bad chars if requested (not recommended)
    if ($strip && function_exists('iconv')) {
        return iconv('utf-8', 'utf-8', $string);
    }

    return '';
}

function _specialchars($string, $quote_style = ENT_NOQUOTES, $charset = false, $double_encode = false)
{
    $string = (string)$string;

    if (0 === strlen($string)) {
        return '';
    }

    // Don't bother if there are no specialchars - saves some processing
    if (!preg_match('/[&<>"\']/', $string)) {
        return $string;
    }

    // Account for the previous behaviour of the function when the $quote_style is not an accepted value
    if (empty($quote_style)) {
        $quote_style = ENT_NOQUOTES;
    } elseif (!in_array($quote_style, array(0, 2, 3, 'single', 'double'), true)) {
        $quote_style = ENT_QUOTES;
    }

    // Store the site charset as a static to avoid multiple calls to wp_load_alloptions()
    if (!$charset) {
        static $_charset = null;
        if (!isset($_charset)) {
            $alloptions = [];
            $_charset = isset($alloptions['blog_charset']) ? $alloptions['blog_charset'] : '';
        }
        $charset = $_charset;
    }

    if (in_array($charset, array('utf8', 'utf-8', 'UTF8'))) {
        $charset = 'UTF-8';
    }

    $_quote_style = $quote_style;

    if ($quote_style === 'double') {
        $quote_style = ENT_COMPAT;
        $_quote_style = ENT_COMPAT;
    } elseif ($quote_style === 'single') {
        $quote_style = ENT_NOQUOTES;
    }

    if (!$double_encode) {
        // Guarantee every &entity; is valid, convert &garbage; into &amp;garbage;
        // This is required for PHP < 5.4.0 because ENT_HTML401 flag is unavailable.
        $string = kses_normalize_entities($string);
    }

    $string = @htmlspecialchars($string, $quote_style, $charset, $double_encode);

    // Back-compat.
    if ('single' === $_quote_style) {
        $string = str_replace("'", '&#039;', $string);
    }

    return $string;
}

function kses_normalize_entities($string)
{
    // Disarm all entities by converting & to &amp;
    $string = str_replace('&', '&amp;', $string);

    // Change back the allowed entities in our entity whitelist
    $string = preg_replace_callback('/&amp;([A-Za-z]{2,8}[0-9]{0,2});/', 'kses_named_entities', $string);
    $string = preg_replace_callback('/&amp;#(0*[0-9]{1,7});/', 'kses_normalize_entities2', $string);
    $string = preg_replace_callback('/&amp;#[Xx](0*[0-9A-Fa-f]{1,6});/', 'kses_normalize_entities3', $string);

    return $string;
}

function kses_named_entities($matches)
{
    global $allowedentitynames;

    if (empty($matches[1]) && is_array($matches[1])) {
        return '';
    }

    $i = $matches[1];

    if (is_array($allowedentitynames)) {
        return (!in_array($i, $allowedentitynames)) ? "&amp;$i;" : "&$i;";
    } else {
        return '';
    }
}

function kses_normalize_entities2($matches)
{
    if (empty($matches[1])) {
        return '';
    }

    $i = $matches[1];
    if (valid_unicode($i)) {
        $i = str_pad(ltrim($i, '0'), 3, '0', STR_PAD_LEFT);
        $i = "&#$i;";
    } else {
        $i = "&amp;#$i;";
    }

    return $i;
}

function kses_normalize_entities3($matches)
{
    if (empty($matches[1])) {
        return '';
    }

    $hexchars = $matches[1];
    return (!valid_unicode(hexdec($hexchars))) ? "&amp;#x$hexchars;" : '&#x' . ltrim($hexchars, '0') . ';';
}

function valid_unicode($i)
{
    return ($i == 0x9 || $i == 0xa || $i == 0xd ||
        ($i >= 0x20 && $i <= 0xd7ff) ||
        ($i >= 0xe000 && $i <= 0xfffd) ||
        ($i >= 0x10000 && $i <= 0x10ffff));
}


function d($arr)
{
    echo '<pre style="background: #000; padding: 20px; color: #fff;">';
    print_r($arr);
    echo '</pre>';
}

