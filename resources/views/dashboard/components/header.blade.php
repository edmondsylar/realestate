<?php do_action('init'); ?>
<?php do_action('admin_init'); ?>
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

    <title>{{ page_title(true) }}</title>

    <link href="{{asset('css/vendor.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css">

    <link href="{{asset('css/main.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/option.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/dashboard.min.css')}}" rel="stylesheet" type="text/css">
    <script>
        var hh_params = {
            'isValidated': {},
            'mapbox_token': '<?php echo e(get_option('mapbox_key')) ?>',
            'currency': <?php echo json_encode(current_currency()) ?>,
            'add_media_url' : '<?php echo e(dashboard_url('add-media')); ?>',
            'facebook_login': '<?php echo e(get_option('facebook_login', 'off')); ?>',
            'facebook_api': '<?php echo e(get_option('facebook_api')); ?>',
            'use_google_captcha': '<?php echo e(get_option('use_google_captcha', 'off')); ?>',
            'google_captcha_key': '<?php echo e(get_option('google_captcha_site_key')) ?>'
        };
    </script>
    <?php do_action('header'); ?>
</head>
<body class="awe-booking {{ isset($bodyClass)? $bodyClass: '' }}">
@include('common.loading', ['class' => 'page-loading'])
