<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                &copy;{{date('Y')}} by <a href="{{ url('/') }}">{{ get_option('site_name', '') }}</a>
            </div>
            <div class="col-md-6">
                <div class="text-md-right footer-links d-none d-sm-block">
                    <a href="javascript:void(0);">{{__('About Us')}}</a>
                    <a href="javascript:void(0);">{{__('Help')}}</a>
                    <a href="javascript:void(0);">{{__('Contact Us')}}</a>
                </div>
            </div>
        </div>
    </div>
</footer>