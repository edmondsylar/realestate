function whichTransitionEvent() {
    var t;
    var el = document.createElement('fakeelement');
    var transitions = {
        'transition': 'transitionend',
        'OTransition': 'oTransitionEnd',
        'MozTransition': 'transitionend',
        'WebkitTransition': 'webkitTransitionEnd'
    };

    for (t in transitions) {
        if (el.style[t] !== undefined) {
            return transitions[t];
        }
    }
}

Object.size = function (obj) {
    let size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};
jQuery(document).ready(function () {
    setTimeout(function () {
        $('.page-loading').hide();
    }, 500);
});

(function ($) {
    'use strict';

    let body = $('body');

    let HHActions = {
        init: function (el) {
            this.initGlobal(el);
            this.initMobileMenu(body);
            this.initDatePicker(body);
            this.initUpDownNumber(body);
            this.initMapbox(body);
            this.initCheckboxAction(el);
            this.initSelectAction(el);
            this.initLinkAction(el);
            this.initFormAction(el);
            this.initTable(el);
            this.initValidation(el);
            this.initOnOff(body);
            this.initRangeSlider(body);
            this.initSlider(body);
            this.initOwlCarousel(body);
            this.initModelContent(body);
            this.initMatchHeight(body);
            this.initScroll(body);
            this.initSelect(body);
        },
        initSelect: function (el) {
            $.fn.niceSelect && $('[data-plugin="customselect"]', el).niceSelect();
            $.fn.select2 && $('[data-toggle="select2"]', el).select2();
        },
        initScroll: function (el) {
            setTimeout(function () {
                $.fn.slimScroll && $('.hh-slimscroll', el).slimScroll({
                    height: false,
                    alwaysVisible: false,
                    railVisible: false,
                    railOpacity: 0,
                    wheelStep: 10,
                    size: 8,
                    color: "#CCC",
                    allowPageScroll: false,
                    disableFadeOut: false
                });
            }, 500);
        },
        initOwlCarousel: function (el) {
            $.fn.owlCarousel && $('.hh-carousel', el).each(function () {
                let t = $(this),
                    owl = $('.owl-carousel', t),
                    responsive = JSON.parse(Base64.decode(t.data('responsive'))),
                    margin = t.data('margin'),
                    loop = t.data('loop');

                let options = {
                    margin: (typeof margin != 'undefined') ? margin : 15,
                    loop: loop
                };
                if (typeof responsive == 'object') {
                    options['responsive'] = responsive;
                    options['onChanged'] = callbackOwl;
                }
                owl.owlCarousel(options);

                $('.next', t).click(function () {
                    owl.trigger('next.owl.carousel');
                });
                $('.prev', t).click(function () {
                    owl.trigger('prev.owl.carousel');
                });
            });

            function callbackOwl(event) {
                var element = event.target;
                var pages = event.page.count;
                var page = event.page.index;
                if (page == -1) {
                    $(element).closest('.hh-carousel').find('.owl-nav .prev').addClass('disabled');
                } else {
                    $(element).closest('.hh-carousel').find('.owl-nav .prev').removeClass('disabled');
                }
                if (page >= pages) {
                    $(element).closest('.hh-carousel').find('.owl-nav .next').addClass('disabled');
                } else {
                    $(element).closest('.hh-carousel').find('.owl-nav .next').removeClass('disabled');
                }
            }
        },
        initModelContent: function (el) {
            $('.hh-get-modal-content', el).on('show.bs.modal', function (ev) {
                var t = $(this),
                    loader = $('.hh-loading', t),
                    target = $(ev.relatedTarget);

                var data = JSON.parse(Base64.decode(target.attr('data-params')));
                if (typeof data == 'object') {
                    data['_token'] = $('meta[name="csrf-token"]').attr('content');

                    loader.show();
                    $('.modal-body', t).empty();

                    $.post(t.attr('data-url'), data, function (respon) {
                        if (typeof respon == 'object') {
                            if (respon.status === 1) {
                                $('.modal-body', t).html(respon.html);
                                $('body').trigger('hh_modal_render_content', [t]);
                            } else {
                                base.alert(respon);
                            }
                        }
                        loader.hide();
                    }, 'json');
                } else {
                    alert('Have a error when parse the data');
                }
            });
        },
        initSlider: function (el) {
            $.fn.otsSlider && $('[data-slider="ots-slider"]', el).otsSlider({
                autoplay: true,
                control: false,
                pagination: false,
                effect: 'ots-slider-fade'
            });

            $.fn.otsSlider && $('[data-slider="ots-stick-slider"]', el).otsSlicker({
                items: 5,
                margin: 10
            });
        },
        initRangeSlider: function (el) {
            $('[data-plugin="ion-range-slider"]', el).each(function () {
                let elRanger = $(this);
                elRanger.ionRangeSlider({
                    skin: $(this).data('skin'),
                    min: parseFloat($(this).data().min),
                    max: parseFloat($(this).data().max),
                    from: parseFloat($(this).data().from),
                    to: parseFloat($(this).data().to),
                    type: "double",
                    prefix: $(this).data().prefix,
                    onFinish: function (data) {
                        elRanger.trigger('hh_ranger_changed');
                    },
                });
            });
        },
        initOnOff: function (el) {
            if ($('[data-plugin="switchery"]', el).length) {
                $('[data-plugin="switchery"]', el).each(function () {
                    let el = $(this).get(0);
                    let size = $(this).attr('data-size');
                    new Switchery(el, {
                        color: $(this).data('color'),
                        size: typeof size == 'string' ? size : 'default'
                    });
                });
            }
        },
        initUpDownNumber: function (el) {
            $('.guest-group', el).each(function () {
                let parent = $(this),
                    button = $('.dropdown-toggle', parent);
                $('input[type="number"]', el).focus(function () {
                    $(this).blur();
                });

                function renderText(button, parent) {
                    let html = '';
                    let adult = parseInt($('input[name="num_adults"]', parent).val()),
                        child = parseInt($('input[name="num_children"]', parent).val()),
                        infant = parseInt($('input[name="num_infants"]', parent).val());
                    if (adult + child >= 2) {
                        html += (adult + child) + ' ' + button.data('text-guests');
                    } else {
                        html += (adult + child) + ' ' + button.data('text-guest');
                    }
                    if (infant > 0) {
                        if (infant >= 2) {
                            html += ', ' + infant + ' ' + button.data('text-infants');
                        } else {
                            html += ', ' + infant + ' ' + button.data('text-infant');
                        }
                    }
                    button.text(html);
                }

                renderText(button, parent);
                $('.decrease', parent).click(function (ev) {
                    ev.preventDefault();
                    ev.stopPropagation();
                    let input = $(this).parent().find('input'),
                        min = parseInt(input.attr('min')),
                        step = parseInt(input.attr('step')),
                        val = parseInt(input.val());
                    if (val - step >= min) {
                        val -= step;
                    }
                    input.val(val).change();
                    renderText(button, parent);
                });
                $('.increase', parent).click(function (ev) {
                    ev.preventDefault();
                    ev.stopPropagation();
                    let input = $(this).parent().find('input'),
                        max = parseInt(input.attr('max')),
                        step = parseInt(input.attr('step')),
                        val = parseInt(input.val());
                    if (val + step <= max) {
                        val += step;
                    }
                    input.val(val).change();
                    renderText(button, parent);
                });
            });
        },
        initDatePicker: function (el) {
            $('.hh-search-form .date', el).each(function () {
                let t = $(this),
                    checkIn = $('.check-in-field', t),
                    checkInRender = $('.check-in-render', t),
                    checkOut = $('.check-out-field', t),
                    checkInOutRender = $('.check-out-render', t),
                    input = $('.check-in-out-field', t);
                let singlePicker = (t.hasClass('date-single'));
                let options = {
                    onlyShowCurrentMonth: true,
                    showCalendar: false,
                    alwaysShowCalendars: false,
                    singleDatePicker: singlePicker,
                    sameDate: singlePicker,
                    autoApply: true,
                    disabledPast: true,
                    dateFormat: 'YYYY-MM-DD',
                    enableLoading: true,
                    showEventTooltip: true,
                    customClass: 'hh-search-form-calendar',
                    classNotAvailable: ['disabled', 'off'],
                    disableHighLight: true,
                    startDate: moment().startOf('day'),
                    endDate: moment().add(1, 'days').startOf('day'),
                    autoResponsive: true,
                };
                if (typeof locale_daterangepicker === 'object') {
                    options.locale = locale_daterangepicker;
                }
                input.daterangepicker(options,
                    function (start, end, label) {
                        checkIn.val(start.format('YYYY-MM-DD'));
                        checkInRender.text(start.format(checkInRender.data('date-format')));
                        checkOut.val(end.format('YYYY-MM-DD'));
                        checkInOutRender.text(end.format(checkInOutRender.data('date-format')));
                        input.trigger('daterangepicker_change', [start, end, label]);
                    });
                checkInRender.click(function () {
                    input.trigger('click');
                });
                checkInOutRender.click(function () {
                    input.trigger('click');
                });

                let dp = input.data('daterangepicker');
                dp.updateView();
                dp.show();
                dp.hide();
            });

            $('#form-book-home .date-double', body).each(function () {
                let t = $(this),
                    form = t.closest('form'),
                    checkIn = $('.check-in-field', t),
                    checkInRender = $('.check-in-render', t),
                    checkOut = $('.check-out-field', t),
                    checkInOutRender = $('.check-out-render', t),
                    input = $('.check-in-out-field', t);
                let options = {
                    parentEl: input,
                    onlyShowCurrentMonth: true,
                    showCalendar: false,
                    alwaysShowCalendars: false,
                    singleDatePicker: false,
                    sameDate: false,
                    autoApply: true,
                    disabledPast: true,
                    dateFormat: 'YYYY-MM-DD',
                    enableLoading: true,
                    showEventTooltip: true,
                    customClass: '',
                    classNotAvailable: ['disabled', 'off'],
                    startDate: moment().startOf('day'),
                    endDate: moment().add(1, 'days').startOf('day'),
                    disableHighLight: true,
                    autoResponsive: true,
                    maybeFixed: '.form-book',
                    fetchEvents: function (start, end, el, callback) {
                        let events = [];
                        if (el.flag_get_events) {
                            return false;
                        }
                        el.flag_get_events = true;
                        el.container.find('.hh-loading').show();
                        let data = {
                            startTime: start.format('YYYY-MM-DD'),
                            endTime: end.format('YYYY-MM-DD'),
                            homeID: input.data('home-id'),
                            homeEncrypt: input.data('home-encrypt'),
                            numberAdult: $('input[name="num_adults"]', form).val(),
                            numberChild: $('input[name="num_children"]', form).val(),
                            numberInfant: $('input[name="num_infants"]', form).val(),
                            _token: $('meta[name="csrf-token"]').attr('content')
                        };
                        $.post(t.attr('data-action'), data, function (respon) {
                            if (typeof respon === 'object') {
                                if (typeof respon.events === 'object') {
                                    events = respon.events;
                                }
                            } else {
                                console.log('Can not get data');
                            }
                            callback(events, el);
                            el.flag_get_events = false;
                            el.container.find('.hh-loading').hide();
                        }, 'json');
                    }
                };
                if (typeof locale_daterangepicker === 'object') {
                    options.locale = locale_daterangepicker;
                }
                input.daterangepicker(options,
                    function (start, end, label) {
                        checkIn.val(start.format('YYYY-MM-DD'));
                        checkInRender.text(start.format(checkInRender.data('date-format')));
                        checkOut.val(end.format('YYYY-MM-DD'));
                        checkInOutRender.text(end.format(checkInOutRender.data('date-format')));
                        input.trigger('daterangepicker_change', [start, end, label]);
                    });
                checkInRender.click(function () {
                    input.trigger('click');
                });
                checkInOutRender.click(function () {
                    input.trigger('click');
                });

                let dp = input.data('daterangepicker');

                dp.updateView();
                dp.show();
                dp.hide();

            });

            $('#form-book-home .date-single', body).each(function () {
                let t = $(this),
                    form = t.closest('form'),
                    checkIn = $('.check-in-field', t),
                    checkInRender = $('.check-in-render', t),
                    checkOut = $('.check-out-field', t),
                    input = $('.check-in-out-single-field', t);
                let options = {
                    parentEl: input,
                    onlyShowCurrentMonth: true,
                    showCalendar: false,
                    alwaysShowCalendars: false,
                    singleDatePicker: true,
                    sameDate: true,
                    autoApply: true,
                    disabledPast: true,
                    dateFormat: 'YYYY-MM-DD',
                    enableLoading: true,
                    showEventTooltip: true,
                    customClass: '',
                    classNotAvailable: ['disabled', 'off'],
                    startDate: moment().startOf('day'),
                    endDate: moment().startOf('day'),
                    disableHighLight: true,
                    autoResponsive: true,
                    maybeFixed: '.form-book',
                    fetchEvents: function (start, end, el, callback) {
                        let events = [];
                        if (el.flag_get_events) {
                            return false;
                        }
                        el.flag_get_events = true;
                        el.container.find('.hh-loading').show();
                        let data = {
                            startTime: start.format('YYYY-MM-DD'),
                            endTime: end.format('YYYY-MM-DD'),
                            homeID: input.data('home-id'),
                            homeEncrypt: input.data('home-encrypt'),
                            numberAdult: $('input[name="num_adults"]', form).val(),
                            numberChild: $('input[name="num_children"]', form).val(),
                            numberInfant: $('input[name="num_infants"]', form).val(),
                            _token: $('meta[name="csrf-token"]').attr('content')
                        };
                        $.post(t.attr('data-action'), data, function (respon) {
                            if (typeof respon === 'object') {
                                if (typeof respon.events === 'object') {
                                    events = respon.events;
                                }
                            } else {
                                console.log('Can not get data');
                            }
                            callback(events, el);
                            el.flag_get_events = false;
                            el.container.find('.hh-loading').hide();
                        }, 'json');
                    }
                };
                if (typeof locale_daterangepicker === 'object') {
                    options.locale = locale_daterangepicker;
                }
                input.daterangepicker(options,
                    function (start, end, label) {
                        checkIn.val(start.format('YYYY-MM-DD'));
                        checkInRender.text(start.format(checkInRender.data('date-format')));
                        checkOut.val(end.format('YYYY-MM-DD'));

                        input.trigger('daterangepicker_change', [start, end, label]);
                    });
                let dp = input.data('daterangepicker');
                dp.updateView();
                dp.show();
                dp.hide();

                checkInRender.click(function () {
                    dp.show();
                });
            });

            $('.hh-search-bar-buttons .button-date', body).each(function (e) {
                let t = $(this),
                    checkInOut = $('.check-in-out-field', t),
                    checkIn = $('.check-in-field', t),
                    checkOut = $('.check-out-field', t),
                    render = $('.text', t);

                let singlePicker = (t.hasClass('button-date-single'));

                let options = {
                    onlyShowCurrentMonth: true,
                    showCalendar: false,
                    alwaysShowCalendars: false,
                    singleDatePicker: singlePicker,
                    sameDate: singlePicker,
                    autoApply: true,
                    disabledPast: true,
                    dateFormat: 'YYYY-MM-DD',
                    enableLoading: true,
                    showEventTooltip: true,
                    customClass: '',
                    classNotAvailable: ['disabled', 'off'],
                    startDate: moment(checkIn.val(), 'YYYY-MM-DD'),
                    endDate: moment(checkOut.val(), 'YYYY-MM-DD'),
                    disableHighLight: true,
                    autoResponsive: true,
                };

                if (typeof locale_daterangepicker === 'object') {
                    options.locale = locale_daterangepicker;
                }
                checkInOut.daterangepicker(options,
                    function (start, end, label) {
                        checkIn.val(start.format('YYYY-MM-DD'));
                        checkOut.val(end.format('YYYY-MM-DD'));
                        if(singlePicker){
                            render.text(start.format(t.data('date-format')) );
                        }else{
                            render.text(start.format(t.data('date-format')) + ' - ' + end.format(t.data('date-format')));
                        }
                        checkInOut.trigger('daterangepicker_change', [start, end, label]);
                    });

                let dp = checkInOut.data('daterangepicker');

                dp.updateView();
                dp.show();
                dp.hide();

                t.click(function () {
                    dp.show();
                });

            });

            setTimeout(function () {
                $('.filter-mobile-box .button-date', body).each(function (e) {
                    let t = $(this),
                        checkInOut = $('.check-in-out-field', t),
                        checkIn = $('.check-in-field', t),
                        checkOut = $('.check-out-field', t),
                        render = $('.text', t);

                    let singlePicker = (t.hasClass('button-date-single'));

                    let options = {
                        parentEl: t,
                        onlyShowCurrentMonth: true,
                        showCalendar: true,
                        alwaysShowCalendars: true,
                        singleDatePicker: singlePicker,
                        sameDate: singlePicker,
                        autoApply: true,
                        disabledPast: true,
                        dateFormat: 'YYYY-MM-DD',
                        enableLoading: true,
                        showEventTooltip: true,
                        customClass: 'calendar-popup-filter',
                        classNotAvailable: ['disabled', 'off'],
                        startDate: moment(checkIn.val(), 'YYYY-MM-DD'),
                        endDate: moment(checkOut.val(), 'YYYY-MM-DD'),
                        disableHighLight: true,
                        autoResponsive: true,
                    };

                    if (typeof locale_daterangepicker === 'object') {
                        options.locale = locale_daterangepicker;
                    }
                    checkInOut.daterangepicker(options,
                        function (start, end, label) {
                            checkIn.val(start.format('YYYY-MM-DD'));
                            checkOut.val(end.format('YYYY-MM-DD'));
                            console.log(singlePicker);
                            if(singlePicker){
                                render.text(start.format(t.data('date-format')));
                            }else{
                                render.text(start.format(t.data('date-format')) + ' - ' + end.format(t.data('date-format')));
                            }
                            checkInOut.trigger('daterangepicker_change', [start, end, label]);
                        });

                    let dp = checkInOut.data('daterangepicker');

                    dp.updateView();
                    dp.show();
                    dp.hide();
                });
            }, 200);
        },
        initMapbox: function (el) {
            if (typeof mapboxgl === 'object' && hh_params.mapbox_token) {
                mapboxgl.accessToken = hh_params.mapbox_token;
                $('.hh-mapbox-single', el).each(function () {
                    let t = $(this),
                        lat = parseFloat(t.data('lat')),
                        lng = parseFloat(t.data('lng')),
                        zoom = parseFloat(t.data('zoom'));

                    let map = new mapboxgl.Map({
                        container: t.get(0),
                        style: 'mapbox://styles/mapbox/light-v10',
                        center: [lng, lat],
                        zoom: zoom
                    });

                    map.scrollZoom.disable();
                    let el = document.createElement('div');
                    el.className = 'hh-marker';
                    new mapboxgl.Marker(el)
                        .setLngLat([lng, lat])
                        .addTo(map);
                    map.on('style.load', function () {
                        map.addSource('markers', {
                            "type": "geojson",
                            "data": {
                                "type": "FeatureCollection",
                                "features": [{
                                    "type": "Feature",
                                    "geometry": {
                                        "type": "Point",
                                        "coordinates": [lng, lat]
                                    },
                                    "properties": {
                                        "modelId": 1,
                                    },
                                }]
                            }
                        });
                        map.addLayer({
                            "id": "circles1",
                            "source": "markers",
                            "type": "circle",
                            "paint": {
                                "circle-radius": 100,
                                "circle-color": "#969696",
                                "circle-opacity": 0.2,
                                "circle-stroke-width": 0,
                            },
                            "filter": ["==", "modelId", 1],
                        });
                    });
                });
                $('[data-plugin="mapbox-geocoder"]', body).each(function () {
                    let t = $(this);
                    if (typeof mapboxgl === 'object' && mapboxgl.accessToken != '') {
                        let geocoder = new MapboxGeocoder({
                            accessToken: mapboxgl.accessToken,
                            mapboxgl: mapboxgl,
                            language: 'en-US',
                            placeholder: t.data().placeholder
                        });
                        let map = new mapboxgl.Map({
                                style: 'mapbox://styles/mapbox/light-v10',
                                container: t.next('.map').get(0)
                            },
                        );

                        t.get(0).appendChild(geocoder.onAdd(map));

                        let oldVal = t.data().value;
                        if (typeof oldVal === 'string') {
                            geocoder.setInput(oldVal);
                        }
                        geocoder.on('result', function (result) {
                            if (typeof result.result.geometry.coordinates === 'object') {
                                t.closest('.form-group').find('input[name="lng"]').attr('value', result.result.geometry.coordinates[0]).trigger('change');
                                t.closest('.form-group').find('input[name="lat"]').attr('value', result.result.geometry.coordinates[1]).trigger('change');
                                t.closest('.form-group').find('input[name="address"]').attr('value', result.result.place_name).trigger('change');
                            }
                        });
                        map.on('load', function () {
                            if ($('.mapboxgl-ctrl-geocoder--input', t).val() === '' && t.data('current-location') == '1') {
                                if (navigator.geolocation) {
                                    navigator.geolocation.getCurrentPosition(function (position) {
                                        if (typeof position === 'object') {
                                            t.closest('.form-group').find('input[name="lat"]').attr('value', position.coords.latitude).trigger('change');
                                            t.closest('.form-group').find('input[name="lng"]').attr('value', position.coords.longitude).trigger('change');
                                            t.closest('.form-group').find('input[name="address"]').attr('value', t.data('your-location')).trigger('change');
                                            geocoder.setInput(t.data('your-location'));
                                        }
                                    });
                                }
                            }
                            $('.mapboxgl-ctrl-geocoder--input', t).trigger('hh_mapbox_input_load');

                            $('.mapboxgl-ctrl-geocoder--input', t).on('focus', function () {
                                $(this).trigger('hh_mapbox_input_focus');
                            });
                            $('.mapboxgl-ctrl-geocoder--input', t).on('blur', function () {
                                $(this).trigger('hh_mapbox_input_blur');
                            });
                        });
                    }
                });

                if ($('#contact-us-map', 'body').length) {
                    let t = $('#contact-us-map', 'body'),
                        map_content = $('.map-render', t),
                        lat = parseFloat(t.data('lat')),
                        lng = parseFloat(t.data('lng'));

                    let map = new mapboxgl.Map({
                        container: map_content.get(0),
                        style: 'mapbox://styles/mapbox/light-v10',
                        center: [lng - 0.018, lat],
                        zoom: 14,
                        offset: [500, 0],
                    });
                    map.scrollZoom.disable();

                    let el = document.createElement('div');
                    el.className = 'hh-marker-contact';
                    new mapboxgl.Marker(el)
                        .setLngLat([lng, lat])
                        .addTo(map);
                    map.on('style.load', function () {
                        map.addSource('markers', {
                            "type": "geojson",
                            "data": {
                                "type": "FeatureCollection",
                                "features": [{
                                    "type": "Feature",
                                    "geometry": {
                                        "type": "Point",
                                        "coordinates": [lng, lat]
                                    },
                                    "properties": {
                                        "modelId": 1,
                                    },
                                }]
                            }
                        });
                        map.addLayer({
                            "id": "circles1",
                            "source": "markers",
                            "type": "circle",
                            "paint": {
                                "circle-radius": 100,
                                "circle-color": "#969696",
                                "circle-opacity": 0.2,
                                "circle-stroke-width": 0,
                            },
                            "filter": ["==", "modelId", 1],
                        });
                    });
                    let rs;
                    clearTimeout(rs);
                    $(window).on('resize', function () {
                        rs = setTimeout(function () {
                            if (window.matchMedia("(max-width:767px)").matches) {
                                map.setCenter([lng, lat]);
                            } else {
                                if (window.matchMedia("(max-width:991px)").matches) {
                                    map.setCenter([lng - 0.01, lat]);
                                } else {
                                    map.setCenter([lng - 0.018, lat]);
                                }
                            }
                        }, 500);
                    }).resize();
                }
            }
        },
        initMatchHeight: function (el) {
            $.fn.matchHeight && $('[data-plugin="matchHeight"]', el).matchHeight();
        },
        initMobileMenu: function (el) {
            let container = $('#mobile-navigation', el);

            container.click(function (ev) {
                if ($(ev.target).is(container)) {
                    $('.mobile-menu', container).removeClass('open');
                    container.removeClass('open-menu deep');
                    $('.body-wrapper', body).removeClass('open-menu');
                }
            });
            $('#toggle-mobile-menu', body).click(function () {
                container.addClass('open-menu');
                $('.body-wrapper', body).addClass('open-menu');
                let zindex = 9 - $('.mobile-menu', container).find('ul').length;
                $('.mobile-menu', container).addClass('open').css({'z-index': zindex});
            });
            $('.toggle-submenu', container).click(function (ev) {
                ev.preventDefault();
                let menu = $(this).next('ul.sub-menu');
                let parent = $(this).closest('ul');
                parent.addClass('deep');
                let zindex = 9 - menu.find('ul').length;
                menu.addClass('open').css({'z-index': zindex});
            });
            $('.submenu-head', container).click(function (ev) {
                ev.preventDefault();
                let menu = $(this).closest('ul');
                let parent = menu.closest('li').parent('ul');
                menu.removeClass('open').css({'z-index': ''});
                parent.removeClass('deep');
            });
        },
        initSelectAction: function (el) {
            var base = this;
            $('body').on('change', '.hh-select-action', function (ev) {
                var t = $(this),
                    parent = t.closest(t.data('parent'));
                ev.preventDefault();
                var data = JSON.parse(Base64.decode(t.attr('data-params')));
                if (typeof data == 'object') {
                    data['val'] = t.val();
                    data['_token'] = $('meta[name="csrf-token"]').attr('content');
                    if ($(parent).length) {
                        $(parent).addClass('is-doing');
                    }
                    $.post(t.attr('data-action'), data, function (respon) {
                        if (typeof respon == 'object') {
                            base.alert(respon);
                            if (respon.redirect) {
                                setTimeout(function () {
                                    window.location.href = respon.redirect;
                                }, 2000);
                            }

                            if ($(parent).length) {
                                $(parent).removeClass('is-doing');
                            }
                            t.trigger('hh_select_action_completed', [respon]);

                            if (respon.reload) {
                                window.location.reload();
                            }
                        }
                    }, 'json');
                } else {
                    alert('Have a error when parse the data');
                }
            });
        },
        initCheckboxAction: function (el) {
            var base = this;
            $('body').on('change', '.hh-checkbox-action', function (ev) {
                var t = $(this),
                    parent = t.closest(t.data('parent'));
                ev.preventDefault();
                var data = JSON.parse(Base64.decode(t.attr('data-params')));
                if (typeof data == 'object') {
                    data['val'] = (t.is(':checked')) ? t.val() : '';
                    data['_token'] = $('meta[name="csrf-token"]').attr('content');
                    if ($(parent).length) {
                        $(parent).addClass('is-doing');
                    }
                    $.post(t.attr('data-action'), data, function (respon) {
                        if (typeof respon == 'object') {
                            base.alert(respon);
                            if (respon.redirect) {
                                setTimeout(function () {
                                    window.location.href = respon.redirect;
                                }, 2000);
                            }

                            if ($(parent).length) {
                                $(parent).removeClass('is-doing');
                            }
                            t.trigger('hh_checkbox_action_completed', [respon]);

                            if (respon.reload) {
                                window.location.reload();
                            }
                        }
                    }, 'json');
                } else {
                    alert('Have a error when parse the data');
                }
            });
        },
        initTable: function (el) {
            var base = this;
            setTimeout(function () {
                $('table[data-plugin="datatable"]', el).each(function () {
                    var t = $(this),
                        columns = t.data('cols') ? JSON.parse(Base64.decode(t.data('cols'))) : [];
                    t.dataTable({
                        dom: 'frtipB',
                        buttons: [
                            {
                                text: t.data('pdf-name'),
                                extend: 'pdfHtml5',
                                customize: function (doc) {
                                    $.each(doc.content[1].table.body[0], function (index, name) {
                                        doc.content[1].table.body[0][index].text = name;
                                    });
                                    $.each(doc.content[1].table.body[0], function (index, item) {
                                        if (typeof columns[index] != 'undefined') {
                                            doc.content[1].table.body[0][index].text = columns[index];
                                        } else {
                                            delete doc.content[1].table.body[0][index];

                                            doc.content[1].table.body[0].length = columns.length;
                                        }
                                    });
                                    $.each(doc.content[1].table.body, function (index, item) {
                                        if (index != 0) {
                                            $.each(item, function (_index, _item) {
                                                if (typeof columns[_index] == 'undefined') {
                                                    delete doc.content[1].table.body[index][_index];
                                                    doc.content[1].table.body[index].length = columns.length;
                                                }
                                            });
                                        }
                                    });
                                },
                                download: 'open'
                            }
                        ]
                    });
                });
            }, 500);
        },
        initFormAction: function (el) {
            let base = this;
            $(document).on('submit', 'form.form-action', function (ev) {
                ev.stopPropagation();
                ev.preventDefault();

                let form = $(this),
                    url = form.attr('action'),
                    loading = (typeof form.data('loading-from') == 'string') ? form.closest(form.data('loading-from')).find('.hh-loading') : $('.hh-loading', form),
                    reloadTime = form.data('reload-time'),
                    use_captcha = form.data('google-captcha');

                if (typeof reloadTime == 'undefined') {
                    reloadTime = 0;
                }
                base.initValidation(form, true);
                if (Object.size(hh_params.isValidated)) {
                    $("html, body").animate({scrollTop: $('.has-validation.is-invalid', form).first().offset().top}, 500);
                    $('.has-validation.is-invalid', form).first().focus();
                } else {
                    form.trigger('hh_form_action_before', [form]);

                    if (typeof tinyMCE != 'undefined') {
                        tinyMCE.triggerSave();
                    }
                    let data = form.serializeArray();
                    data.push({
                        name: '_token',
                        value: $('meta[name="csrf-token"]').attr('content'),
                    });

                    loading.show();
                    if ($('.form-message', form).length) {
                        $('.form-message', form).empty();
                    }

                    if (typeof use_captcha == 'string' && use_captcha == 'yes' && hh_params.use_google_captcha === 'on') {
                        grecaptcha.ready(function () {
                            grecaptcha.execute(hh_params.google_captcha_key, {action: 'form_action'}).then(function (token) {
                                data.push({
                                    name: 'g-recaptcha-response',
                                    value: token
                                });
                                $.post(url, data, function (respon) {
                                    if (typeof respon === 'object') {
                                        if ($('.form-message', form).length) {
                                            $('.form-message', form).html(respon.message);
                                        } else {
                                            base.alert(respon);
                                        }

                                        form.trigger('hh_form_action_complete', [respon]);

                                        if (form.hasClass('.has-reset')) {
                                            form.get(0).reset();
                                        }
                                        if (respon.redirect) {
                                            setTimeout(function () {
                                                window.location.href = respon.redirect;
                                            }, reloadTime);
                                        }

                                        if (respon.reload) {
                                            setTimeout(function () {
                                                window.location.reload();
                                            }, reloadTime);
                                        }
                                    }
                                    loading.hide();
                                }, 'json');
                            });
                        });
                    } else {
                        $.post(url, data, function (respon) {
                            if (typeof respon === 'object') {
                                if ($('.form-message', form).length) {
                                    $('.form-message', form).html(respon.message);
                                } else {
                                    base.alert(respon);
                                }

                                form.trigger('hh_form_action_complete', [respon]);

                                if (form.hasClass('.has-reset')) {
                                    form.get(0).reset();
                                }
                                if (respon.redirect) {
                                    setTimeout(function () {
                                        window.location.href = respon.redirect;
                                    }, reloadTime);
                                }

                                if (respon.reload) {
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, reloadTime);
                                }
                            }
                            loading.hide();
                        }, 'json');
                    }
                }
            });
        },
        initLinkAction: function (el) {
            let base = this;
            $('.hh-link-action', el).each(function () {
                let t = $(this),
                    parent = t.closest(t.data('parent'));

                t.click(function (ev) {
                    ev.preventDefault();

                    let dataConfirm = t.data('confirm');
                    if (dataConfirm === 'yes') {
                        $.confirm({
                            animation: 'none',
                            title: t.data('confirm-title'),
                            content: t.data('confirm-question'),
                            buttons: {
                                ok: {
                                    text: t.data('confirm-button'),
                                    btnClass: 'btn-primary',
                                    action: function () {
                                        let data = JSON.parse(Base64.decode(t.attr('data-params')));
                                        if (typeof data == 'object') {
                                            data['_token'] = $('meta[name="csrf-token"]').attr('content');
                                            if ($(parent).length) {
                                                $(parent).addClass('is-doing');
                                            }
                                            $.post(t.attr('data-action'), data, function (respon) {
                                                if (typeof respon == 'object') {
                                                    base.alert(respon);
                                                    t.trigger('hh_link_action_completed', [respon]);

                                                    if (respon.redirect) {
                                                        setTimeout(function () {
                                                            window.location.href = respon.redirect;
                                                        }, 1500);
                                                    }

                                                    if ($(parent).length) {
                                                        $(parent).removeClass('is-doing');
                                                        if (t.attr('data-is-delete')) {
                                                            $(parent).addClass('is-deleted');
                                                            $(parent).one(whichTransitionEvent(), function () {
                                                                $(parent).hide();
                                                            });
                                                        }
                                                    }
                                                    if (respon.reload) {
                                                        window.location.reload();
                                                    }
                                                }
                                            }, 'json');

                                        } else {
                                            alert('Have a error when parse the data');
                                        }
                                    }
                                },
                                cancel: function () {

                                }
                            }
                        });
                    } else {
                        let data = JSON.parse(Base64.decode(t.attr('data-params')));
                        if (typeof data == 'object') {
                            data['_token'] = $('meta[name="csrf-token"]').attr('content');
                            if ($(parent).length) {
                                $(parent).addClass('is-doing');
                            }
                            $.post(t.attr('data-action'), data, function (respon) {
                                if (typeof respon == 'object') {
                                    base.alert(respon);
                                    t.trigger('hh_link_action_completed', [respon]);

                                    if (respon.redirect) {
                                        setTimeout(function () {
                                            window.location.href = respon.redirect;
                                        }, 1500);
                                    }

                                    if ($(parent).length) {
                                        $(parent).removeClass('is-doing');
                                        if (t.attr('data-is-delete')) {
                                            $(parent).addClass('is-deleted');
                                            $(parent).one(whichTransitionEvent(), function () {
                                                $(parent).hide();
                                            });
                                        }
                                    }
                                    if (respon.reload) {
                                        window.location.reload();
                                    }
                                }
                            }, 'json');

                        } else {
                            alert('Have a error when parse the data');
                        }
                    }
                });
            });
        },

        initValidation: function (el, addEvent) {
            $('.has-validation', el).each(function () {
                let _id = $(this).attr('id'),
                    validation = $(this).attr('data-validation');
                bootstrapValidate('#' + _id, validation, function (isValid) {
                    if (isValid) {
                        if (typeof hh_params.isValidated[_id] !== 'undefined') {
                            delete hh_params.isValidated[_id];
                        }
                    } else {
                        hh_params.isValidated[_id] = 1;
                    }
                });
                if (addEvent) {
                    if ($(this).val() === '') {
                        $(this).trigger('focus').trigger('blur');
                    }
                }
            });
        },
        alert: function (respon) {
            if (typeof respon.message != "undefined") {
                if (respon.status === 0) {
                    $.toast({
                        heading: respon.title,
                        text: respon.message,
                        icon: 'error',
                        loaderBg: '#bf441d',
                        position: 'bottom-right',
                        allowToastClose: false,
                        hideAfter: 2000
                    });
                } else {
                    if (respon.status === 1) {
                        $.toast({
                            heading: respon.title,
                            text: respon.message,
                            icon: 'success',
                            loaderBg: '#5ba035',
                            position: 'bottom-right',
                            allowToastClose: false,
                            hideAfter: 2000
                        });
                    } else {
                        $.toast({
                            heading: respon.title,
                            text: respon.message,
                            icon: 'info',
                            loaderBg: '#26afa4',
                            position: 'bottom-right',
                            allowToastClose: false,
                            hideAfter: 2000
                        });
                    }
                }
            }
        },
        initGlobal: function (el) {
            let base = this;
            $('.date-time .date-render', 'form').click(function (ev) {
                let t = $(this);
                $('.dropdown-time', t).stop().toggle();
            });
            $('.date-time .date-render', '.filter-mobile-box').click(function (ev) {
                let t = $(this);
                $('.dropdown-time', t).stop().toggle();
            });

            $('.dropdown-time', 'form').on('click', '.item', function (ev) {
                timeClick($(this));
            });
            $('.dropdown-time', '.filter-mobile-box').on('click', '.item', function (ev) {
                timeClick($(this));
            });

            function timeClick($el){
                let t = $el,
                    container = t.closest('.date-render'),
                    parent = t.closest('.dropdown-time'),
                    val = t.attr('data-value'),
                    title = t.text();

                $('.item', parent).removeClass('active');
                t.addClass('active');

                $('input', container).attr('value', val).trigger('change');
                $('.render', container).text(title);

                if (t.closest('.date-render.check-in-render').length) {
                    let pos = t.index();

                    let next_time = t.closest('.date-time').find('.date-render.check-out-render').find('.dropdown-time');
                    $('.item', next_time).removeClass('disable');
                    $('.item', next_time).eq(pos).next().click();
                    $('.item', next_time).eq(pos).addClass('disable').prevAll().addClass('disable');
                }
                t.trigger('hh_dropdown_time_item_click', [t]);
            }

            $('body').click(function (ev) {
                if ($(ev.target).closest('.date-time').length === 0) {
                    $('.dropdown-time').hide();
                }
            });

            $('input[name="bookingType"]', '.hh-search-form').on('change',function (ev) {
                let t = $(this),
                    val = $('input[name="bookingType"]:checked', '.hh-search-form').val(),
                    parent = t.closest('.hh-search-form');
                if (val === 'per_hour') {
                    $('.form-group-date-single', parent).removeClass('d-none');
                    $('.form-group-date-time', parent).removeClass('d-none');
                    $('.form-group-date', parent).addClass('d-none');
                } else {
                    $('.form-group-date-single', parent).addClass('d-none');
                    $('.form-group-date-time', parent).addClass('d-none');
                    $('.form-group-date', parent).removeClass('d-none');
                }
            }).change();

            $.fn.flatpickr && $('.date-time .flatpickr').each(function(){
                let t = $(this),
                    wrapper = t.closest('.button-time');
                let fl = t.flatpickr({
                    minuteIncrement: 30,
                    static: true,
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "h:i K",
                    clickOpens: true,
                    onChange: function(selectedDates, dateStr, instance) {
                        selectedDates = new Date(selectedDates);
                        if(t.closest('.check-in-render').length){
                            $('.text.start', wrapper).text(moment(selectedDates).format('hh:mm A'));
                        }
                        if(t.closest('.check-out-render').length){
                            $('.text.end', wrapper).text(moment(selectedDates).format('hh:mm A'));
                        }
                    }
                });
                fl.open();
            });
        }
    };
    HHActions.init(body);

    let HHFormHomeSingle = {
        init: function () {
            let base = this;
            let formHome = $('#form-book-home', body);
            base.responsiveForm(formHome);
            base.addEvent(formHome);
        },
        responsiveForm: function (form) {
            /* Sticky sidebar */
            if (typeof ScrollMagic === 'function') {
                let postDetails = document.querySelector(".single-home .col-content");
                let postSidebar = document.querySelector("#form-book-home");

                let controller = new ScrollMagic.Controller();

                let scene = new ScrollMagic.Scene({
                    triggerElement: postSidebar,
                    triggerHook: 0,
                    duration: postDetails.offsetHeight - (postSidebar.offsetHeight +38)
                }).addTo(controller);

                if (window.matchMedia("(min-width: 992px)").matches) {
                    scene.setPin(postSidebar, {pushFollowers: false});
                }

                window.addEventListener("resize", () => {
                    if (window.matchMedia("(min-width: 992px)").matches) {
                        scene.setPin(postSidebar, {pushFollowers: false});
                    } else {
                        scene.removePin(postSidebar, true);
                    }
                });
            }
        },
        addEvent: function (form) {
            let base = this;

            $('.check-in-out-field', form).on('daterangepicker_change', function (e, start, end, label) {
                if (label === 'clicked_date') {
                    base.getRealPrice(form);
                }
            });
            $('.dropdown-time', form).on('hh_dropdown_time_item_click', function (ev, el) {
                if (el.closest('.check-out-render').length) {
                    base.getRealPrice(form);
                }
            });
            $('.input-extra', form).on('change', function () {
                base.getRealPrice(form);
            });

            $('.check-in-out-single-field', form).on('daterangepicker_change', function (ev, start, end, label) {
                let t = $(this),
                    parent = t.closest('.date-single'),
                    form = t.closest('form'),
                    container = t.closest('.form-book'),
                    loading = $('.booking-loading', container);
                let data = {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'home_id': t.data('home-id'),
                    'start': start.format('YYYY-MM-DD')
                };
                loading.show();
                $('.form-message', form).empty();
                $('.form-group-date-time', form).addClass('d-none');
                $.post(parent.data('action-time'), data, function (respon) {
                    if (typeof respon == 'object') {
                        if (respon.status === 1) {
                            $('.date-time .dropdown-time', form).html(respon.html);
                            $('.form-group-date-time', form).removeClass('d-none');
                        } else {
                            $('.form-message', form).html(respon.message);
                        }
                    }
                    loading.hide();
                }, 'json');
            });
        },
        getRealPrice: function (form) {
            let data = $('form', form).serializeArray();
            data.push({
                name: '_token',
                value: $('meta[name="csrf-token"]').attr('content')
            });
            let loading = $('.hh-loading', form);
            loading.show();
            $('.form-render', form).empty();

            $.post(form.attr('data-real-price'), data, function (respon) {
                if (typeof respon === 'object') {
                    $('.form-render', form).html(respon.html);
                    if (respon.status === 0) {
                        alert(respon.message);
                    }

                }
                loading.hide();
            }, 'json');
        }
    };
    HHFormHomeSingle.init();

    let HHCheckoutForm = {
        init: function () {
            let container = $('.hh-checkout-page', body);
            this.initEvents(container);
        },
        initEvents: function (container) {
            $('.btn-next-payment', container).click(function (ev) {
                ev.preventDefault();
                let t = $(this),
                    form = t.closest('form');
                $('.has-validation', form).each(function () {
                    let _id = $(this).attr('id'),
                        validation = $(this).attr('data-validation');
                    bootstrapValidate('#' + _id, validation, function (isValid) {
                        if (isValid) {
                            if (typeof hh_params.isValidated[_id] !== 'undefined') {
                                delete hh_params.isValidated[_id];
                            }
                        } else {
                            hh_params.isValidated[_id] = 1;
                        }
                    });
                    if ($(this).val() === '') {
                        $(this).trigger('focus').trigger('blur');
                    }
                });
                if (Object.size(hh_params.isValidated)) {
                    $("html, body").animate({scrollTop: $('.has-validation.is-invalid', form).first().offset().top}, 500);
                    return false;
                } else {
                    $('.nav-tabs .nav-item .nav-link[href="#co-payment-selection"]', container).tab('show');
                }
            });

            $('.checkout-form-payment', container).submit(function (ev) {
                ev.preventDefault();
                let form = $(this),
                    loading = $('.hh-loading', form),
                    message = $('.form-message', form);

                let data = [];
                $('.checkout-form', container).each(function () {
                    data = data.concat($(this).serializeArray());
                });
                data.push({
                    name: '_token',
                    value: $('meta[name="csrf-token"]').attr('content')
                });
                loading.show();
                message.empty();
                $.post(form.attr('action'), data, function (respon) {
                    if (typeof respon === 'object') {
                        message.html(respon.message);
                        if (respon.need_login) {
                            $('a[data-target="#hh-login-modal"]', 'body').trigger('click');
                        }
                        if (respon.redirect) {
                            window.location.href = respon.redirect;
                        }
                    }
                    loading.hide();
                }, 'json');
            });

            $('.btn-prev-customer', container).click(function (ev) {
                ev.preventDefault();
                $('.nav-tabs .nav-item .nav-link[href="#co-customer-information"]', container).tab('show');
            });
            $('.payment-method', container).each(function () {
                let t = $(this);
                if (t.is(':checked')) {
                    t.closest('.payment-item').addClass('active');
                }
                t.change(function () {
                    let parent = t.closest('.payment-item');
                    $('.payment-method', container).closest('.payment-item').removeClass('active');
                    parent.removeClass('active');
                    if (t.is(':checked')) {
                        parent.addClass('active');
                    }
                });
            });

            $('#last-user-checkout', body).change(function (ev) {
                let t = $(this),
                    val = t.is(':checked') ? t.val() : '',
                    parent = t.closest('.use-last-user-checkout');

                if (val === 'on') {
                    let data = $('input[name="last_user_checkout"]').val();
                    data = JSON.parse(Base64.decode(data));
                    if (typeof data == 'object') {
                        $.each(data, function (index, value) {
                            $('input[name="' + index + '"]').val(value).trigger('change');
                        });
                        parent.next().hide();
                    }
                } else {
                    $('.checkout-form').each(function () {
                        $(this).get(0).reset();
                    });
                    parent.next().show();
                }
            });
        }
    };

    HHCheckoutForm.init();

    $(".view-gallery", body).click(function () {
        let t = $(this);
        let parent = t.closest('.hh-thumbnail');
        if ($(".data-gallery", parent).length) {
            let data = $(".data-gallery", parent).attr("data-gallery");
            if (typeof data === "string" && data !== "") {
                data = JSON.parse(Base64.decode(data));
                $(this).lightGallery({dynamic: true, dynamicEl: data})
            }
        }
    });

    $('.hh-search-bar-buttons', body).on('hh_mapbox_input_focus', '.mapboxgl-ctrl-geocoder--input', function () {
        $(this).css({'max-width': '100%'});
        this.selectionStart = this.selectionEnd = 10000;
        let w = $(this).closest('.ots-button-item').innerWidth();
        $(this).parent().find('.suggestions').width(w);
    });

    $('.hh-search-bar-buttons', body).each(function () {
        let t = $(this);

        t.on('hh_mapbox_input_load', '.mapboxgl-ctrl-geocoder--input', function () {
            $(this).attr('data-max-width', $(this).css('max-width'));
        });
        t.on('hh_mapbox_input_blur', '.mapboxgl-ctrl-geocoder--input', function (ev) {
            $(this).css({'max-width': $(this).attr('data-max-width')});
            this.selectionStart = this.selectionEnd = 0;
        });
    });

    $('.reply-box-wrapper .btn-reply').on('click', function (e) {
        e.preventDefault();
        var t = $(this),
            wrapper = t.closest('li'),
            parent = t.closest('.reply-box-wrapper'),
            appendEl = parent.find('.reply-form'),
            commentForm = $('.post-comment.parent-form');

        $('.post-comment.append-form').remove();
        $('.reply-box-wrapper').find('.reply-form').html('');
        $('.reply-box-wrapper').removeClass('active');

        parent.addClass('active');
        commentForm.find('input[name="comment_id"]').val(parent.data('comment_id'));
        appendEl.html(commentForm.clone().removeClass('parent-form').addClass('append-form').show());
        commentForm.hide();
    });

    $('.reply-box-wrapper .btn-cancel-reply').on('click', function (e) {
        e.preventDefault();
        var t = $(this),
            wrapper = t.closest('li'),
            parent = t.closest('.reply-box-wrapper'),
            appendEl = parent.find('.reply-form'),
            commentForm = $('.post-comment.parent-form');

        parent.removeClass('active');
        commentForm.find('input[name="comment_id"]').val('');
        appendEl.html('');
        commentForm.show();
    });

    $('.review-select-rate .fas-star .fa').each(function () {
        var list = $(this).parent(),
            listItems = list.children(),
            itemIndex = $(this).index(),
            parentItem = list.parent();
        $(this).on({
            mouseenter: function () {
                for (var i = 0; i < listItems.length; i++) {
                    if (i <= itemIndex) {
                        $(listItems[i]).addClass('hovered');
                    } else {
                        break;
                    }
                }
                $(this).on('click', function () {
                    for (var i = 0; i < listItems.length; i++) {
                        if (i <= itemIndex) {
                            $(listItems[i]).addClass('selected');
                        } else {
                            $(listItems[i]).removeClass('selected');
                        }
                    }
                    parentItem.children('.review_star').val(itemIndex + 1);
                });
            },
            mouseleave: function () {
                listItems.removeClass('hovered');
            }
        });

    });

    $('.hh-navigation li.current-menu-item').each(function () {
        var t = $(this);
        t.closest('.menu-item-has-children').addClass('current-menu-item');
        t.closest('.menu-item-has-children').attr('is-active', '1');
    });

    function recursiveCheckMenuCurrent(element) {
        if (!element.length)
            return;

        var the_ul = element.parent();
        if (the_ul.hasClass('hh-parent')) {
            element.addClass('current-menu-item');
        } else {
            recursiveCheckMenuCurrent(the_ul.parent());
        }
    }

    $('.current-menu-item').each(function () {
        recursiveCheckMenuCurrent($(this));
    });

    $('#mobile-check-availability').on('click', function () {
        $('#form-book-home').fadeIn();
    });

    $('#form-book-home .popup-booking-form-close').on('click', function () {
        $('#form-book-home').fadeOut();
    });

    $('.mobile-book-action').each(function () {
        var t = $(this);
        $(window).scroll(function () {
            if ($(window).scrollTop() >= 50 && window.matchMedia('(max-width: 991px)').matches) {
                t.css('display', 'flex');
            } else {
                t.css('display', 'none');
            }
        });
    });

    $('#hh-fogot-password-modal', body).on('show.bs.modal', function (ev) {
        $('#hh-register-modal', body).modal('hide');
        $('#hh-login-modal', body).modal('hide');
    });
})(jQuery);
