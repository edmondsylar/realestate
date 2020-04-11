<!-- Topbar Start -->
<div class="navbar-custom">
    <ul class="list-unstyled topnav-menu float-right mb-0">
        @php
            $search = Input::get('_s');
        @endphp
        <li class="d-none d-sm-block">
            <form class="app-search" action="{{ dashboard_url('all-booking') }}" method="get">
                <div class="app-search-box">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="{{__('Search booking...')}}" name="_s"
                               value="{{ $search }}">
                        <div class="input-group-append">
                            <button class="btn" type="submit">
                                <i class="fe-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </li>
        @php
            $langs = get_languages(true);
            $current_session = get_current_language();
        @endphp
        @if(count($langs) > 1)
            @php
                $lang_remain = [];
                $current_session = get_current_language();
                    $current_lang = [];
                    foreach ($langs as $item){
                        if($item['code'] == $current_session){
                            $current_lang = $item;
                        }else{
                            $lang_remain[] = $item;
                        }
                    }
                    if(empty($current_lang)){
                        $current_lang = $langs[0];
                            $lang_remain = $langs;
                        if(isset($lang_remain[0])){
                            unset($lang_remain);
                        }
                    }
            @endphp
            <li class="dropdown">
                <a class="nav-link dropdown-toggle waves-effect waves-light" data-toggle="dropdown"
                   href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                            <span class="ml-1">
                                <img src="{{ esc_attr(asset('vendors/countries/flag/32x32/' . $current_lang['flag_code'] . '.png')) }}"/>
                                <i class="mdi mdi-chevron-down"></i>
                            </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                    @foreach($lang_remain as $item)
                        @php
                            $url = \Illuminate\Support\Facades\Request::fullUrl();
                            $url = add_query_arg('lang', $item['code'], $url);
                        @endphp
                        <a href="{{ $url }}" class="dropdown-item notify-item">
                            <span>
                                <img src="{{ esc_attr(asset('vendors/countries/flag/32x32/' . $item['flag_code'] . '.png')) }}"/>
                                {{$item['name']}}
                            </span>
                        </a>
                    @endforeach
                </div>
            </li>
        @endif
        @php
            $noti = Notifications::get_inst()->getLatestNotificationByUser(get_current_user_id(), 'to');

            $args = [
                'user_id' => get_current_user_id(),
                'user_encrypt' => hh_encrypt(get_current_user_id())
            ];
        @endphp
        <li id="hh-dropdown-notification" class="dropdown notification-list"
            data-action="{{ url('update-last-time-check-notification') }}"
            data-params="{{ base64_encode(json_encode($args)) }}">
            <a class="nav-link dropdown-toggle waves-effect waves-light"
               data-toggle="dropdown" href="#" role="button"
               aria-haspopup="false" aria-expanded="false">
                <i class="fe-bell noti-icon"></i>
                @if($noti['total'])
                    <span class="badge badge-danger rounded-circle noti-icon-badge">{{ $noti['total'] }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-lg">

                <!-- item-->
                <div class="dropdown-item noti-title">
                    <h5 class="m-0">{{__('Notification')}}</h5>
                </div>
                @if($noti['total'])
                    <div class="slimscroll noti-scroll">
                        @foreach($noti['results'] as $noti_item)
                            <div class="dropdown-item notify-item">
                                <div class="notify-icon notify-{{ $noti_item->type }}">
                                    @if($noti_item->type == 'booking')
                                        <i class="fe-calendar"></i>
                                    @elseif($noti_item->type == 'global')
                                        <i class="fe-shield"></i>
                                    @endif
                                </div>
                                <p class="notify-details">{!! balanceTags($noti_item->title) !!}</p>
                                <p class="text-muted mb-0 user-msg">
                                    <small>{!! balanceTags(get_time_since($noti_item->created_at)) !!}</small>
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center">{{__('No notification yet')}}</p>
            @endif
            <!-- All-->
                <a href="{{ dashboard_url('all-notifications') }}"
                   class="dropdown-item text-center text-primary notify-item notify-all">
                    {{__('View all')}}
                    <i class="fi-arrow-right"></i>
                </a>

            </div>
        </li>

        <li class="dropdown user-nav-list">
            <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#"
               role="button" aria-haspopup="false" aria-expanded="false">
                @php
                    $userdata = get_current_user_data();
                @endphp
                <img src="{{ get_user_avatar($userdata->getUserId(), [32,32]) }}" alt="user-image"
                     class="rounded-circle">
                <span class="pro-user-name ml-1">
                    {{ get_username($userdata->getUserId()) }}
                    <i class="mdi mdi-chevron-down"></i>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                <!-- item-->
                <div class="dropdown-header noti-title">
                    <h6 class="text-overflow">{{__('Welcome !')}}</h6>
                </div>
                <!-- item-->
                <a href="{{ url('/') }}" class="dropdown-item notify-item">
                    <i class="fe-home"></i>
                    <span>{{__('Home')}}</span>
                </a>
                <!-- item-->
                <a href="{{ dashboard_url('profile') }}" class="dropdown-item notify-item">
                    <i class="fe-user"></i>
                    <span>{{__('Profile')}}</span>
                </a>
                <!-- item-->
                @if(is_admin() || is_partner())
                    <a href="{{ dashboard_url('my-home') }}" class="dropdown-item notify-item">
                        <i class="fe-book-open"></i>
                        <span>{{__('Listing')}}</span>
                    </a>
                @endif
                <div class="dropdown-divider"></div>
                <!-- item-->
                @php
                    $data = [
                        'user_id' => $userdata->getUserId()
                    ];
                @endphp
                <a href="javascript:void(0)" data-action="{{ auth_url('logout') }}"
                   data-params="{{ base64_encode(json_encode($data)) }}"
                   class="dropdown-item notify-item hh-link-action">
                    <i class="fe-log-out"></i>
                    <span>{{__('Logout')}}</span>
                </a>

            </div>
        </li>
        @if(is_admin())
            <li class="dropdown notification-list">
                <a href="javascript:void(0);" class="nav-link right-bar-toggle waves-effect waves-light">
                    <i class="fe-settings noti-icon"></i>
                </a>
            </li>
        @endif
    </ul>

    <!-- LOGO -->
    <div class="logo-box">
        <a href="{{ dashboard_url('/') }}" class="logo text-center">
            @php
                $dashboard_logo = get_option('dashboard_logo');
                $dashboard_logo_short = get_option('dashboard_logo_short');
                $dashboard_logo_url = get_attachment_url($dashboard_logo);
                $dashboard_logo_short_url = get_attachment_url($dashboard_logo_short);
            @endphp
            <span class="logo-lg">
                <img src="{{ $dashboard_logo_url }}" alt="" height="40">
            </span>
            <span class="logo-sm">
                <img src="{{ $dashboard_logo_short_url }}" alt="" height="40">
            </span>
        </a>
    </div>
    <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
        <li>
            <button class="button-menu-mobile waves-effect waves-light">
                <i class="fe-menu"></i>
            </button>
        </li>
        @if(is_admin() || is_partner())
        <li class="dropdown d-none d-lg-block">
            <a class="nav-link dropdown-toggle waves-effect waves-light" data-toggle="dropdown"
               href="javascript:void(0)" role="button"
               aria-haspopup="false" aria-expanded="false">
                {{__('Create New')}}
                <i class="mdi mdi-chevron-down"></i>
            </a>
            <div class="dropdown-menu">
                <!-- item-->
                <a href="{{ dashboard_url('add-new-post') }}" class="dropdown-item">
                    <span>{{__('New Post')}}</span>
                </a>
                <!-- item-->
                <a href="{{ dashboard_url('add-new-page') }}" class="dropdown-item">
                    <span>{{__('New Page')}}</span>
                </a>
                <!-- item-->
                <a href="{{ dashboard_url('add-new-home') }}" class="dropdown-item">
                    <span>{{__('New Home')}}</span>
                </a>
                <!-- item-->
                <a href="{{ dashboard_url('coupon') }}" class="dropdown-item">
                    <span>{{__('New Coupon')}}</span>
                </a>
            </div>
        </li>
            @endif
    </ul>
</div>
<!-- end Topbar -->
