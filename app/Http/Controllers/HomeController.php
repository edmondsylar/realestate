<?php

namespace App\Http\Controllers;

use App\HomeAvailability;
use Illuminate\Http\Request;
use App\Home;
use App\TermRelation;
use App\CustomPrice;
use App\Comment;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\In;
use Sentinel;
use Illuminate\Support\Facades\Input;

class HomeController extends Controller
{
    public function __construct()
    {
        add_action('hh_dashboard_breadcrumb', [$this, '_addCreateHomeButton']);
    }

    public function _sendEnquiryForm(Request $request)
    {
        $name = Input::get('name');
        $email = Input::get('email');
        $message = Input::get('message');

        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                'email' => 'required|email',
                'message' => 'required|min:10'
            ],
            [
                'name.required' => __('Name is required'),
                'email.required' => __('Email is required'),
                'email.email' => __('This email is incorrect'),
                'message.required' => __('Message is required')
            ]
        );
        if ($validator->fails()) {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'danger', 'message' => $validator->errors()->first()])->render()
            ]);
        }
        $post_id = Input::get('post_id');
        $post_encrypt = Input::get('post_encrypt');
        if (!hh_compare_encrypt($post_id, $post_encrypt)) {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'danger', 'message' => __('This service is invalid')])->render()
            ]);
        }
        $post = get_post($post_id, 'home');
        $admin = get_admin_user();
        $partner = get_user_by_id($post->author);

        send_mail(esc_html($email), esc_html($name), $admin->email, sprintf(__('[%s] Have a booking request from [%s] - %s'), get_option('site_name'), $post_id, $post->post_title), balanceTags($message));
        send_mail(esc_html($email), esc_html($name), $partner->email, sprintf(__('[%s] Have a booking request from [%s] - %s'), get_option('site_name'), $post_id, $post->post_title), balanceTags($message));
        return $this->sendJson([
            'status' => 1,
            'message' => view('common.alert', ['type' => 'success', 'message' => __('Sent! Please wait for a response from the partner')])->render()
        ]);
    }

    public function _getHomeNearYouAjax(Request $request)
    {
        $lat = Input::get('lat');
        $lng = Input::get('lng');
        $radius = Input::get('radius', 50);
        $html = '';
        if ($lat && $lng) {
            $list_services = $this->listOfHomes([
                'number' => 8,
                'location' => [
                    'lat' => $lat,
                    'lng' => $lng,
                    'radius' => $radius
                ]
            ]);

            if (count($list_services['results'])) {
                start_get_view();
                ?>
                <div class="hh-list-of-services">
                    <div class="row">
                        <?php foreach ($list_services['results'] as $item) { ?>
                            <div class="col-6 col-md-4 col-lg-3">
                                <?php echo view('frontend.home.loop.grid', ['item' => $item])->render() ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php
                $html = end_get_view();
            } else {
                $html = '<h4 class="mt-3 text-center">'.__('Not found').'</h4>';
            }
        }

        return $this->sendJson([
            'html' => $html
        ]);
    }

    public function _getLatestHomeAjax(Request $request)
    {
        $html = '';
        $list_services = $this->listOfHomes([
            'number' => 8,
        ]);

        if (count($list_services['results'])) {
            start_get_view();
            ?>
            <div class="hh-list-of-services">
                <div class="row">
                    <?php foreach ($list_services['results'] as $item) { ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <?php echo view('frontend.home.loop.grid', ['item' => $item])->render() ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php
            $html = end_get_view();
        } else {
            $html = '<h4 class="mt-3 text-center">'.__('Not found').'</h4>';
        }


        return $this->sendJson([
            'html' => $html
        ]);
    }

    public function _homeReview($page = 1)
    {
        $folder = $this->getFolder();

        $search = Input::get('_s');
        $status = Input::get('status', '');

        $comment_obj = new Comment();
        $data = [
            'type' => 'home',
            'search' => $search,
            'page' => $page,
            'status' => $status
        ];
        if (!is_admin()) {
            $data['author'] = get_current_user_id();
        }
        $comments = $comment_obj->getAllComments($data);

        return view("dashboard.screens.{$folder}.home-review", ['role' => $folder, 'bodyClass' => 'hh-dashboard', 'comments' => $comments]);
    }

    public function _addCreateHomeButton()
    {
        $screen = current_screen();
        if ($screen == 'my-home') {
            echo view('dashboard.components.quick-add-home')->render();
        }
    }

    public function listOfHomes($data = [])
    {
        $home = new Home();
        return $home->listOfHomes($data);
    }

    public function getAllHomes($data = [])
    {
        $home = new Home();
        return $home->getAllHomes($data);
    }

    public function getById($home_id)
    {
        $home_object = new Home();

        global $post, $old_post;

        if (!is_null($post)) {
            if (isset($post->post_id) && $post->post_id == $home_id) {
                return $post;
            } else {
                $old_post = $post;
                $post = $home_object->getById($home_id);
            }
        } else {
            $post = $home_object->getById($home_id);
        }

        $this->_storeData();

        return $post;
    }

    public function getByName($home_name)
    {
        $home_object = new Home();

        global $post, $old_post;
        if (!is_null($post)) {
            if (isset($post->post_slug) && $post->post_slug == $home_name) {
                return $post;
            } else {
                $old_post = $post;
                $post = $home_object->getByName($home_name);
            }
        } else {
            $post = $home_object->getByName($home_name);
        }

        $this->_storeData();

        return $post;
    }


    private function _storeData()
    {
        global $post;
        $term_relation_object = new TermRelation();

        $post->review_count = get_comment_number($post->post_id, 'home');

        $tax = get_taxonomies();
        foreach($tax as $key => $tax_name){
            $name = 'tax_'.Str::slug($tax_name, '_');
            $post->$name = $term_relation_object->get_the_terms($post->post_id, $key);
        }

        $post->extra = $this->getExtraServices();
        $post->required_extra = $this->getExtraServices('required');
        $post->not_required_extra = $this->getExtraServices('not_required');

        $post->unit = get_home_booking_type($post);
    }

    public function getMinMaxPrice()
    {
        $home_model = new Home();
        $minMaxPrice = $home_model->getMinMaxPrice();
        if (!isset($minMaxPrice['min_price']) || empty($minMaxPrice['min_price'])) {
            $minMaxPrice['min_price'] = 0;
        }
        if (!isset($minMaxPrice['max_price'])) {
            $minMaxPrice['max_price'] = 500;
        }

        return $minMaxPrice;
    }

    public function getExtraServices($select = 'all')
    {
        global $post;

        $return = [];
        $extra = maybe_unserialize($post->extra_services);
        if (!empty($extra) && is_array($extra)) {
            foreach ($extra as $key => $value) {
                if ($select == 'required' && (isset($value['required']) && $value['required'])) {
                    $return[] = $value;
                }
                if ($select == 'not_required' && (!isset($value['required']) || !$value['required'])) {
                    $return[] = $value;
                }
            }
            return $return;
        }
        return false;
    }

    public function getRealPrice($startTime, $endTime)
    {
        global $post;
        $price_model = new CustomPrice();
        $price = $post->base_price;
        $weekendPrice = $post->weekend_price;
        $weekendApply = $post->weekend_to_apply;
        $customPrice = $price_model->getPriceItems($post->post_id, $startTime, $endTime);
        $total = 0;
        for ($i = $startTime; $i < $endTime; $i = strtotime('+1 day', $i)) {
            $inCustom = false;
            foreach ($customPrice['results'] as $item) {
                if ($i >= $item->start_time && $i <= $item->end_time) {
                    $total += (float)$item->price;
                    $inCustom = true;
                    break;
                }
            }
            if (!$inCustom) {
                if (is_weekend($i, $weekendApply)) {
                    $total += is_null($weekendPrice) ? $price : (float)$weekendPrice;
                } else {
                    $total += $price;
                }
            }
        }

        return $total;
    }

    public function getRealPriceByTime($startTime, $endTime)
    {
        global $post;
        $price_model = new CustomPrice();

        $price = $post->base_price;
        $weekendPrice = $post->weekend_price;
        $weekendApply = $post->weekend_to_apply;

        $totalHour = ceil(hh_date_diff($startTime, $endTime, 'minute') / 60);

        $start_date = strtotime(date('Y-m-d', $startTime));

        $customPrice = $price_model->getPriceItems($post->post_id, $startTime, $endTime);
        $total = 0;
        $inCustom = false;
        foreach ($customPrice['results'] as $item) {
            if ($start_date >= $item->start_time && $start_date <= $item->end_time) {
                $total += (float)$item->price;
                $inCustom = true;
                break;
            }
        }
        if (!$inCustom) {
            if (is_weekend($start_date, $weekendApply)) {
                $total += is_null($weekendPrice) ? $price : (float)$weekendPrice;
            } else {
                $total += $price;
            }
        }
        $total *= $totalHour;

        return $total;
    }

    public function getRequiredExtraPrice($night = 1)
    {
        $extras = $this->getExtraServices('required');
        $total = 0;
        if ($extras) {
            foreach ($extras as $extra) {
                $total += (float)$extra['price'];
            }
        }
        $total *= $night;

        return $total;
    }

    public function getExtraPrice($extraParams = [], $night = 1)
    {
        $extras = $this->getExtraServices('not_required');
        $total = 0;
        if (!empty($extraParams)) {
            foreach ($extraParams as $extra) {
                foreach ($extras as $_extra) {
                    if ($extra === $_extra['name_unique']) {
                        $total += (float)$_extra['price'];
                    }
                }
            }
            $total *= $night;
        }

        return $total;
    }

    public function _addToCartHome(Request $request)
    {
        $homeID = (int)Input::get('homeID');
        $homeEncrypt = Input::get('homeEncrypt');

        $number_adult = (int)Input::get('num_adults', 1);
        $number_child = (int)Input::get('num_children');
        $number_infant = (int)Input::get('num_infants');
        $startDate = strtotime(Input::get('checkIn'));
        $startTime = Input::get('startTime');
        $endDate = strtotime(Input::get('checkOut'));
        $endTime = Input::get('endTime');
        $extraParams = Input::get('extraServices');
        $numberNight = hh_date_diff($startDate, $endDate);
        $numberGuest = $number_adult + $number_child;
        if (hh_compare_encrypt($homeID, $homeEncrypt) && $startDate && $endDate) {
            $homeObject = $this->getById($homeID);
            $data = [
                'homeID' => $homeID,
                'numberAdult' => $number_adult,
                'numberChild' => $number_child,
                'numberInfant' => $number_infant,
                'minStay' => $homeObject->min_stay,
                'maxStay' => $homeObject->max_stay,
                'guest' => $homeObject->number_of_guest,
                'startDate' => $startDate,
                'startTime' => strtotime(date('Y-m-d', $startDate) . ' ' . $startTime),
                'endDate' => $endDate,
                'endTime' => strtotime(date('Y-m-d', $endDate) . ' ' . $endTime),
                'bookingType' => $homeObject->booking_type
            ];

            $checkAvailability = $this->homeValidation($data);
            if ($checkAvailability['status'] == 0) {
                $this->sendJson([
                    'status' => 0,
                    'message' => view('common.alert', ['type' => 'danger', 'message' => $checkAvailability['message']])->render()
                ], true);
            } else {
                $bookingType = $homeObject->booking_type;

                if ($bookingType == 'per_night') {
                    $basePrice = $this->getRealPrice($startDate, $endDate);
                    $requiredExtra = $this->getRequiredExtraPrice($numberNight);
                    $extra = $this->getExtraPrice($extraParams, $numberNight);
                } elseif ($bookingType == 'per_hour') {
                    $numberNight = ceil(hh_date_diff($data['startTime'], $data['endTime'], 'minute') / 60);
                    $basePrice = $this->getRealPriceByTime($data['startTime'], $data['endTime']);
                    $requiredExtra = $this->getRequiredExtraPrice($numberNight);
                    $extra = $this->getExtraPrice($extraParams, $numberNight);
                }

                $rules = [
                    [
                        'unit' => '+',
                        'price' => $basePrice
                    ],
                    [
                        'unit' => '+',
                        'price' => $requiredExtra
                    ],
                    [
                        'unit' => '+',
                        'price' => $extra
                    ],
                ];
                $taxRule = [];

                $taxData = \CartHome::get_inst()->getTax();

                if ($taxData['included'] == 'off') {
                    $taxRule = [
                        [
                            'unit' => 'tax',
                            'price' => $taxData['tax']
                        ]
                    ];
                }

                $data['numberNight'] = $numberNight;
                $data['numberGuest'] = $numberGuest;
                $totalData = \CartHome::get_inst()->totalCalculation($rules, $taxRule);

                $cartData = [
                    'serviceID' => $homeID,
                    'serviceObject' => serialize($homeObject),
                    'basePrice' => $basePrice,
                    'extraPrice' => $requiredExtra + $extra,
                    'subTotal' => $totalData['subTotal'],
                    'tax' => $taxData,
                    'amount' => $totalData['amount'],
                    'cartData' => $data,
                ];

                $cartData = apply_filters('hh_cart_data_before_add_to_cart', $cartData);

                \CartHome::get_inst()->setCart($cartData);

                return $this->sendJson(array(
                    'status' => true,
                    'redirect' => checkout_url()
                ));
            }
        } else {
            $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'warning', 'message' => __('The data is invalid')])->render()
            ], true);
        }
    }

    public function homeValidation($data)
    {
        $default = [
            'homeID' => '',
            'numberAdult' => 0,
            'numberChild' => 0,
            'numberInfant' => 0,
            'minStay' => 1,
            'maxStay' => -1,
            'guest' => 1,
            'startDate' => '',
            'startTime' => '',
            'endDate' => '',
            'endTime' => '',
            'bookingType' => ''
        ];
        $data = wp_parse_args($data, $default);

        $homeObject = $this->getById($data['homeID']);
        if (is_null($homeObject)) {
            return [
                'status' => 0,
                'message' => __('This home is not available')
            ];
        }
        if ($data['numberAdult'] + $data['numberChild'] > $data['guest']) {
            return [
                'status' => 0,
                'message' => sprintf(__('The maximum number of people is %s'), $data['guest'])
            ];
        }

        if ($data['bookingType'] == 'per_hour') {
            if (hh_date_diff($data['startTime'], $data['endTime'], 'hour') < $data['minStay']) {
                return [
                    'status' => 0,
                    'message' => _n(__('The min stay is %s hour'), __('The min stay is %s hours'), $data['minStay'])
                ];
            }
            if (hh_date_diff($data['startTime'], $data['endTime'], 'hour') > $data['maxStay'] && $data['maxStay'] != -1) {
                return [
                    'status' => 0,
                    'message' => _n(__('The max stay is %s hour'), __('The max stay is %s hours'), $data['minStay'])
                ];
            }
        } elseif ($data['bookingType'] == 'per_night') {

            if (hh_date_diff($data['startDate'], $data['endDate']) < $data['minStay']) {
                return [
                    'status' => 0,
                    'message' => _n(__('The min stay is %s day'), __('The min stay is %s days'), $data['minStay'])
                ];
            }
            if (hh_date_diff($data['startDate'], $data['endDate']) > $data['maxStay'] && $data['maxStay'] != -1) {
                return [
                    'status' => 0,
                    'message' => _n(__('The max stay is %s day'), __('The max stay is %s days'), $data['minStay'])
                ];
            }
        }
        $avai_model = new HomeAvailability();

        if ($data['bookingType'] == 'per_night') {
            $avai = $avai_model->getAvailabilityItems($data['homeID'], $data['startDate'], $data['endDate']);

            if ($avai['total'] > 0) {
                $status = true;
                for ($i = $data['startDate']; $i <= $data['endDate']; $i = strtotime('+1 day', $i)) {
                    foreach ($avai['results'] as $item) {
                        if ($i >= $item->start_date && $i <= $item->end_date) {
                            if ($item->booking_id == 0 && $item->total_minutes == 1440) {
                                $status = false;
                                break;
                            } else {
                                if ($i == $item->start_date && $item->start_date == $item->end_date) {
                                    $status = false;
                                    break;
                                }
                                if ($i < $data['endDate'] && $i == $item->start_date && $item->start_date < $item->end_date) {
                                    $status = false;
                                    break;
                                }
                                if ($i > $item->start_date && $i < $item->end_date) {
                                    $status = false;
                                    break;
                                }
                            }
                        }
                    }
                    if (!$status) {
                        break;
                    }
                }
                if (!$status) {
                    return [
                        'status' => 0,
                        'message' => __('This date range is not available')
                    ];
                }
            }
        } elseif ($data['bookingType'] == 'per_hour') {
            if (is_timestamp($data['startTime']) && is_timestamp($data['endTime']) && $data['startTime'] < $data['endTime']) {
                $avai = $avai_model->getAvailabilityTimeItems($data['homeID'], $data['startDate'], $data['endDate'], false);
                if ($avai['total'] > 0) {
                    $status = true;
                    for ($i = $data['startTime']; $i <= $data['endTime']; $i = strtotime('+1 minute', $i)) {
                        foreach ($avai['results'] as $item) {
                            if ($i >= $item->start_time && $i <= $item->end_time) {
                                if ($item->booking_id == 0 && $item->total_minutes == 1440) {
                                    $status = false;
                                    break;
                                } else {
                                    if ($i == $item->start_time && $item->start_time == $item->end_time) {
                                        $status = false;
                                        break;
                                    }
                                    if ($i < $data['endTime'] && $i == $item->start_time && $item->start_time < $item->end_time) {
                                        $status = false;
                                        break;
                                    }
                                    if ($i > $item->start_time && $i < $item->end_time) {
                                        $status = false;
                                        break;
                                    }
                                }
                            }
                        }
                        if (!$status) {
                            break;
                        }
                    }
                    if (!$status) {
                        return [
                            'status' => 0,
                            'message' => __('This date range is not available')
                        ];
                    }
                }
            } else {
                return [
                    'status' => 0,
                    'message' => __('Please select a valid datetime')
                ];
            }

        }


        return [
            'status' => 1,
            'message' => __('This date range is available')
        ];
    }

    public function _getHomePriceRealTime(Request $request)
    {
        $homeID = Input::get('homeID');
        $homeEncrypt = Input::get('homeEncrypt');

        $startDate = strtotime(Input::get('checkIn'));
        $endDate = strtotime(Input::get('checkOut'));

        $startTime = Input::get('startTime');
        $endTime = Input::get('endTime');

        $extraServices = Input::get('extraServices');
        $total = 0;
        if (hh_compare_encrypt($homeID, $homeEncrypt) && $startDate && $endDate) {
            $home = $this->getById($homeID);
            if ($home->booking_type == 'per_night') {
                $numberNight = hh_date_diff($startDate, $endDate);
                $price = $this->getRealPrice($startDate, $endDate);
                $requiredExtra = $this->getRequiredExtraPrice($numberNight);
                $extra = $this->getExtraPrice($extraServices, $numberNight);
                $total = $price + $requiredExtra + $extra;
            } elseif ($home->booking_type == 'per_hour') {
                $startTime = strtotime(date('Y-m-d', $startDate) . ' ' . $startTime);
                $endTime = strtotime(date('Y-m-d', $endDate) . ' ' . $endTime);
                if (is_timestamp($startTime) && is_timestamp($endTime) && $startTime < $endTime) {
                    $numberNight = hh_date_diff($startTime, $endTime, 'hour');
                    $price = $this->getRealPriceByTime($startTime, $endTime);
                    $requiredExtra = $this->getRequiredExtraPrice($numberNight);
                    $extra = $this->getExtraPrice($extraServices, $numberNight);
                    $total = $price + $requiredExtra + $extra;
                } else {
                    $this->sendJson([
                        'status' => 0,
                        'html' => '',
                        'message' => __('The data is invalid')
                    ], true);
                }
            };
            $this->sendJson([
                'status' => 1,
                'html' => view('frontend.home.calculate-price-render', ['total' => $total])->render()
            ], true);
        } else {
            $this->sendJson([
                'status' => 0,
                'html' => '',
                'message' => __('The data is invalid')
            ], true);
        }
    }

    public function _getHomeAvailabilitySingle(Request $request)
    {
        $events['events'] = [];
        $startTime = strtotime(Input::get('startTime'));
        $endTime = strtotime(Input::get('endTime'));
        $homeID = Input::get('homeID');
        $homeEncrypt = Input::get('homeEncrypt');

        if ($startTime && $endTime && hh_compare_encrypt($homeID, $homeEncrypt)) {
            $price_model = new CustomPrice();
            $avai_model = new HomeAvailability();
            $priceItems = $price_model->getPriceItems($homeID, $startTime, $endTime, $status = 'on');
            $homeObject = $this->getById($homeID);

            $price = (float)$homeObject->base_price;
            $wprice = $homeObject->weekend_price;
            $ruleWeekend = $homeObject->weekend_to_apply;
            if ($homeObject->booking_type == 'per_night') {
                $avaiItems = $avai_model->getAvailabilityItems($homeID, $startTime, $endTime);
                for ($i = $startTime; $i <= $endTime; $i = strtotime('+1 day', $i)) {
                    $status = 'available';
                    $event = convert_price($price);
                    $inCustom = false;
                    foreach ($avaiItems['results'] as $avaiItem) {
                        if ($i >= $avaiItem->start_date && $i <= $avaiItem->end_date) {
                            if ($avaiItem->booking_id == 0 && $avaiItem->total_minutes == 1440) {
                                $status = 'not_available';
                                $event = 'Unavailable';
                                break;
                            } else {
                                if ($i == $avaiItem->start_date && $avaiItem->start_date == $avaiItem->end_date) {
                                    $status = 'booked';
                                    $event = __('Booked');
                                    break;
                                }
                                if ($i == $avaiItem->start_date && $avaiItem->start_date < $avaiItem->end_date) {
                                    $status = 'booked';
                                    $event = __('Booked');
                                    break;
                                }
                                if ($i > $avaiItem->start_date && $i < $avaiItem->end_date) {
                                    $status = 'booked';
                                    $event = __('Booked');
                                    break;
                                }
                            }
                        }
                    }
                    if ($status == 'available') {
                        foreach ($priceItems['results'] as $range) {
                            if ($i >= $range->start_time && $i <= $range->end_time) {
                                $event = convert_price($range->price);
                                $inCustom = true;
                                break;
                            }
                        }
                    }

                    if (!$inCustom) {
                        if (!is_null($wprice) && is_weekend($i, $ruleWeekend)) {
                            $event = convert_price($wprice);
                        }
                    }
                    $events['events'][] = [
                        'start' => date('Y-m-d', $i),
                        'end' => date('Y-m-d', $i),
                        'status' => $status,
                        'event' => $event
                    ];
                }
            } elseif ($homeObject->booking_type == 'per_hour') {
                $avaiItems = $avai_model->getAvailabilityTimeItems($homeID, $startTime, $endTime);
                for ($i = $startTime; $i <= $endTime; $i = strtotime('+1 day', $i)) {
                    $status = 'available';
                    $event = convert_price($price);
                    $inCustom = false;
                    foreach ($avaiItems['results'] as $item) {
                        if ($i >= $item->start_time && $i <= $item->end_time) {
                            if ((int)$item->total >= 1440) {
                                $status = ($item->has_booking > 0) ? 'booked' : 'not_available';
                                break;
                            }
                        }
                    }
                    if ($status == 'available') {
                        foreach ($priceItems['results'] as $range) {
                            if ($i >= $range->start_time && $i <= $range->end_time) {
                                $event = convert_price($range->price);
                                $inCustom = true;
                                break;
                            }
                        }
                        if (!$inCustom) {
                            if (!empty($wprice) && is_weekend($i, $ruleWeekend)) {
                                $event = convert_price($wprice);
                            }
                        }
                    } elseif ($status == 'booked') {
                        $event = __('Booked');
                    } else {
                        $event = __('Unavailable');
                    }
                    $events['events'][] = [
                        'start' => date('Y-m-d', $i),
                        'end' => date('Y-m-d', $i),
                        'status' => $status,
                        'event' => $event
                    ];
                }
            }

        }

        $this->sendJson($events, true);
    }

    public function _getHomeAvailabilityTimeSingle(Request $request)
    {
        $date = Input::get('start');
        $home_id = Input::get('home_id');

        if ($date && $home_id) {
            $avai_model = new HomeAvailability();
            $calendarItems = $avai_model->getAvailabilityItems($home_id, strtotime($date), strtotime($date));
            $times = list_hours(30);
            $result = $times;
            if ($calendarItems['total']) {
                foreach ($times as $key => $time) {
                    $timestamp = strtotime($date . ' ' . $key);
                    foreach ($calendarItems['results'] as $item) {
                        if ($timestamp >= $item->start_time && $timestamp <= $item->end_time) {
                            if ($item->booking_id == 0 && $item->total_minutes == 1440) {
                                unset($result[$key]);
                                break;
                            } else {
                                if ($timestamp == $item->start_time && $item->start_time == $item->end_time) {
                                    unset($result[$key]);
                                    break;
                                }
                                if ($timestamp == $item->start_time && $item->start_time < $item->end_time) {
                                    unset($result[$key]);
                                    break;
                                }
                                if ($timestamp > $item->start_time && $timestamp < $item->end_time) {
                                    unset($result[$key]);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            $return = '';
            foreach ($result as $key => $time) {
                $return .= '<div class="item" data-value="' . esc_attr($key) . '">' . esc_html($time) . '</div>';
            }
            return $this->sendJson([
                'status' => 1,
                'html' => $return
            ]);
        } else {
            return $this->sendJson([
                'status' => 0,
                'message' => view('common.alert', ['type' => 'danger', __('The data is invalid')])->render()
            ]);
        }
    }

    public function _getHomeSingle(Request $request, $home_name = null)
    {
        $homeObject = $this->getByName($home_name);

        if (is_null($homeObject) || !$homeObject || $homeObject->status != 'publish') {
            return view('frontend.404');
        } else {
            return view('frontend.home.default');
        }
    }

    public function _myHome(Request $request, $page = 1)
    {
        $folder = $this->getFolder();

        $search = Input::get('_s');
        $orderBy = Input::get('orderby', 'post_id');
        $order = Input::get('order', 'desc');
        $booking_type = Input::get('booking_type');
        $status = Input::get('status');

        $allHomes = $this->getAllHomes([
            'search' => $search,
            'orderby' => $orderBy,
            'order' => $order,
            'booking_type' => $booking_type,
            'status' => $status,
            'page' => $page
        ]);

        return view("dashboard.screens.{$folder}.my-home", ['role' => $folder, 'bodyClass' => 'hh-dashboard', 'allHomes' => $allHomes]);
    }

    public function _deleteHomeItem(Request $request)
    {
        $home_id = Input::get('serviceID');
        $home_encrypt = Input::get('serviceEncrypt');

        if (!hh_compare_encrypt($home_id, $home_encrypt)) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('The home is not exist')
            ], true);
        }

        $home_model = new Home();

        $delete = $home_model->deleteHomeItem($home_id);
        if ($delete) {
            $this->sendJson([
                'status' => 1,
                'title' => __('System Alert'),
                'message' => __('Successfully Deleted'),
                'reload' => true
            ], true);
        }

        $this->sendJson([
            'status' => 0,
            'title' => __('System Alert'),
            'message' => __('Has error when delete this home')
        ], true);
    }

    public function _changeStatusHome(Request $request)
    {
        $home_id = Input::get('serviceID');
        $home_encrypt = Input::get('serviceEncrypt');
        $status = Input::get('status', '');

        if (!hh_compare_encrypt($home_id, $home_encrypt) || !$status) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('The data is invalid')
            ], true);
        }

        $home_model = new Home();
        $updated = $home_model->updateStatus($home_id, $status);
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
                'message' => __('Have error when saving')
            ], true);
        }
    }

    public function _updateHome(Request $request)
    {
        $step = Input::get('step', 'next');
        $event = Input::get('option_event', 'button');
        $redirect = Input::get('redirect');
        $post_title_field = 'post_title';
        if(is_multi_language()) {
            $current_lang = get_current_language();
            $post_title_field .= '_' . $current_lang;
        }

        $fields = Input::get('currentOptions');
        $fields = unserialize(base64_decode($fields));
        if ($fields) {
            $postID = Input::get('postID');
            $postEncrypt = Input::get('postEncrypt');
            if (!hh_compare_encrypt($postID, $postEncrypt)) {
                $this->sendJson([
                    'status' => 0,
                    'title' => __('System Alert'),
                    'message' => __('Can not save meta for this service')
                ], true);
            }
            $data = [];
            foreach ($fields as $field) {
                $field = \ThemeOptions::mergeField($field);
                $value = \ThemeOptions::fetchField($field);
                if (!$field['excluded'] && !empty($value)) {
                    if ($field['field_type'] == 'meta') {
                        $data[$field['id']] = $value;
                    } elseif ($field['field_type'] == 'taxonomy') {
                        $value = (array)$value;
                        $taxonomy = explode(':', $field['choices'])[1];
                        $termRelation = new TermRelation();
                        $termRelation->deleteRelationByServiceID($postID, $taxonomy);
                        foreach ($value as $termID) {
                            $termRelation->createRelation($termID, $postID);
                        }
                        $data[$field['id']] = implode(',', $value);
                    } elseif ($field['field_type'] == 'location') {
                        if (is_array($value)) {
                            foreach ($value as $key => $_val) {
                                $data[$field['id'] . '_' . $key] = $_val;
                            }
                        }
                    }
                }
            }
            if (!empty($data)) {
                if (isset($_POST['post_slug']) && (!isset($data['post_slug']) || empty($data['post_slug']))) {
                    $data['post_slug'] = Str::slug(esc_html(Input::post($post_title_field, 'new-home' . time())));
                }
                $home = new Home();
                $home->updateHome($data, $postID);
            }
            do_action('hh_saved_service_meta', $postID);

            $respon = [
                'status' => 1,
                'title' => __('System Alert'),
                'message' => __('Saved Successful')
            ];
            if ($step == 'finish' && !empty($redirect) && $event != 'tab') {

                $respon['redirect'] = dashboard_url($redirect);
            }
            $this->sendJson($respon, true);
        }
        $this->sendJson([
            'status' => 0,
            'title' => __('System Alert'),
            'message' => __('Have error when saving data')
        ], true);
    }

    public function _editHome(Request $request, $home_id = null)
    {
        $folder = $this->getFolder();
        $newHome = get_post($home_id, 'home');

        return view("dashboard.screens.{$folder}.edit-home", ['role' => $folder, 'bodyClass' => 'hh-dashboard', 'newHome' => $newHome]);
    }

    public function _addNewHome(Request $request)
    {
        $folder = $this->getFolder();
        $home = new Home();
        $newHome = $home->createHome();
        return view("dashboard.screens.{$folder}.add-new-home", ['role' => $folder, 'bodyClass' => 'hh-dashboard', 'newHome' => $newHome]);
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

    public static function get_inst()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}
