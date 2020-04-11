@include('frontend.components.header')
@php
    enqueue_style('home-slider');
    enqueue_script('home-slider');

    enqueue_style('mapbox-gl-css');
    enqueue_style('mapbox-gl-geocoder-css');
    enqueue_script('mapbox-gl-js');
    enqueue_script('mapbox-gl-geocoder-js');

    enqueue_style('daterangepicker-css');
    enqueue_script('daterangepicker-js');
    enqueue_script('daterangepicker-lang-js');

    enqueue_style('iconrange-slider');
    enqueue_script('iconrange-slider');
@endphp
<div class="home-page pb-5">
    <div class="hh-search-form-wrapper">
        <div class="ots-slider-wrapper" data-style="full-screen" data-slider="ots-slider">
            <div class="ots-slider">
                @php
                    $sliders = get_option('home_slider');
                    $sliders = explode(',', $sliders);
                @endphp
                @if(!empty($sliders) && is_array($sliders))
                    @foreach($sliders as $id)
                        @php
                            $url = get_attachment_url($id);
                        @endphp
                        <div class="item">
                            <div class="outer"
                                 style="background-image: url('{{ $url }}')"></div>
                            <div class="inner">
                                <div class="img"
                                     style="background-image: url('{{ $url }}');"></div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="hh-search-form-section">
            <div class="container">
                <div class="hh-search-form">
                    <h1 class="h3">{{ __('Book unique places to stay') }}</h1>
                    <form action="{{ url('page-search-result') }}" class="form mt-3" method="get">
                        <div class="form-group">
                            <label>{{__('Where')}}</label>
                            <div class="form-control" data-plugin="mapbox-geocoder" data-value=""
                                 data-current-location="1"
                                 data-your-location="{{__('Your Location')}}"
                                 data-placeholder="{{__('Enter a location ...')}}"></div>
                            <div class="map d-none"></div>
                            <input type="hidden" name="lat" value="">
                            <input type="hidden" name="lng" value="">
                            <input type="hidden" name="address" value="">
                        </div>
                        <div class="form-group">
                            <div class="radio radio-pink form-check-inline ml-1">
                                <input id="booking_type_home_night" type="radio" name="bookingType" value="per_night"
                                       checked>
                                <label for="booking_type_home_night">{{ __('per Night') }}</label>
                            </div>
                            <div class="radio radio-pink form-check-inline ml-1">
                                <input id="booking_type_home_hour" type="radio" name="bookingType" value="per_hour">
                                <label for="booking_type_home_hour">{{ __('per Hour') }}</label>
                            </div>
                        </div>

                        <div class="form-group form-group-date-single d-none">
                            <label>{{__('Check In')}}</label>
                            <div class="date-wrapper date date-single"
                                 data-date-format="{{ hh_date_format_moment() }}">
                                <input type="text"
                                       class="input-hidden check-in-out-field"
                                       name="checkInOutTime">
                                <input type="text" class="input-hidden check-in-field"
                                       name="checkInTime">
                                <input type="text" class="input-hidden check-out-field"
                                       name="checkOutTime">
                                <span class="check-in-render"
                                      data-date-format="{{ hh_date_format_moment() }}"></span>
                            </div>
                        </div>
                        <div class="form-group form-group-date-time d-none">
                            <label>{{ __('Time') }}</label>
                            @php
                                $listTime = list_hours(30);
                            @endphp
                            <div class="date-wrapper date-time">
                                <div class="date-render check-in-render"
                                     data-time-format="{{ hh_time_format() }}">
                                    <div class="render">{{__('Start Time')}}</div>
                                    <div class="dropdown-time">
                                        @foreach($listTime as $key => $value)
                                            <div class="item" data-value="{{ $key }}">{{ $value }}</div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="startTime" value=""
                                           class="input-checkin"/>
                                </div>
                                <span class="divider"></span>
                                <div class="date-render check-out-render"
                                     data-time-format="{{ hh_time_format() }}">
                                    <div class="render">{{__('End Time')}}</div>
                                    <div class="dropdown-time">
                                        @foreach($listTime as $key => $value)
                                            <div class="item" data-value="{{ $key }}">{{ $value }}</div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="endTime" value=""
                                           class="input-checkin"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-date">
                            <label>{{__('Check In/Out')}}</label>
                            <div class="date-wrapper date date-double" data-date-format="{{ hh_date_format_moment()  }}">
                                <input type="text" class="input-hidden check-in-out-field" name="checkInOut">
                                <input type="text" class="input-hidden check-in-field" name="checkIn">
                                <input type="text" class="input-hidden check-out-field" name="checkOut">
                                <span class="check-in-render"
                                      data-date-format="{{ hh_date_format_moment()  }}"></span>
                                <span class="divider"></span>
                                <span class="check-out-render"
                                      data-date-format="{{ hh_date_format_moment()  }}"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{__('Guests')}}</label>
                            <div class="guest-group">
                                <button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown"
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
                                            <input type="number" min="1" step="1" max="15" name="num_adults" value="1">
                                            <i class="increase ti-plus"></i>
                                        </div>
                                    </div>
                                    <div class="group">
                                        <span class="pull-left">{{__('Children')}}</span>
                                        <div class="control-item">
                                            <i class="decrease ti-minus"></i>
                                            <input type="number" min="0" step="1" max="15" name="num_children"
                                                   value="0">
                                            <i class="increase ti-plus"></i>
                                        </div>
                                    </div>
                                    <div class="group">
                                        <span class="pull-left">{{__('Infants')}}</span>
                                        <div class="control-item">
                                            <i class="decrease ti-minus"></i>
                                            <input type="number" min="0" step="1" max="10" name="num_infants" value="0">
                                            <i class="increase ti-plus"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $minmax = \App\Http\Controllers\HomeController::get_inst()->getMinMaxPrice();
                        $currencySymbol = current_currency('symbol');
                        ?>
                        <div class="form-group">
                            <label>{{__('Price Range')}}</label>
                            <input type="text" name="price_filter"
                                   data-plugin="ion-range-slider"
                                   data-prefix="{{ $currencySymbol }}"
                                   data-min="{{ $minmax['min'] }}"
                                   data-max="{{ $minmax['max'] }}"
                                   data-from="{{ $minmax['min'] }}"
                                   data-to="{{ $minmax['max'] }}"
                                   data-skin="round">
                        </div>
                        <div class="form-group">
                            <input class="btn btn-primary w-100" type="submit" name="sm"
                                   value="{{__('Search')}}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <!-- Home Types -->
        <h2 class="h3 mt-5">{{__('Find a Home type')}}</h2>
        <div class="hh-list-terms mt-3">
            @php
                $home_types = get_terms('home-type', true);
            @endphp
            @if(count($home_types))
                @php
                    enqueue_script('owl-carousel');
                    enqueue_style('owl-carousel');
                    enqueue_style('owl-carousel-theme');

                $responsive = [
                    0 => [
                        'items' => 1
                    ],
                    768 => [
                        'items' => 2
                    ],
                    992 => [
                        'items' => 3
                    ],
                    1200 => [
                        'items' => 4
                    ]
                ];
                @endphp
                <div class="hh-carousel carousel-padding nav-style2"
                     data-responsive="{{ base64_encode(json_encode($responsive)) }}" data-margin="15" data-loop="1">
                    <div class="owl-carousel">
                        @foreach($home_types as $item)
                            @php
                                $url = get_attachment_url($item->term_image, [350, 300]);
                            @endphp
                            <div class="item">
                                <div class="hh-term-item">
                                    <a href="{{ url('page-search-result/?home-type=' . $item->term_id) }}">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="thumbnail has-matchHeight">
                                                    <div class="thumbnail-outer">
                                                        <div class="thumbnail-inner">
                                                            <img src="{{ $url }}"
                                                                 alt="{{ get_attachment_alt($item->term_image ) }}"
                                                                 class="img-fluid">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 d-flex align-items-center">
                                                <div class="clearfix">
                                                    <h4>{{ get_translate($item->term_title) }}</h4>
                                                    @php
                                                        $home_count = get_homes_by_hometype_id($item->term_id);
                                                    @endphp
                                                    <p class="text-muted">{{ sprintf(__('%s Homes'), $home_count) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="owl-nav">
                        <a href="javascript:void(0)"
                           class="prev">{!! balanceTags(get_icon('004_left_arrow', '#353535', '15px')) !!}</a>
                        <a href="javascript:void(0)"
                           class="next">{!! balanceTags(get_icon('005_right', '#353535', '15px')) !!}</a>
                    </div>
                </div>
            @endif
        </div>
        <!--Featured Homes -->
        @php
            $list_services = \App\Http\Controllers\HomeController::get_inst()->listOfHomes([
                'number' => 4,
                'is_featured' => 'on'
            ]);
        @endphp
        @if(count($list_services['results']))
            <h2 class="h3 mt-5">{{__('Featured Homes')}}</h2>
            <p>{{__('Browse beautiful places to stay with all the comforts of home, plus more')}}</p>
            <div class="hh-list-of-services">
                @php
                    $responsive = [
                        0 => [
                            'items' => 1
                        ],
                        768 => [
                            'items' => 2
                        ],
                        992 => [
                            'items' => 3
                        ],
                        1200 => [
                            'items' => 4
                        ],
                    ];
                @endphp
                <div class="hh-carousel carousel-padding nav-style2"
                     data-responsive="{{ base64_encode(json_encode($responsive)) }}" data-margin="15" data-loop="1">
                    <div class="owl-carousel">
                        @foreach($list_services['results'] as $item)
                            <div class="item">
                                @include('frontend.home.loop.grid', ['item' => $item])
                            </div>
                        @endforeach
                    </div>
                    <div class="owl-nav">
                        <a href="javascript:void(0)"
                           class="prev">{!! get_icon('004_left_arrow', '#353535', '15px') !!}</a>
                        <a href="javascript:void(0)" class="next">{!! get_icon('005_right', '#353535', '15px') !!}</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <!-- Call to action -->
    @php
        $page_id  = get_option('call_to_action_page');
    @endphp
    @if(!empty($page_id))
        @php
            $link = get_permalink_by_id($page_id, 'page');
        @endphp
        <div class="container mt-5">
            <div class="call-to-action pl-4 pr-4">
                <div class="row">
                    <div class="col-lg-8">
                        <h5 class="main-text">{{__('The most exciting trip this summer')}}</h5>
                        <p class="sub-text">{{__('Enjoy moments at the beach Maldives with friends')}}</p>
                    </div>
                    <div class="col-lg-4">
                        <a href="{{ $link }}" class="btn btn-primary right">{{__('Watch now')}}</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="container">
        <!-- Destination -->
        @php
            $locations = get_option('top_destination');
        @endphp
        @if(!empty($locations))
            <h2 class="h3 mt-5">{{__('Top destinations')}}</h2>
            <p>{{__('Browse beautiful places to stay with all the comforts of home, plus more')}}</p>
            <div class="hh-list-destinations">
                @php
                    $location_check_in = date('Y-m-d');
                    $location_check_out = date('Y-m-d', strtotime('+1 day', time()));
                    $responsive = [
                        0 => [
                            'items' => 1
                        ],
                        768 => [
                            'items' => 2
                        ],
                        992 => [
                            'items' => 3
                        ],
                    ];
                @endphp
                <div class="hh-carousel carousel-padding nav-style2"
                     data-responsive="{{ base64_encode(json_encode($responsive)) }}" data-margin="15" data-loop="1">
                    <div class="owl-carousel">
                        @foreach($locations as $location)
                            @php
                                $lat = $location['lat'];
                                $lng = $location['lng'];
                                $address = $location['name'];
                                $location_query = [
                                    'lat' => $lat,
                                    'lng' => $lng,
                                    'address' => urlencode($address),
                                    'checkIn' => $location_check_in,
                                    'checkOut' => $location_check_out,
                                ];
                                $location_url = url('/page-search-result');
                                $location_url = add_query_arg($location_query, $location_url);
                                $rand = rand(1,6);
                            @endphp
                            <div class="item">
                                <div class="hh-destination-item">
                                    <a href="{{ $location_url }}">
                                        <div class="thumbnail has-matchHeight">
                                            <div class="thumbnail-outer">
                                                <div class="thumbnail-inner">
                                                    <img src="{{ get_attachment_url($location['image']) }}"
                                                         alt="{{ get_attachment_alt($location['image'] ) }}"
                                                         class="img-fluid">
                                                </div>
                                            </div>
                                            <div class="detail">
                                                <h2 class="text-center des-paterm-{{$rand}}">{{ get_translate($location['name']) }}</h2>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="owl-nav">
                        <a href="javascript:void(0)"
                           class="prev">{!! balanceTags(get_icon('004_left_arrow', '#353535', '15px')) !!}</a>
                        <a href="javascript:void(0)" class="next">{!! get_icon('005_right', '#353535', '15px') !!}</a>
                    </div>
                </div>
            </div>
        @endif
    <!-- Home in New York -->
        @php
            $list_services = \App\Http\Controllers\HomeController::get_inst()->listOfHomes([
                'number' => 4,
                'location' => [
                    'lat' => '40.72939317669241',
                    'lng' => '-73.99034249572969',
                    'radius' => 50
                ]
            ]);
        @endphp
        @if(count($list_services['results']))
            <h2 class="h3 mt-4">{{__('Homes in New York')}}</h2>
            <p>{{__('Browse beautiful places to stay with all the comforts of home, plus more')}}</p>
            <div class="hh-list-of-services">
                <div class="row">
                    @foreach($list_services['results'] as $item)
                        <div class="col-12 col-md-6 col-lg-3">
                            @include('frontend.home.loop.grid', ['item' => $item])
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    <!-- Testimonial -->
    @php
        $testimonials = get_option('testimonial', []);
        $responsive = [
            0 => [
                'items' => 1
            ],
            768 => [
                'items' => 2
            ],
            992 => [
                'items' => 2
            ],
            1200 => [
                'items' => 3
            ],
        ];
    @endphp
    @if(count($testimonials))
        <div class="section section-background pt-5 pb-5 mt-5" style="background-color: #dd556a;">
            <div class="container">
                <h2 class="h3 mt-0 c-white">{{__('Say about Us')}}</h2>
                <p class="c-white">{{__('Browse beautiful places to stay with all the comforts of home, plus more')}}</p>
                <div class="hh-testimonials">
                    <div class="hh-carousel carousel-padding nav-style2"
                         data-responsive="{{ base64_encode(json_encode($responsive)) }}" data-margin="30" data-loop="1">
                        <div class="owl-carousel">
                            @foreach($testimonials as $testimonial)
                                <div class="item">
                                    <div class="testimonial-item">
                                        <div class="testimonial-inner">
                                            <div class="author-avatar">
                                                <img src="{{ get_attachment_url($testimonial['author_avatar'], [80, 80]) }}"
                                                     alt="{{ $testimonial['author_name'] }}" class="img-fluid">
                                                <i class="mdi mdi-format-quote-open hh-icon"></i>
                                            </div>
                                            <div class="author-rate">
                                                @include('frontend.components.star', ['rate' => (int) $testimonial['author_rate']])
                                            </div>
                                            <div class="author-comment">
                                                {{ get_translate($testimonial['author_comment']) }}
                                            </div>
                                            <h2 class="author-name">
                                                {{ get_translate($testimonial['author_name']) }}
                                            </h2>
                                            @if($testimonial['date'])
                                                <div class="author-date">
                                                    on {{ date(hh_date_format(), strtotime($testimonial['date'])) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="owl-nav">
                            <a href="javascript:void(0)"
                               class="prev">{!! get_icon('004_left_arrow', '#353535', '15px') !!}</a>
                            <a href="javascript:void(0)"
                               class="next">{!! get_icon('005_right', '#353535', '15px') !!}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endif
<!-- List of Blog -->
    <div class="container">
        @php
            $list_services = \App\Http\Controllers\PostController::get_inst()->listOfPosts([
                'number' => 2
            ]);
            $responsive = [
                0 => [
                    'items' => 1
                ]
            ];
        @endphp
        @if(count($list_services['results']))
            <h2 class="h3 mt-5 mb-3">{{__('The latest from Blog')}}</h2>
            <div class="hh-list-of-blog">
                <div class="row">
                    @foreach($list_services['results'] as $item)
                        <div class="col-12 col-md-6">
                            <div class="hh-blog-item style-2">
                                <a href="{{ get_the_permalink($item->post_id, $item->post_slug, 'post') }}">
                                    <div class="thumbnail">
                                        <div class="thumbnail-outer">
                                            <div class="thumbnail-inner">
                                                <img src="{{ get_attachment_url($item->thumbnail_id, [650, 550]) }}"
                                                     alt="{{ get_attachment_alt($item->thumbnail_id ) }}"
                                                     class="img-fluid">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <div class="category">{{__('Action')}}
                                    <div class="date">{{ date(hh_date_format(), $item->created_at) }}</div>
                                </div>
                                <h2 class="title"><a
                                            href="{{ get_the_permalink($item->post_id, $item->post_slug, 'post') }}">{{ get_translate($item->post_title) }}</a>
                                </h2>
                                <div class="description">{!! balanceTags(short_content(get_translate($item->post_content), 55)) !!}</div>
                                <div class="w-100 mt-2"></div>
                                <div class="d-flex justify-content-between">
                                    @php
                                        $url = get_the_permalink($item->post_id, $item->post_slug, 'post');
                                        $img = get_attachment_url($item->thumbnail_id);
                                        $desc = get_translate($item->post_title);

                                    $share = [
                                        'facebook' => [
                                            'url' => $url
                                        ],
                                        'twitter' => [
                                            'url' => $url
                                        ],
                                        'pinterest' => [
                                            'url' => $url,
                                            'img' => $img,
                                            'description' => $desc
                                        ]
                                    ];
                                    @endphp
                                    @include('frontend.components.share', ['share' => $share])
                                    <a href="{{ get_the_permalink($item->post_id, $item->post_slug, 'post') }}"
                                       class="read-more">{{__('Keep Reading')}} {!! balanceTags(get_icon('002_right_arrow', '#F8546D', '12px', '')) !!}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@include('frontend.components.footer')
