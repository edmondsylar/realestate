@include('frontend.components.header')
<div class="hh-checkout-page pb-4">
    @include('frontend.components.breadcrumb', ['currentPage' => 'Checkout', 'inContainer' => true])
    <div class="container">
        @if ($cart)
            @php
                $homeID = $cart['serviceID'];
                $homeObject = unserialize($cart['serviceObject']);
            @endphp
            <div class="row">
                <div class="col-12 col-sm-4 order-sm-8">
                    <div class="checkout-sidebar mt-4">
                        <h3 class="heading">{{__('Your Item')}}</h3>
                        <div class="card-box mt-3 cart-information">
                            <div class="media service-detail d-flex align-items-center">
                                @php
                                    $thumbnail = get_attachment_url($homeObject->thumbnail_id, [400, 400])
                                @endphp
                                <img src="{{ $thumbnail }}" class="mr-3"
                                     alt="{{ get_attachment_alt($homeObject->thumbnail_id) }}">
                                <div class="media-body">
                                    <a target="_blank"
                                       href="{{ get_the_permalink($homeID) }}">{{ get_translate($homeObject->post_title) }}</a>
                                    @if ($address = get_translate($homeObject->location_address))
                                        <div class="desc mt-2">
                                            <i class="fe-map-pin mr-1"></i> {{ $address }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <h5 class="title">{{__('Detail')}}</h5>
                            <ul class="menu cart-list">
                                @php
                                    $checkIn = $cart['cartData']['startDate'];
                                    $checkOut = $cart['cartData']['endDate'];
                                    $startTime = $cart['cartData']['startTime'];
                                    $endTime = $cart['cartData']['endTime'];
                                    $adults = $cart['cartData']['numberAdult'];
                                    $children = $cart['cartData']['numberChild'];
                                    $infant = $cart['cartData']['numberInfant'];
                                @endphp
                                <li>
                                    @if($homeObject->booking_type == 'per_night')
                                        <span>{{__('Check In/Out')}}</span>
                                        <span>
                                        {{ date(hh_date_format(), $checkIn) }} <i class="fe-arrow-right ml-2 mr-2"></i> {{ date(hh_date_format(), $checkOut) }}
                                        </span>
                                    @elseif($homeObject->booking_type == 'per_hour')
                                        <span>{{__('Date')}}</span>
                                        <span>
                                        {{ date(hh_date_format(), $checkIn) }}
                                        </span>
                                    @endif
                                </li>
                                @if($homeObject->booking_type == 'per_hour')
                                    <li>
                                        <span>{{__('Time')}}</span>
                                        <span>
                                        {{ date(hh_time_format(), $startTime) }} <i
                                                    class="fe-arrow-right ml-2 mr-2"></i> {{ date(hh_time_format(), $endTime) }}
                                        </span>
                                    </li>
                                @endif
                                @if ($adults > 0)
                                    <li>
                                        <span>{{ _n(__('Adult'), __('Adults'), $adults) }}</span>
                                        <span> {{ $adults }}  </span>
                                    </li>
                                @endif
                                @if ($children > 0)
                                    <li>
                                        <span>{{ _n(__('Child'), __('Children'), $children) }}</span>
                                        <span>{{ $children }}</span>
                                    </li>
                                @endif
                                @if ($infant > 0)
                                    <li>
                                        <span>{{ _n(__('Infant'), __('Infants'), $infant) }}</span>
                                        <span>{{ $infant }}</span>
                                    </li>
                                @endif
                            </ul>
                            @php
                                $coupon = isset($cart['cartData']['coupon']) ? $cart['cartData']['coupon'] : [];
                                $couponCode = isset($coupon->coupon_code) ? $coupon->coupon_code : '';
                            @endphp
                            <form action="{{ url('add-coupon') }}" class="form-sm form-action form-add-coupon"
                                  method="post"
                                  data-reload-time="1000">
                                @include('common.loading')
                                <div class="form-group">
                                    <label for="coupon">{{__('Coupon Code')}}</label>
                                    <input id="coupon" type="text" class="form-control" name="coupon"
                                           value="{{ $couponCode }}"
                                           placeholder="{{__('Have a coupon?')}}">
                                    <input type="hidden" name="homeID"
                                           value="{{ $homeID }}">
                                    <button class="btn" type="submit" name="sm"><i class="fe-arrow-right "></i>
                                    </button>
                                </div>
                                <div class="form-message"></div>
                            </form>
                            <h5 class="title">{{__('Summary')}}</h5>
                            <ul class="menu cart-list">
                                @php
                                    $numberNight = $cart['cartData']['numberNight'];
                                    $basePrice = $cart['basePrice'];
                                    $extraPrice = $cart['extraPrice'];
                                    $tax = $cart['tax'];
                                @endphp
                                <li>
                                    @if($homeObject->booking_type == 'per_night')
                                        <span>{{ _n(__('Price for %s night'), __('Price for %s nights'), $numberNight) }}</span>
                                    @elseif($homeObject->booking_type == 'per_hour')
                                        <span>{{ _n(__('Price for %s hour'), __('Price for %s hours'), $numberNight) }}</span>
                                    @endif
                                    <span>{{ convert_price($basePrice) }}</span>
                                </li>
                                @if ($extraPrice > 0)
                                    <li>
                                        <span>{{__('Extra Service')}}</span>
                                        <span>{{ convert_price($extraPrice) }}</span>
                                    </li>
                                @endif
                                @if (!empty($coupon))
                                    <li>
                                        <form action="{{ url('remove-coupon') }}" class="form-action" method="post"
                                              data-reload-time="1500">
                                            @include('common.loading')
                                            <input type="hidden" name="homeID"
                                                   value="{{ $homeID }}">
                                            <div class="d-flex align-items-center">
                                            <span>
                                                {{__('Coupon')}}
                                                <button class="btn ml-2" type="submit" name="sm">(remove)</button>
                                            </span>
                                                <span>- {{ $coupon->couponPriceHtml }}</span>
                                            </div>
                                            <div class="form-message"></div>
                                        </form>
                                    </li>
                                @endif
                                <li class="divider">
                                        <span>{{__('Tax')}}
                                            <span class="text-muted">
                                                @if ($cart['tax']['included'] == 'on')
                                                    {{__('(included)')}}
                                                @endif
                                            </span>
                                        </span>
                                    <span>{{ $cart['tax']['tax'] }}%</span>
                                </li>
                                <li class="amount">
                                    <span>{{__('Amount')}}</span>
                                    <span>{{ convert_price($cart['amount']) }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-8 order-sm-0">
                    <div class="checkout-content mt-4">
                        <h3 class="heading">{{__('Checkout')}}</h3>
                        <div class="card-box card-border mt-3">
                            <ul class="nav nav-tabs nav-bordered">
                                <li class="nav-item">
                                    <a href="#co-customer-information" data-toggle="tab" aria-expanded="false"
                                       class="nav-link active">
                                        {{__('1. Customer Information')}}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#co-payment-selection" data-toggle="tab" aria-expanded="true"
                                       class="nav-link">
                                        {{__('2. Payment Selection')}}
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane relative show active" id="co-customer-information">
                                    <form action="{{ url('validation-user-checkout') }}"
                                          class="form checkout-form relative">
                                        @include('common.loading')
                                        @php
                                            if(is_user_logged_in()){
                                                $user_checkout = get_user_meta(get_current_user_id(), 'user_checkout_information');
                                            }else{
                                                $user_checkout = false;
                                            }
                                        @endphp
                                        @if($user_checkout)
                                            @php
                                                enqueue_script('switchery-js');
                                                enqueue_style('switchery-css');
                                            @endphp
                                            <div class="use-last-user-checkout">
                                                <div class="form-group d-flex align-items-center">
                                                    <input type="checkbox" id="last-user-checkout"
                                                           name="use_last_checkout"
                                                           data-plugin="switchery" data-color="#1abc9c" value="on"
                                                           data-size="small"/>
                                                    <label class="mb-0 ml-1"
                                                           for="last-user-checkout">{{__('Use last your information')}}</label>
                                                </div>
                                                <div class="detail">
                                                    <p>
                                                        <strong>{{__('Email:')}} </strong><span>{{ $user_checkout['email'] }}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{__('Full Name:')}} </strong><span>{{ $user_checkout['firstName'] }} {{ $user_checkout['lastName'] }}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{__('Phone:')}} </strong><span>{{ $user_checkout['phone'] }}</span>
                                                    </p>
                                                    <p>
                                                        <strong>{{__('Address:')}} </strong><span>{{ $user_checkout['address'] }} | {{ $user_checkout['city'] }} | {{ $user_checkout['postCode'] }}</span>
                                                    </p>
                                                </div>
                                                <input type="hidden" name="last_user_checkout"
                                                       value="{{ base64_encode(json_encode($user_checkout)) }}">
                                            </div>
                                        @endif
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group mb-3">
                                                    <label for="co-firstname">{{__('First Name')}}</label>
                                                    <input type="text" name="firstName" id="co-firstname"
                                                           class="form-control has-validation"
                                                           data-validation="required"
                                                           placeholder="{{__('First name')}}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group mb-3">
                                                    <label for="co-lastname">{{__('Last Name')}}</label>
                                                    <input type="text" name="lastName" id="co-lastname"
                                                           class="form-control has-validation"
                                                           data-validation="required"
                                                           placeholder="{{__('Last Name')}}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group mb-3">
                                                    <label for="co-email">{{__('Email')}}</label>
                                                    <input type="text" name="email" id="co-email"
                                                           class="form-control has-validation"
                                                           data-validation="required|email"
                                                           placeholder="{{__('Email')}}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group mb-3">
                                                    <label for="co-phone">{{__('Phone')}}</label>
                                                    <input type="text" name="phone" id="co-phone"
                                                           class="form-control has-validation"
                                                           data-validation="required"
                                                           placeholder="{{__('Phone')}}">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group mb-3">
                                                    <label for="co-address">{{__('Address')}}</label>
                                                    <input type="text" name="address" id="co-address"
                                                           class="form-control has-validation"
                                                           data-validation="required"
                                                           placeholder="{{__('Address')}}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group mb-3">
                                                    <label for="co-city">{{__('City (Optional)')}}</label>
                                                    <input type="text" name="city" id="co-city" class="form-control"
                                                           placeholder="{{__('City')}}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group mb-3">
                                                    <label for="co-postcode">{{__('Postcode')}} (Optional)</label>
                                                    <input type="text" name="postCode" id="co-postcode"
                                                           class="form-control"
                                                           placeholder="{{__('Postcode')}}">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group mb-3">
                                                    <label for="co-note">{{__('Note (Optional)')}}</label>
                                                    <textarea name="note" id="co-note"
                                                              class="form-control"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-footer d-flex align-items-center">
                                            <a href="{{ url('/') }}" class="c-black"><i
                                                        class="fe-arrow-left mr-2"></i>{{__('Return to Home')}}
                                            </a>
                                            <a href="javascript: void(0)"
                                               class="btn btn-primary text-uppercase float-right ml-auto waves-effect waves-light btn-next-payment">
                                                {{__('Continue to Payment')}}
                                            </a>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane relative" id="co-payment-selection">
                                    <form action="{{ url('checkout') }}" data-google-captcha="yes"
                                          class="form checkout-form checkout-form-payment relative">
                                        <div class="payment-gateways">
                                            @include('common.loading')
                                            @php
                                                $allPayment = get_available_payments();
                                            @endphp
                                            @if (!empty($allPayment))
                                                @foreach ($allPayment as $paymentName)
                                                    <div class="payment-item payment-{{ $paymentName::getID() }}">
                                                        <div class="radio radio-success mb-3 d-flex align-content-center">
                                                            <input id="payment-{{ $paymentName::getID() }}"
                                                                   class="payment-method"
                                                                   type="radio" name="payment"
                                                                   value="{{ $paymentName::getID() }}">
                                                            <label for="payment-{{ $paymentName::getID() }}">{{ $paymentName::getName() }}</label>
                                                        </div>
                                                        @php
                                                            $desc = $paymentName::getDescription();
                                                        @endphp
                                                        @if (!empty($desc))
                                                            <div class="payment-desc">{!! balanceTags($desc) !!}</div>
                                                        @endif
                                                        <img src="{{ $paymentName::getLogo() }}"
                                                             alt="{{ $paymentName::getName() }}"
                                                             class="payment-logo">
                                                        @php
                                                            $html = $paymentName::getHtml();
                                                        @endphp
                                                        @if (!empty($html))
                                                            <div class="payment-html">{!! balanceTags($html) !!}</div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <div class="checkbox checkbox-success mb-2">
                                                <input type="checkbox" id="term-condition-checkout"
                                                       name="term_condition"
                                                       value="1">
                                                <label for="term-condition-checkout">
                                                    @php
                                                        $term_page_id = get_option('term_condition_page');
                                                        $term_page = get_post($term_page_id, 'page');
                                                    $url = url('/');
                                                    if($term_page){
                                                        $url = get_the_permalink($term_page->post_id, $term_page->post_slug);
                                                    }
                                                    @endphp

                                                    {!! sprintf(__('Agree with <a href="%s" target="_blank">The Terms and Conditions</a>'), $url) !!}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-message"></div>
                                        <div class="tab-footer d-flex align-items-center">
                                            <a href="javascript: void(0);"
                                               class="btn-prev-customer c-black"><i
                                                        class="fe-arrow-left mr-2"></i>{{__('Back to Customer Information')}}
                                            </a>
                                            <input type="submit" name="sm"
                                                   value="Complete Booking"
                                                   class="btn btn-primary text-uppercase float-right ml-auto waves-effect waves-light btn-complete-payment">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="mt-4">
                @include('common.alert', ['type' => 'danger', 'message' => __('The cart is empty')])
            </div>
        @endif
    </div>
</div>
@include('frontend.components.footer')
