@include('frontend.components.header')
@php
    enqueue_script('scroll-magic-js');
    global $post;
@endphp
<div class="single-page single-home pb-5">
    <!-- Gallery -->
    @php
        $gallery = $post->gallery;
        $thumbnail_id = get_home_thumbnail_id($post->post_id);
        $thumbnailUrl = get_attachment_url($thumbnail_id, 'full');
    @endphp
    <div class="hh-thumbnail" style="background-image: url({{ $thumbnailUrl }})">
        <div class="items">
            <a href="javascript: void(0);" class="view-gallery item-link"><span>{{__('View Photos')}}</span> <i
                    class="ti-gallery"></i> </a>
        </div>
        @php
            if ( !empty( $gallery ) ) {
                enqueue_script('light-gallery-js');
                enqueue_style('light-gallery-css');

                $gallery = explode(',', $gallery);
                $data    = [];
                foreach ( $gallery as $key => $val ) {
                    $url       = get_attachment_url( $val, [ 1200, 900 ] );
                    $url_thumb = get_attachment_url( $val, [ 400, 300 ] );
                    if ( !empty( $url ) ) {
                        $data[] = [
                            'src'     =>$url,
                            'thumb'   => $url_thumb
                        ];
                    }
                }
                if ( !empty( $data ) ) {
                    $data = base64_encode(json_encode( $data ));
                    echo '<div class="data-gallery" data-gallery="' .esc_attr($data) . '"></div>';
                }
            }
        @endphp
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-8 col-md-8 col-lg-9 col-content">
                @include('frontend.components.breadcrumb', ['currentPage' => get_translate($post->post_title)])
                <h1 class="title mt-3">
                    {{ get_translate($post->post_title) }}
                    @if($post->is_featured == 'on')
                        <span class="is-featured">{{ get_option('featured_text', __('Featured')) }}</span>
                    @endif
                </h1>
                @if ($post->location_address)
                    <p class="location">
                        <i class="ti-location-pin"></i>
                        {{ get_translate($post->location_address) }}
                    </p>
                @endif
                @php
                    $rate = $post->review_count;
                @endphp
                @if ($rate)
                    <div class="count-reviews">
                        <span class="count">{{ _n('%s review', '%s reviews', $rate) }}</span>
                        {!! star_rating_render($post->rating) !!}
                    </div>
                @endif
                <div class="featured-amenities mt-2 mb-2">
                    <div class="item">
                        <h4>{{__('Guests:')}}</h4>
                        <span> {{ $post->number_of_guest }} </span>
                    </div>
                    <div class="item">
                        <h4>{{__('Bedrooms:')}}</h4>
                        <span>{{ $post->number_of_bedrooms }}</span>
                    </div>
                    <div class="item">
                        <h4>{{__('Bathrooms:')}}</h4>
                        <span>{{ $post->number_of_bathrooms }}</span>
                    </div>
                    <div class="item">
                        <h4>{{__('Footage:')}}</h4>
                        <span>{{ $post->size }} {{ get_option('unit_of_measure', 'm2') }}</span>
                    </div>
                    @php
                        $type = get_term_by('id', $post->home_type);
                        $type_name = $type? get_translate($type->term_title): '';
                    @endphp
                    @if(!empty($type_name))
                        <div class="item">
                            <h4>{{__('Type:')}}</h4>
                            <span>{{ $type_name }}</span>
                        </div>
                    @endif
                </div>
                <h2 class="heading mt-2 mb-3">{{__('Detail')}}</h2>
                {!! balanceTags(get_translate($post->post_content)) !!}
                @php
                    $amenities = $post->tax_home_amenity;
                @endphp
                @if (!empty($amenities) && is_object($amenities))
                    <h2 class="heading mt-4 mb-2">{{__('Amenities')}}</h2>
                    <div class="amenities row">
                        @foreach ($amenities as $amenity)
                            <div class="col-6 col-sm-4 col-lg-3">
                                <div class="amenity-item" data-toggle="ots-tooltip"
                                     data-title="{{ get_translate($amenity->term_description) }}">
                                    @if (!$amenity->term_icon)
                                        <i class="fe-check"></i>
                                    @else
                                        {!! get_icon($amenity->term_icon, '#2a2a2a', '30px', '')  !!}
                                    @endif
                                    <span class="title">{{ get_translate($amenity->term_title) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                <h2 class="heading mt-2 mb-2">{{__('Policies')}}</h2>
                @php
                    $checkIn = $post->checkin_time;
                    $checkOut = $post->checkout_time;
                    $enableCancellation = $post->enable_cancellation;
                    $cancelBeforeDay = (int)$post->cancel_before;
                    $cancellationDetail = $post->cancellation_detail;
                @endphp
                @if($checkIn)
                    <div class="item d-inline-block mr-4 mb-3">
                        <span class="font-weight-bold"><i class=" ti-time mr-1"></i>{{__('Check-in Time:')}}</span>
                        <span class="ml-2">{{ $checkIn }}</span>
                    </div>
                @endif
                @if ($checkOut)
                    <div class="item d-inline-block mr-4 mb-3">
                        <span class="font-weight-bold"><i class=" ti-time mr-1"></i>{{__('Check-out Time:')}}</span>
                        <span class="ml-2">{{ $checkOut }}</span>
                    </div>
                @endif
                <div class="w-100"></div>
                @if ($enableCancellation == 'on')
                    <div class="item d-inline-block mr-4 mb-3">
                        <span class="font-weight-bold">{{__('Cancellation:')}}</span>
                        <span class="ml-2 small-info bg-success">{{__('enable')}}</span>
                        <span class="d-inline-block ml-1">{{ sprintf(__('before %s day(s)'), $cancelBeforeDay) }}</span>
                    </div>
                    @if (get_translate($cancellationDetail))
                        <div class="w-100">{!! balanceTags(get_translate($cancellationDetail)) !!}</div>
                    @endif
                @endif
                <h2 class="heading mt-2 mb-2">{{__('On Map')}}</h2>
                @php
                    $lat = $post->location_lat;
                    $lng = $post->location_lng;
                    $zoom = $post->location_zoom;

                    enqueue_style('mapbox-gl-css');
                    enqueue_style('mapbox-gl-geocoder-css');
                    enqueue_script('mapbox-gl-js');
                    enqueue_script('mapbox-gl-geocoder-js');
                @endphp
                <div class="hh-mapbox-single" data-lat="{{ $lat }}"
                     data-lng="{{ $lng }}" data-zoom="{{ $zoom }}"></div>
                @php
                    $author = get_user_by_id($post->author);
                    $address = $author->address;
                    $location = $author->location;
                    $country = get_country_by_code($location);
                    $description = $author->description;
                @endphp
                <div class="w-100 mt-4"></div>
                <div class="hosted-author">
                    <div class="d-flex justify-content-between">
                        <div class="clearfix">
                            <h2 class="heading mt-0 mb-2">{{sprintf(__('Hosted by %s'), get_username($author->getUserId()) )}}</h2>
                            @if(!empty($address) || !empty($location))
                                <p class="location-author d-flex align-items-center">
                                    @if(!empty($address)) {{$address}} @endif
                                    @if(!empty($location)), {{ $country['name'] }} <span
                                        class="ml-1">{!! $country['flag24'] !!}</span> @endif
                                    <span class="d-none d-sm-inline-block"><span class="dot"></span>{{ sprintf(__('Joined in %s'), date('d F, Y')) }}</span>
                                </p>
                            @endif
                        </div>
                        <div class="clearfix">
                            <img src="{{ get_user_avatar($post->author, [64, 64]) }}" alt="{{ __('User Avatar') }}"
                                 class="avatar rounded-circle">
                        </div>
                    </div>
                    @if(!empty($description))
                        <div class="hr mt-0"></div>
                        <div class="clearfix">
                            {!! balanceTags(nl2br($description)) !!}
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-12 col-sm-4 col-md-4 col-lg-3 col-sidebar">
                @php
                    enqueue_style('daterangepicker-css');
                    enqueue_script('daterangepicker-js');
                    enqueue_script('daterangepicker-lang-js');
                @endphp
                @php
                    $booking_form  = $post->booking_form;
                @endphp
                <div id="form-book-home" class="form-book"
                     data-real-price="{{ url('get-home-price-realtime') }}">
                    <div class="popup-booking-form-close">{!! get_icon('001_close', '#FFFFFF', '28px', '28px') !!}</div>
                    <div class="form-head">
                        <div class="price-wrapper">
                            <span class="price">{{ convert_price($post->base_price) }}</span>
                            <span class="unit">/{{$post->unit}}</span>
                        </div>
                    </div>
                    <div class="form-body relative">
                        @include('common.loading', ['class' => 'booking-loading'])
                        @if($booking_form == 'instant_enquiry')
                            <ul class="nav nav-tabs nav-bordered row">
                                <li class="nav-item nav-item-booking-form-instant col">
                                    <a href="#booking-form-instant"
                                       data-toggle="tab"
                                       aria-expanded="false"
                                       class="nav-link @if($booking_form == 'instant_enquiry' ||$booking_form == 'instant') active @endif">
                                        {{ __('Instant') }}
                                    </a>
                                </li>
                                <li class="nav-item nav-item-booking-form-instant col">
                                    <a href="#booking-form-enquiry"
                                       data-toggle="tab"
                                       aria-expanded="false"
                                       class="nav-link @if($booking_form == 'enquiry') active @endif">
                                        {{ __('Enquiry') }}
                                    </a>
                                </li>
                            </ul>
                        @endif
                        @if($booking_form == 'instant_enquiry')
                            <div class="tab-content">
                                @endif
                                @if($booking_form == 'instant_enquiry' || $booking_form == 'instant')
                                    <div
                                        class="tab-pane @if($booking_form == 'instant_enquiry' ||$booking_form == 'instant') active @endif"
                                        id="booking-form-instant">
                                        <form class="form-action" action="{{ url('add-to-cart-home') }}" method="post"
                                              data-loading-from=".form-body">
                                            @if($post->booking_type == 'per_night')
                                                <div class="form-group">
                                                    <label for="checkinout">{{ __('Check In/Out') }}</label>
                                                    <div class="date-wrapper date-double"
                                                         data-date-format="{{ hh_date_format_moment() }}"
                                                         data-action="{{ url('get-home-availability-single') }}">
                                                        <input type="text" class="input-hidden check-in-out-field"
                                                               name="checkInOut" data-home-id="{{ $post->post_id }}"
                                                               data-home-encrypt="{{ hh_encrypt($post->post_id) }}">
                                                        <input type="text" class="input-hidden check-in-field"
                                                               name="checkIn">
                                                        <input type="text" class="input-hidden check-out-field"
                                                               name="checkOut">
                                                        <span class="check-in-render"
                                                              data-date-format="{{ hh_date_format_moment() }}"></span>
                                                        <span class="divider"></span>
                                                        <span class="check-out-render"
                                                              data-date-format="{{ hh_date_format_moment() }}"></span>
                                                    </div>
                                                </div>
                                            @elseif($post->booking_type == 'per_hour')
                                                <div class="form-group">
                                                    <label for="checkinout">{{__('Check In')}}</label>
                                                    <div class="date-wrapper date-single"
                                                         data-date-format="{{ hh_date_format_moment() }}"
                                                         data-action-time="{{ url('get-home-availability-time-single') }}"
                                                         data-action="{{ url('get-home-availability-single') }}">
                                                        <input type="text"
                                                               class="input-hidden check-in-out-single-field"
                                                               name="checkInOut" data-home-id="{{ $post->post_id }}"
                                                               data-home-encrypt="{{ hh_encrypt($post->post_id) }}">
                                                        <input type="text" class="input-hidden check-in-field"
                                                               name="checkIn">
                                                        <input type="text" class="input-hidden check-out-field"
                                                               name="checkOut">
                                                        <span class="check-in-render"
                                                              data-date-format="{{ hh_date_format_moment() }}"></span>
                                                    </div>
                                                </div>
                                                <div class="form-group form-group-date-time d-none">
                                                    <label>{{ __('Time') }}</label>
                                                    <div class="date-wrapper date-time">
                                                        <div class="date-render check-in-render"
                                                             data-time-format="{{ hh_time_format() }}">
                                                            <div class="render">{{__('Start Time')}}</div>
                                                            <div class="dropdown-time">

                                                            </div>
                                                            <input type="hidden" name="startTime" value=""
                                                                   class="input-checkin"/>
                                                        </div>
                                                        <span class="divider"></span>
                                                        <div class="date-render check-out-render"
                                                             data-time-format="{{ hh_time_format() }}">
                                                            <div class="render">{{__('End Time')}}</div>
                                                            <div class="dropdown-time">

                                                            </div>
                                                            <input type="hidden" name="endTime" value=""
                                                                   class="input-checkin"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @php
                                                $max_guest = (int) $post->number_of_guest;
                                            @endphp
                                            <div class="form-group">
                                                <label for="guest">{{__('Guests')}}</label>
                                                <div class="guest-group">
                                                    <button type="button" class="btn btn-light dropdown-toggle"
                                                            data-toggle="dropdown"
                                                            data-text-guest="{{__('Guest')}}"
                                                            data-text-guests="{{__('Guests')}}"
                                                            data-text-infant="{{__('Infant')}}"
                                                            data-text-infants="{{__('Infants')}}"
                                                            aria-haspopup="true" aria-expanded="false">
                                                        &nbsp;
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <div class="group">
                                                            <span class="pull-left">{{__('Adults')}}</span>
                                                            <div class="control-item">
                                                                <i class="decrease ti-minus"></i>
                                                                <input type="number" min="1" step="1"
                                                                       max="{{ $max_guest }}"
                                                                       name="num_adults"
                                                                       value="1">
                                                                <i class="increase ti-plus"></i>
                                                            </div>
                                                        </div>
                                                        <div class="group">
                                                            <span class="pull-left">{{__('Children')}}</span>
                                                            <div class="control-item">
                                                                <i class="decrease ti-minus"></i>
                                                                <input type="number" min="0" step="1"
                                                                       max="{{ $max_guest }}"
                                                                       name="num_children"
                                                                       value="0">
                                                                <i class="increase ti-plus"></i>
                                                            </div>
                                                        </div>
                                                        <div class="group">
                                                            <span class="pull-left">{{__('Infants')}}</span>
                                                            <div class="control-item">
                                                                <i class="decrease ti-minus"></i>
                                                                <input type="number" min="0" step="1"
                                                                       max="{{ $max_guest }}"
                                                                       name="num_infants"
                                                                       value="0">
                                                                <i class="increase ti-plus"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                @php
                                                    $requiredExtra = $post->required_extra;
                                                @endphp
                                                @if ($requiredExtra)
                                                    <div class="extra-services">
                                                        <label class="d-block mb-2" for="extra-services">
                                                            {{__('Extra')}}
                                                            <span class="text-danger f12">{{__('(required)')}}</span>
                                                            <a href="#extra-collapse" class="right"
                                                               data-toggle="collapse">{!! get_icon('002_download_1', '#2a2a2a','15px') !!}</a>
                                                        </label>
                                                        <div id="extra-collapse" class="collapse show">
                                                            @foreach ($requiredExtra as $ex)
                                                                <div class="extra-item d-flex">
                                                                    <span
                                                                        class="name">{{ get_translate($ex['name']) }}</span>
                                                                    <span
                                                                        class="price ml-auto">{{ convert_price($ex['price']) }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                                @php
                                                    $extra = $post->not_required_extra;
                                                @endphp
                                                @if ($extra)
                                                    <div class="extra-services">
                                                        <label class="d-block mb-2" for="extra-services">
                                                            <span>{{__('Extra (optional)')}}</span>
                                                            <a href="#extra-not-required-collapse" class="right"
                                                               data-toggle="collapse">{!! get_icon('002_download_1', '#2a2a2a','15px') !!}</a>
                                                        </label>
                                                        <div id="extra-not-required-collapse" class="collapse">
                                                            @foreach ($extra as $ex)
                                                                <div class="extra-item d-flex">
                                                                    <div class="checkbox checkbox-success">
                                                                        <input
                                                                            id="extra-service-{{ $ex['name_unique'] }}"
                                                                            class="input-extra"
                                                                            type="checkbox" name="extraServices[]"
                                                                            value="{{ $ex['name_unique'] }}">
                                                                        <label
                                                                            for="extra-service-{{ $ex['name_unique'] }}">
                                                                            <span
                                                                                class="name">{{ get_translate($ex['name']) }}</span>
                                                                        </label>
                                                                    </div>
                                                                    <span
                                                                        class="price ml-auto">{{ convert_price($ex['price']) }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group form-render">
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="hidden" name="homeID" value="{{ $post->post_id }}">
                                                <input type="hidden" name="homeEncrypt"
                                                       value="{{ hh_encrypt($post->post_id) }}">
                                                <input type="submit" class="btn btn-primary btn-block text-uppercase"
                                                       name="sm"
                                                       value="{{__('Book Now')}}">
                                            </div>
                                            <div class="form-message"></div>
                                        </form>
                                    </div>
                                @endif
                                @if($booking_form == 'instant_enquiry' || $booking_form == 'enquiry')
                                    <div class="tab-pane @if($booking_form == 'enquiry') active @endif"
                                         id="booking-form-enquiry">
                                        <form action="{{ url('send-enquiry-form') }}" data-google-captcha="yes"
                                              class="form-action form-sm has-reset" data-loading-from=".form-body">
                                            <div class="form-group">
                                                <label for="full-name-enquiry-form">{{ __('Full Name') }} <span
                                                        class="text-danger">*</span></label>
                                                <input id="full-name-enquiry-form" type="text" name="name"
                                                       class="form-control has-validation" data-validation="required">
                                            </div>
                                            <div class="form-group">
                                                <label for="email-enquiry-form">{{ __('Email') }} <span
                                                        class="text-danger">*</span></label>
                                                <input id="email-enquiry-form" type="email" name="email"
                                                       class="form-control has-validation"
                                                       data-validation="required|email">
                                            </div>
                                            <div class="form-group">
                                                <label for="message-enquiry-form">{{ __('Message') }} <span
                                                        class="text-danger">*</span></label>
                                                <textarea id="phone-enquiry-form" class="form-control has-validation"
                                                          name="message" data-validation="required|min:10:m"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <input type="submit" class="btn btn-primary btn-block text-uppercase"
                                                       name="sm"
                                                       value="{{ __('Send a Request') }}">
                                            </div>
                                            <input type="hidden" name="post_id" value="{{ $post->post_id }}">
                                            <input type="hidden" name="post_encrypt"
                                                   value="{{ hh_encrypt($post->post_id) }}">
                                            <div class="form-message"></div>
                                        </form>
                                    </div>
                                @endif
                                @if($booking_form == 'instant_enquiry')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @php
            $lat = $post->location_lat;
            $lng = $post->location_lng;
            $list_services = \App\Http\Controllers\HomeController::get_inst()->listOfHomes([
                'number' => 4,
                'location' => [
                    'lat' => $lat,
                    'lng' => $lng,
                    'radius' => 25
                ],
                'orderby' => 'distance',
                'order' => 'asc',
                'not_in' => [$post->post_id]
            ]);
        @endphp
        @if(count($list_services['results']))
            <h2 class="heading mt-4 mb-2">{{__('Homes Near By')}}</h2>
            <div class="hh-list-of-services">
                <div class="row">
                    @foreach($list_services['results'] as $item)
                        <div class="col-12 col-md-6 col-lg-3">
                            @include('frontend.home.loop.grid', ['item' => $item, 'show_distance' => true])
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        @if(enable_review())
            <div class="row">
                <div class="col-12 col-sm-8 col-md-8 col-lg-9 col-content">
                    @include('frontend.home.review')
                </div>
            </div>
        @endif
    </div>
    <div class="mobile-book-action">
        <div class="container">
            <div class="action-inner">
                <div class="action-price-wrapper">
                    <span class="price">{{ convert_price($post->base_price) }}</span>
                    <span class="unit">/{{$post->unit}}</span>
                </div>
                <a class="btn btn-primary action-button" id="mobile-check-availability">{{__('Check Availability')}}</a>
            </div>
        </div>
    </div>
</div>
@include('frontend.components.footer')
