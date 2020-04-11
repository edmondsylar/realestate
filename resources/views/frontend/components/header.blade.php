<?php do_action('init'); ?>
<?php do_action('frontend_init'); ?>
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $favicon = get_option('favicon');
        $favicon_url = get_attachment_url($favicon);
    @endphp
    <link rel="shortcut icon" type="image/png" href="{{ $favicon_url }}"/>

    <title>{{ page_title() }}</title>

    <link href="{{asset('css/vendor.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/main.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/frontend.min.css')}}" rel="stylesheet" type="text/css">
    <script>
        var hh_params = {
            'isValidated': {},
            'mapbox_token': '<?php echo e(get_option('mapbox_key')) ?>',
            'currency': '<?php echo json_encode(current_currency()) ?>',
            'facebook_login': '<?php echo e(get_option('facebook_login', 'off')); ?>',
            'facebook_api': '<?php echo e(get_option('facebook_api')); ?>',
            'use_google_captcha': '<?php echo e(get_option('use_google_captcha', 'off')); ?>',
            'google_captcha_key': '<?php echo e(get_option('google_captcha_site_key')) ?>'
        };
    </script>
    <?php do_action('header'); ?>
</head>
<body class="awe-booking {{ isset($bodyClass)? $bodyClass: '' }}">
<nav id="mobile-navigation" class="main-navigation mobile-natigation d-lg-none"
     aria-label="Top Menu">
    <div class="menu-primary-container">
        <?php
        if (has_nav_primary()) {
            get_nav([
                'location' => 'primary',
                'walker' => 'main-mobile'
            ]);
        }
        ?>
    </div>
</nav><!-- #site-navigation -->
@include('common.loading', ['class' => 'page-loading'])
@if(!is_user_logged_in())
    <div id="hh-login-modal" class="modal fade modal-no-footer" tabindex="-1" role="dialog"
         aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-uppercase">{{__('Login')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                    </button>
                </div>
                <div class="modal-body">
                    <form id="hh-login-form" class="form form-sm form-action" action="{{ url('auth/login') }}"
                          data-reload-time="1500"
                          method="post">
                        @include('common.loading')
                        <div class="form-group mb-3">
                            <label for="email-login-form">{{__('Email address')}}</label>
                            <input class="form-control has-validation" data-validation="required" type="text"
                                   id="email-login-form" name="email" placeholder="{{__('Enter your email')}}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="password-login-form">{{__('Password')}}</label>
                            <input class="form-control has-validation" data-validation="required|min:6:ms"
                                   type="password" id="password-login-form" name="password"
                                   placeholder="{{__('Enter your password')}}">
                        </div>
                        <div class="form-group mb-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkbox-signin-login-form"
                                       checked>
                                <label class="custom-control-label"
                                       for="checkbox-signin-login-form">{{__('Remember me')}}</label>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-center">
                            {!! referer_field(false) !!}
                            <button class="btn btn-primary btn-block text-uppercase"
                                    type="submit"> {{__('Log In')}}</button>
                        </div>
                        <div class="form-message">

                        </div>
                        <div class="text-center">
                            <p class="mt-3 text-muted">{{__('Log in with')}}</p>
                            <ul class="social-list list-inline mt-3 mb-0">
                                @if(social_enable('facebook'))
                                    <li class="list-inline-item">
                                        <a href="{{ FacebookLogin::get_inst()->getLoginUrl() }}"
                                           class="social-list-item border-primary text-primary"><i
                                                class="mdi mdi-facebook"></i></a>
                                    </li>
                                @endif
                                @if(social_enable('google'))
                                    <li class="list-inline-item">
                                        <a href="{{ GoogleLogin::get_inst()->getLoginUrl() }}"
                                           class="social-list-item border-danger text-danger"><i
                                                class="mdi mdi-google"></i></a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div class="mt-3 text-center">
                            <p><a href="javascript: void(0)" data-toggle="modal" data-target="#hh-fogot-password-modal"
                                  class="c-black ml-1">{{__('Forgot your password?')}}</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div><!-- /.modal -->
    <div id="hh-register-modal" class="modal fade modal-no-footer" tabindex="-1" role="dialog"
         aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-uppercase">{{__('Sign Up')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                    </button>
                </div>
                <div class="modal-body">
                    <form id="hh-sign-up-form" action="{{ url('auth/sign-up') }}" method="post" data-reload-time="1500"
                          class="form form-action">
                        @include('common.loading')
                        <div class="form-group">
                            <label for="first-name-reg-form">{{__('First Name')}}</label>
                            <input class="form-control" type="text" id="first-name-reg-form" name="first_name"
                                   placeholder="{{__('First Name')}}">
                        </div>
                        <div class="form-group">
                            <label for="last-name-reg-form">{{__('Last Name')}}</label>
                            <input class="form-control" type="text" id="last-name-reg-form" name="last_name"
                                   placeholder="{{__('Last Name')}}">
                        </div>
                        <div class="form-group">
                            <label for="email-address-reg-form">{{__('Email address')}}</label>
                            <input class="form-control has-validation" data-validation="required|email" type="email"
                                   id="email-address-reg-form" name="email" placeholder="{{__('Email')}}">
                        </div>
                        <div class="form-group">
                            <label for="password-reg-form">{{__('Password')}}</label>
                            <input class="form-control has-validation" data-validation="required|min:6:ms"
                                   name="password" type="password" id="password-reg-form"
                                   placeholder="{{__('Password')}}">
                        </div>
                        <div class="form-group">
                            <div class="checkbox checkbox-success">
                                <input type="checkbox" id="term-condition" name="term_condition" value="1">
                                <label for="term-condition">
                                    {{ sprintf(__('I accept %s'), '<a href="javascript: void(0);" class="text-dark">'. __('Terms and Conditions') .'</a>') }}
                                </label>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-center">
                            <button class="btn btn-primary btn-block text-uppercase"
                                    type="submit"> {{__('Sign Up')}}</button>
                        </div>
                        <div class="form-message">

                        </div>
                    </form>

                    <div class="text-center">
                        <h5 class="mt-3 text-muted">{{__('Sign up using')}}</h5>
                        <ul class="social-list list-inline mt-3 mb-0">
                            @if(social_enable('facebook'))
                                <li class="list-inline-item">
                                    <a href="{{ FacebookLogin::get_inst()->getLoginUrl() }}"
                                       class="social-list-item border-primary text-primary"><i
                                            class="mdi mdi-facebook"></i></a>
                                </li>
                            @endif
                            @if(social_enable('google'))
                                <li class="list-inline-item">
                                    <a href="{{ GoogleLogin::get_inst()->getLoginUrl() }}"
                                       class="social-list-item border-danger text-danger"><i
                                            class="mdi mdi-google"></i></a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.modal -->
    <div id="hh-fogot-password-modal" class="modal fade modal-no-footer" tabindex="-1" role="dialog"
         aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-uppercase">{{__('Reset Password')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                    </button>
                </div>
                <div class="modal-body">
                    <form id="hh-reset-password-form" action="{{ url('auth/reset-password') }}" method="post"
                          data-reload-time="1500"
                          class="form form-action">
                        @include('common.loading')
                        <div class="form-group">
                            <label for="email-address-reset-pass-form">{{__('Email address')}}</label>
                            <input class="form-control has-validation" data-validation="required|email" type="email"
                                   id="email-address-reset-pass-form" name="email" placeholder="{{__('Email')}}">
                        </div>
                        <div class="form-group mb-0 text-center">
                            <button class="btn btn-primary btn-block text-uppercase"
                                    type="submit"> {{__('Reset Password')}}</button>
                        </div>
                        <div class="form-message">

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div><!-- /.modal -->
@endif
<div class="body-wrapper">
    <header id="header" class="header">
    <span class="d-block d-lg-none" id="toggle-mobile-menu"><span class="top"></span><span class="center"></span><span
            class="bottom"></span></span>
        <a href="{{ url('/') }}" id="logo">
            @php
                $logo = get_option('logo');
                $logo_url = get_attachment_url($logo);
            @endphp
            <img src="{{ $logo_url }}" alt="img-logo" class="img-logo">
        </a>
        <nav id="site-navigation" class="main-navigation d-none d-lg-block"
             aria-label="Primary Menu">
            <div class="menu-prmary-container">
                <?php
                if (has_nav_primary()) {
                    get_nav([
                        'location' => 'primary',
                        'walker' => 'main'
                    ]);
                }
                ?>
            </div>
        </nav><!-- #site-navigation -->
        <div id="right-navigation" class="right-navigation">
            <ul class="list-unstyled topnav-menu mb-0">
                @php
                    $langs = get_languages(true);
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
                    <li class="dropdown notification-list dropdown-language">
                        <a class="nav-item dropdown-toggle nav-user waves-effect waves-light" data-toggle="dropdown"
                           href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                            <span class="ml-1">
                                <img
                                    src="{{ esc_attr(asset('vendors/countries/flag/32x32/' . $current_lang['flag_code'] . '.png')) }}"/>
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
                                        <img
                                            src="{{ esc_attr(asset('vendors/countries/flag/32x32/' . $item['flag_code'] . '.png')) }}"/>
                                        {{$item['name']}}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </li>
                @endif
                @if (is_user_logged_in())
                    @php
                        $noti = Notifications::get_inst()->getLatestNotificationByUser(get_current_user_id(), 'to');

                        $args = [
                            'user_id' => get_current_user_id(),
                            'user_encrypt' => hh_encrypt(get_current_user_id())
                        ];

                    $userData = get_current_user_data();
                    @endphp
                    <li class="dropdown notification-list">
                        <a class="nav-item dropdown-toggle  waves-effect waves-light" data-toggle="dropdown" href="#"
                           role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="fe-bell noti-icon"></i>
                            @if($noti['total'])
                                <span
                                    class="badge badge-danger rounded-circle noti-icon-badge">{{ $noti['total'] }}</span>
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
                    <li class="dropdown notification-list">
                        <a class="nav-item dropdown-toggle nav-user waves-effect waves-light" data-toggle="dropdown"
                           href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                            <img src="{{ get_user_avatar($userData->getUserId(), [32,32]) }}" alt="user-image"
                                 class="rounded-circle">
                            <span class="pro-user-name ml-1">
                            {{ get_username($userData->getUserId()) }} <i class="mdi mdi-chevron-down"></i>
                        </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                            <!-- item-->
                            <div class="dropdown-header noti-title">
                                <h6 class="text-overflow">{{__('Welcome !')}}</h6>
                            </div>
                            <!-- item-->
                            <a href="{{ dashboard_url('profile') }}" class="dropdown-item notify-item">
                                <i class="fe-user"></i>
                                <span>{{__('Profile')}}</span>
                            </a>
                        @if(is_admin() || is_partner())
                            <!-- item-->
                                <a href="{{ dashboard_url('my-home') }}" class="dropdown-item notify-item">
                                    <i class="fe-book-open"></i>
                                    <span>{{__('My Homes')}}</span>
                                </a>
                        @endif
                        @if(is_admin())
                            <!-- item-->
                                <a href="{{ dashboard_url('settings') }}" class="dropdown-item notify-item">
                                    <i class="fe-settings "></i>
                                    <span>{{__('Settings')}}</span>
                                </a>
                        @endif
                        <!-- item-->
                        @php
                            $data = [
                                'user_id' => $userData->getUserId(),
                                'redirect_url' => current_url()
                            ];
                        @endphp
                        <!-- item-->
                            <a href="{{ dashboard_url('/') }}" class="dropdown-item notify-item">
                                <i class="fe-stop-circle "></i>
                                <span>{{__('Dashboard')}}</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0)" data-action="{{ auth_url('logout') }}"
                               data-params="{{ base64_encode(json_encode($data)) }}"
                               class="dropdown-item notify-item hh-link-action">
                                <i class="fe-log-out"></i>
                                <span>{{__('Logout')}}</span>
                            </a>
                        </div>
                    </li>
                @else
                    <li>
                        <a href="javascript: void(0);" class="nav-item "
                           data-toggle="modal"
                           data-target="#hh-login-modal">{{__('Login')}}</a> /
                        <a href="javascript: void(0);" class="nav-item pl-0"
                           data-toggle="modal"
                           data-target="#hh-register-modal">{{__('Register')}}</a>
                    </li>
                @endif
            </ul>
        </div>
    </header>
