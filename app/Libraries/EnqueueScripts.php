<?php

class EnqueueScripts
{
    private static $styles = [];
    private static $scripts = [];

    private static $enqueuedStyles = [];
    private static $enqueuedScripts = [];

    public function __construct()
    {
        add_action('init', [$this, '_registerScripts']);
        add_action('header', [$this, '_enqueueHeader']);
        add_action('footer', [$this, '_enqueueFooter']);
        add_action('hh_updated_option', [$this, '_applySSL']);
    }

    public function _applySSL($option_Value)
    {
        $option_Value = unserialize($option_Value);
        if (isset($option_Value['use_ssl']) && $option_Value['use_ssl'] == 'on') {
            updateEnv('APP_ENV', 'production_ssl');
        } else {
            updateEnv('APP_ENV', 'local');
        }
    }

    public function _registerScripts()
    {
        $this->addScript('image-loaded-js', asset('vendors/imagesloaded.pkgd.js'), false, true);
        $this->addScript('jquery-ui-js', asset('vendors/jquery-ui/jquery-ui.js'), false, true);
        $this->addScript('bootstrap-validate-js', asset('vendors/bootstrap-validate.js'), false, true);
        $this->addScript('toast-js', asset('vendors/jquery-toast/jquery.toast.js'), false, true);
        $this->addScript('bootstrap-maxlength-js', asset('vendors/bootstrap-maxlength/bootstrap-maxlength.js'), false, true);

        if (get_option('use_google_captcha') == 'on') {
            $this->addScript('google-captcha', 'https://www.google.com/recaptcha/api.js?render=' . get_option('google_captcha_site_key'), false, true);
        }

        $this->addScript('nested-sort-js', asset('vendors/jquery.mjs.nestedSortable.js'), false, true);

        $this->addScript('nice-select-js', asset('vendors/jquery-nice-select/jquery.nice-select.js'));
        $this->addStyle('nice-select-css', asset('vendors/jquery-nice-select/nice-select.css'));

        $this->addScript('select2-js', asset('vendors/select2/select2.js'));
        $this->addStyle('select2-css', asset('vendors/select2/select2.css'));

        $this->addScript('switchery-js', asset('vendors/switchery/switchery.js'));
        $this->addStyle('switchery-css', asset('vendors/switchery/switchery.css'));

        $this->addScript('flatpickr-js', asset('vendors/flatpickr/flatpickr.js'));
        $this->addStyle('flatpickr-css', asset('vendors/flatpickr/flatpickr.css'));

        $this->addScript('bootstrap-colorpicker-js', asset('vendors/bootstrap-colorpicker/bootstrap-colorpicker.js'));
        $this->addStyle('bootstrap-colorpicker-css', asset('vendors/bootstrap-colorpicker/bootstrap-colorpicker.css'));

        $this->addScript('mapbox-gl-js', asset('vendors/mapbox/mapbox-gl.js'));
        $this->addScript('mapbox-gl-geocoder-js', asset('vendors/mapbox/mapbox-gl-geocoder.js'));
        $this->addStyle('mapbox-gl-css', asset('vendors/mapbox/mapbox-gl.css'));
        $this->addStyle('mapbox-gl-geocoder-css', asset('vendors/mapbox/mapbox-gl-geocoder.css'));

        $this->addScript('dropzone-js', asset('vendors/dropzone/dropzone.min.js'));
        $this->addStyle('dropzone-css', asset('vendors/dropzone/dropzone.min.css'));

        $this->addScript('datatables-js', asset('vendors/datatables/datatable.js'));
        $this->addScript('pdfmake-js', asset('vendors/pdfmake/pdfmake.js'));
        $this->addScript('vfs-fonts-js', asset('vendors/pdfmake/vfs_fonts.js'));
        $this->addStyle('datatables-css', asset('vendors/datatables/datatable.css'));

        $this->addScript('tinymce-js', asset('vendors/tinymce/tinymce.min.js'));

        $this->addScript('confirm-js', asset('vendors/confirm/jquery-confirm.js'));
        $this->addStyle('confirm-css', asset('vendors/confirm/jquery-confirm.css'));

        $this->addScript('tagify-js', asset('vendors/tagify/tagify.min.js'));
        $this->addStyle('tagify-css', asset('vendors/tagify/tagify.css'));

        $this->addScript('light-gallery-js', asset('vendors/lightGallery/js/lightgallery.js'));
        $this->addStyle('light-gallery-css', asset('vendors/lightGallery/css/lightgallery.css'));

        $this->addScript('daterangepicker-js', asset('vendors/daterangepicker/daterangepicker.js'));
        $this->addStyle('daterangepicker-css', asset('vendors/daterangepicker/daterangepicker.css'));
        $lang = str_replace('_', '-', app()->getLocale());
        $this->addScript('daterangepicker-lang-js', asset('vendors/daterangepicker/languages/' . $lang . '.js'));

        $this->addStyle('home-slider', asset('vendors/slider/css/style.css'));
        $this->addScript('home-slider', asset('vendors/slider/js/slider.js'));

        $this->addStyle('iconrange-slider', asset('vendors/ion-rangeslider/ion.rangeSlider.css'));
        $this->addScript('iconrange-slider', asset('vendors/ion-rangeslider/ion.rangeSlider.js'));

        $this->addStyle('range-slider', asset('vendors/rangeslider/rangeslider.css'));
        $this->addScript('range-slider', asset('vendors/rangeslider/rangeslider.js'));

        $this->addScript('flot', asset('vendors/flot-charts/jquery.flot.js'));
        $this->addScript('flot-time', asset('vendors/flot-charts/jquery.flot.time.js'));
        $this->addScript('flot-tooltip', asset('vendors/flot-charts/jquery.flot.tooltip.min.js'));
        $this->addScript('flot-crosshair', asset('vendors/flot-charts/jquery.flot.crosshair.js'));
        $this->addScript('flot-selection', asset('vendors/flot-charts/jquery.flot.selection.js'));

        $this->addScript('nicescroll-js', asset('vendors/jquery.nicescroll.js'));
        $this->addScript('scroll-magic-js', asset('vendors/scroll-magic.js'));

        $this->addScript('owl-carousel', asset('vendors/owl-carousel/owl.carousel.min.js'));
        $this->addStyle('owl-carousel', asset('vendors/owl-carousel/assets/owl.carousel.min.css'));
        $this->addStyle('owl-carousel-theme', asset('vendors/owl-carousel/assets/owl.theme.default.min.css'));

        $this->addScript('context-menu-pos', asset('vendors/jquery-contextmenu/jquery.ui.position.min.js'));
        $this->addScript('context-menu', asset('vendors/jquery-contextmenu/jquery.contextMenu.min.js'));
        $this->addStyle('context-menu', asset('vendors/jquery-contextmenu/jquery.contextMenu.min.css'));

        $this->addScript('search-js', asset('js/search.js'));

        do_action('hh_register_scripts', $this);
    }

    public function _enqueueHeader()
    {
        $this->styleRender(true);
        $this->scriptRender(true);
    }

    public function _enqueueFooter()
    {
        $this->styleRender(false);
        $this->scriptRender(false);
    }

    public function addStyle($name, $url, $in_header = false, $queue = false)
    {
        if (!isset(self::$styles[$name])) {
            self::$styles[$name] = [
                'name' => $name,
                'url' => $url,
                'queue' => $queue,
                'header' => $in_header
            ];
        }
    }

    public function addScript($name, $url, $in_header = false, $queue = false)
    {
        if (!isset(self::$scripts[$name])) {
            self::$scripts[$name] = [
                'name' => $name,
                'url' => $url,
                'queue' => $queue,
                'header' => $in_header
            ];
        }
    }

    public function enqueueStyles()
    {
        foreach (self::$styles as $name => $style) {
            $this->_enqueueStyle($name);
        }
    }

    public function enqueueScripts()
    {
        foreach (self::$scripts as $name => $script) {
            $this->_enqueueScript($name);
        }
    }

    public function _enqueueScript($name)
    {
        if (isset(self::$scripts[$name])) {
            self::$scripts[$name]['queue'] = true;
        }
    }

    public function _enqueueStyle($name)
    {
        if (isset(self::$styles[$name])) {
            self::$styles[$name]['queue'] = true;
        }

    }

    public function styleRender($in_header = false)
    {
        foreach (self::$styles as $name => $style) {
            if ($style['queue'] && $style['header'] == $in_header && !in_array($name, self::$enqueuedStyles)) {
                self::$enqueuedStyles[] = $name;
                echo '<link href="' . $style['url'] . '" rel="stylesheet" type="text/css">' . "\r\n";
            }
        }
    }

    public function scriptRender($in_header = false)
    {
        foreach (self::$scripts as $name => $script) {
            if ($script['queue'] && $script['header'] == $in_header && !in_array($name, self::$enqueuedScripts)) {
                self::$enqueuedScripts[] = $name;
                echo '<script src="' . $script['url'] . '"></script>' . "\r\n";
            }
        }
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
