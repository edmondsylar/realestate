Object.size = function (obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

function slugify(string) {
    const a = 'àáäâãåăæąçćčđďèéěėëêęğǵḧìíïîįłḿǹńňñòóöôœøṕŕřßşśšșťțùúüûǘůűūųẃẍÿýźžż·/_,:;';
    const b = 'aaaaaaaaacccddeeeeeeegghiiiiilmnnnnooooooprrsssssttuuuuuuuuuwxyyzzz------';
    const p = new RegExp(a.split('').join('|'), 'g');

    return string.toString().toLowerCase()
        .replace(/\s+/g, '_') // Replace spaces with -
        .replace(p, c => b.charAt(a.indexOf(c))) // Replace special characters
        .replace(/&/g, '_and_') // Replace & with 'and'
        .replace(/[^\w\-]+/g, '') // Remove all non-word characters
        .replace(/\-\-+/g, '_') // Replace multiple - with single -
        .replace(/^-+/, '') // Trim - from start of text
        .replace(/-+$/, '') // Trim - from end of text
}

function esc_html(unsafe) {
    if (typeof unsafe == 'string') {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    } else {
        return unsafe;
    }
}

(function ($) {
    'use strict';

    var HHOptions = {
        init: function () {
            let base = this;
            var body = $('body');

            this.initFontIcon(body);
            this.initPasswordGenerator(body);
            this.initSelect(body);
            this.initEditor(body);
            this.initCustomPrice(body);
            this.initCalendar(body);
            this.initOnOff(body);
            this.initUnique(body);
            this.initUpload(body);
            this.initUploads(body);
            this.initGoogleMap(body);
            this.initMapbox(body);
            this.initGallery(body);
            this.initDatePicker(body);
            this.initRange(body);
            this.initColorPicker(body);
            this.initGoogleFonts(body);
            this.initAvailability(body);
            this.initListItem(body);
            this.initConditions(body);
            this.initMaxLength(body);
            this.initValidation(body);
            this.submitTab(body);
            this.doSubmit(body);
            this.gotoPrevNext(body);
            this.initFlagIcon(body);

            body.on('hh_modal_render_content', function () {
                base.initSelect(body);
                base.initFontIcon(body);
                base.initDatePicker(body);
                base.initPasswordGenerator(body);
            });
        },
        initFlagIcon: function (el) {
            let base = this;
            $('[data-plugin="flagicon"]', el).each(function () {
                var t = $(this),
                    parent = t.parent(),
                    flags = t.data('flags'),
                    assetURL = t.data('flag-url') + '/',
                    inputFlag = $('.flag-code', parent);

                parent.css('position', 'relative');
                if ($('.hh-icon-wrapper', parent).length) {
                    $('.hh-icon-wrapper', parent).remove();
                }

                var flags_items = '';
                flags.map(function (item) {
                    flags_items += '<span data-code="' + item.alpha2 + '" data-name="' + item.name + '" class="item-flag" style="margin: 0px 5px;">' +
                        '<img src="' + assetURL + item.alpha2 + '.png"/>' +
                        '</span>';
                });

                parent.append('<div class="hh-icon-wrapper">' +
                    '<input type="text" name="search" autocomplete="off">' +
                    '<div class="result">' +
                    '<div class="render" style="margin-left: -7px; margin-right: -7px;">' + flags_items + '</div>' +
                    '<div class="hh-loading">\n' +
                    '    <div class="lds-ellipsis">\n' +
                    '        <div></div>\n' +
                    '        <div></div>\n' +
                    '        <div></div>\n' +
                    '        <div></div>\n' +
                    '    </div>\n' +
                    '</div>' +
                    '</div>' +
                    '</div>');

                t.focus(function () {
                    $('.hh-icon-wrapper', parent).show();
                    $('.hh-icon-wrapper .render', parent).show();
                    if (t.val() === '') {
                        $('.hh-icon-wrapper input', parent).focus();
                    }
                });
                $('body').click(function (ev) {
                    if ($(ev.target).closest('.hh-icon-wrapper').length === 0 && !$(ev.target).is('.hh-icon-input')) {
                        $('.hh-icon-wrapper .render', parent).hide();
                        $('.hh-icon-wrapper', parent).hide();
                    }
                });
                $('.hh-icon-wrapper input', parent).on('keyup', function () {
                    var text = $(this).val();

                    var temp = flags.filter(function (item) {
                        return item.name.toLowerCase().indexOf(text.toLowerCase()) >= 0;
                    });

                    if (temp.length) {
                        $('.render .item-flag', parent).hide();
                        $('.render #no-flags', parent).remove();
                        temp.map(function (item) {
                            $('.render .item-flag[data-code="' + item.alpha2 + '"]', parent).show();
                        });
                    } else {
                        $('.render .item-flag', parent).hide();
                        if (!$('.render', parent).find('#no-flags').length) {
                            $('.render', parent).append('<span id="no-flags" style="margin: 20px 10px 10px 10px;">' + t.data('no-flags') + '</span>');
                        }
                    }
                });

                $('.render .item-flag', parent).click(function () {
                    t.val($(this).attr('data-name')).trigger('change').focus();
                    $('.flag-display', parent).html('<span><img src="' + assetURL + $(this).attr('data-code') + '.png" /></span>');
                    $('.flag-display', parent).find('span').css({'display': 'block'});
                    $('.hh-icon-wrapper', parent).hide();
                    inputFlag.val($(this).attr('data-code'));
                    t.blur();
                });
            });
        },

        initAvailability: function (el) {
            $('.field-availability', el).each(function () {
                var t = $(this);
                var container = $('.hh-availability', t);
                var calendar = $('.calendar_input', container);
                var options = {
                    parentEl: container,
                    showCalendar: true,
                    alwaysShowCalendars: true,
                    singleDatePicker: true,
                    sameDate: true,
                    autoApply: true,
                    disabledPast: false,
                    dateFormat: 'DD/MM/YYYY',
                    enableLoading: true,
                    showEventTooltip: false,
                    classNotAvailable: ['disabled', 'off'],
                    disableHightLight: true,
                    breakEvent: false,
                    fetchEvents: function (start, end, el, callback) {
                        var events = [];
                        if (el.flag_get_events) {
                            return false;
                        }
                        el.flag_get_events = true;
                        el.container.find('.hh-loading').show();
                        var data = {
                            start: start.format('YYYY-MM-DD'),
                            end: end.format('YYYY-MM-DD'),
                            post_id: calendar.data('id'),
                            _token: $('meta[name="csrf-token"]').attr('content')
                        };
                        $.post(calendar.data('action'), data, function (respon) {
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
                if (typeof locale_daterangepicker == 'object') {
                    options.locale = locale_daterangepicker;
                }
                calendar.daterangepicker(options, function (start, end, label, elmdate, el) {

                });
                var dp = calendar.data('daterangepicker');
                dp.show();

                container.on('click', '.booking-item-time .item', function (ev) {
                    let t = $(this),
                        _modal = t.attr('data-target');
                });
                $('#hh-show-availability-time-slot-modal', el).on('show.bs.modal', function (ev) {
                    let element = $(ev.relatedTarget),
                        modal = $(this);
                    $('.modal-title', modal).text(element.attr('data-title'));

                });
            });
        },
        generatePassword: function () {
            var length = 32,
                charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@!#^*()",
                retVal = "";
            for (var i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            return retVal;
        },
        initPasswordGenerator: function (el) {
            let base = this;
            $('[data-plugin="password"]', el).each(function () {
                let t = $(this),
                    parent = t.closest('.input-group'),
                    button = $('button', parent);

                button.click(function () {
                    let password = base.generatePassword();
                    t.val(password).focus().blur();
                });
            });
        },
        initFontIcon: function (el) {
            let base = this;
            $('[data-plugin="fonticon"]', el).each(function () {
                var t = $(this),
                    parent = t.parent();
                parent.css('position', 'relative');
                if ($('.hh-icon-wrapper', parent).length) {
                    $('.hh-icon-wrapper', parent).remove();
                }
                parent.append('<div class="hh-icon-wrapper">' +
                    '<input type="text" name="search" autocomplete="off">' +
                    '<div class="result">' +
                    '<div class="render"></div>' +
                    '<div class="hh-loading">\n' +
                    '    <div class="lds-ellipsis">\n' +
                    '        <div></div>\n' +
                    '        <div></div>\n' +
                    '        <div></div>\n' +
                    '        <div></div>\n' +
                    '    </div>\n' +
                    '</div>' +
                    '</div>' +
                    '</div>');

                t.focus(function () {
                    $('.hh-icon-wrapper', parent).show();
                    if (t.val() == '') {
                        $('.hh-icon-wrapper input', parent).focus();
                    }
                });
                $('body').click(function (ev) {
                    if ($(ev.target).closest('.hh-icon-wrapper').length == 0 && !$(ev.target).is('.hh-icon-input')) {
                        $('.hh-icon-wrapper .render', parent).hide();
                        $('.hh-icon-wrapper', parent).hide();
                    }
                });
                var timeout, ajax;
                $('.hh-icon-wrapper input', parent).on('keyup', function () {
                    var text = $(this).val();
                    if (text.length < 2) {
                        return false;
                    }
                    clearTimeout(timeout);
                    if (ajax) {
                        ajax.abort();
                    }
                    var data = {
                        text: text,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    };
                    timeout = setTimeout(function () {
                        $('.hh-icon-wrapper .hh-loading', parent).show();
                        ajax = $.post(t.attr('data-action'), data, function (respon) {
                            $('.hh-icon-wrapper .hh-loading', parent).hide();
                            if (typeof respon == 'object') {
                                if (respon.status == 0) {
                                    $('.hh-icon-wrapper .render', parent).html(respon.data).show();
                                } else {
                                    var html = '';
                                    $.each(respon.data, function (index, value) {
                                        html += '<div class="item" data-ico="' + index + '">' + value + '</div>';
                                    });
                                    $('.hh-icon-wrapper .render', parent).html(html).show();
                                    $('.hh-icon-wrapper .render .item', parent).click(function () {
                                        t.val($(this).attr('data-ico')).trigger('change').focus();
                                    });
                                }
                            }
                        }, 'json');
                    }, 500);
                });
            });
        },
        initMapbox: function (el) {
            let base = this;

            if (typeof mapboxgl == 'object') {
                mapboxgl.accessToken = 'pk.eyJ1IjoiaGFpbmd1eWVuaGQiLCJhIjoiY2sxcHMxdDdwMTA5cTNqcHNwOWVxaWNsaCJ9.RAjP-xtBYwgmfj0Bb8A8_w';

                $('[data-plugin="mapbox-geocoder"]', el).each(function () {
                    let t = $(this),
                        container = t.closest('.field-location'),
                        mapbox = $('.mapbox-content', container),
                        zoom = parseInt($('.hh-zoom', container).val()),
                        lat = parseFloat($('.hh-lat', container).val()),
                        lng = parseFloat($('.hh-lng', container).val()),
                        markers = [];

                    let geocoder = new MapboxGeocoder({
                        accessToken: mapboxgl.accessToken,
                        mapboxgl: mapboxgl,
                        language: 'en-US',
                        placeholder: t.data().placeholder
                    });
                    let map = new mapboxgl.Map({
                            style: 'mapbox://styles/mapbox/light-v10',
                            container: mapbox.get(0),
                            center: [lng, lat],
                            zoom: zoom
                        },
                    );

                    var el = document.createElement('div');
                    el.className = 'hh-marker';

                    let marker = new mapboxgl.Marker(el, {
                        offset: {
                            x: 0,
                            y: -8
                        }
                    })
                        .setLngLat([lng, lat])
                        .addTo(map);

                    markers.push(marker);

                    if ($('.mapboxgl-ctrl-geocoder--input', t).length === 0) {
                        t.get(0).appendChild(geocoder.onAdd(map));
                    }

                    map.on('load', function () {
                        container.on('keyup', '.mapboxgl-ctrl-geocoder--input', function () {
                            $('.hh-address', container).attr('value', $(this).val()).trigger('change');
                        });
                        geocoder.on('result', function (ev) {
                            var result = ev.result;
                            if (typeof result.context == 'object') {
                                $.each(result.context, function (index, geo) {
                                    $('.hh-country', container).attr('value', '').trigger('change');
                                    $('.hh-city', container).attr('value', '').trigger('change');
                                    $('.hh-state', container).attr('value', '').trigger('change');
                                    $('.hh-postcode', container).attr('value', '').trigger('change');
                                    if (geo.id.indexOf('country') !== -1) {
                                        $('.hh-country', container).attr('value', geo.text).trigger('change');
                                    }
                                    if (geo.id.indexOf('region') !== -1) {
                                        $('.hh-city', container).attr('value', geo.text).trigger('change');
                                        $('.hh-state', container).attr('value', geo.text).trigger('change');
                                    }
                                    if (geo.id.indexOf('postcode') !== -1) {
                                        $('.hh-postcode', container).attr('value', geo.text).trigger('change');
                                    }
                                });
                            }
                            if (result.place_name) {
                                $('.hh-address', container).attr('value', result.place_name).trigger('change');
                            }
                            if (typeof result.geometry.coordinates == 'object') {
                                $('.hh-lat', container).attr('value', result.geometry.coordinates[1]).trigger('change');
                                $('.hh-lng', container).attr('value', result.geometry.coordinates[0]).trigger('change');
                                if (typeof markers == 'object') {
                                    $.each(markers, function (index, marker) {
                                        marker.remove();
                                    });
                                    markers = [];
                                }
                                let marker = new mapboxgl.Marker(el, {
                                    offset: {
                                        x: 0,
                                        y: -8
                                    }
                                })
                                    .setLngLat([result.geometry.coordinates[0], result.geometry.coordinates[1]])
                                    .addTo(map);
                                markers.push(marker);
                                map.flyTo({center: [result.geometry.coordinates[0], result.geometry.coordinates[1]]});
                            }
                        });

                        let oldVal = t.data().value;
                        if (typeof oldVal === 'string') {
                            geocoder.setInput(oldVal);
                        }
                    });

                    map.on('click', function (e) {
                        let location = e.lngLat;
                        if (typeof location == 'object') {
                            $('.hh-lat', container).attr('value', location.lat).trigger('change');
                            $('.hh-lng', container).attr('value', location.lng).trigger('change');

                            if (typeof markers == 'object') {
                                $.each(markers, function (index, marker) {
                                    marker.remove();
                                });
                                markers = [];
                            }
                            let marker = new mapboxgl.Marker(el, {
                                offset: {
                                    x: 0,
                                    y: -8
                                }
                            })
                                .setLngLat([location.lng, location.lat])
                                .addTo(map);
                            markers.push(marker);

                        }
                    });

                    map.on('moveend', function (ev) {
                        $('.hh-zoom', container).attr('value', ev.target.getZoom()).trigger('change');
                    });

                    $('body').on('hh_dashboard_submit_tab', function (ev) {
                        setTimeout(function () {
                            if (map) {
                                map.resize();
                            }
                        }, 500);
                    });
                });
            }

        },
        initUnique: function (el) {
            $('.field-unique', el).each(function () {
                var binded = $(this).attr('data-bind-from'),
                    unique = $(this).attr('data-unique'),
                    input = $('input', this);

                if($('#hh-language-action').length){
                    var langs = [];
                    $('#hh-language-action .item').each(function(){
                        if(!langs.includes($(this).data('code'))){
                            langs.push($(this).data('code'));
                        }
                    });
                    if(langs.length > 0){
                        unique += '_' + langs[0];
                    }
                }

                var $bined = $('#' + binded + unique);

                if ($bined.length) {
                    $('body').on('keyup', $bined, function () {
                        input.attr('value', slugify($bined.val())).trigger('change');
                    });
                    input.attr('value', slugify($bined.val())).trigger('change');
                }
            });
        },
        initSelect: function (el) {
            $.fn.niceSelect && $('[data-plugin="customselect"]', el).niceSelect();
            $.fn.select2 && $('[data-toggle="select2"]', el).select2();
        },
        initEditor: function (el) {
            if (typeof tinymce === 'object') {
                tinymce.init({
                    selector: 'textarea.hh-editor',
                    width: '100%',
                    height: 350,
                    nowrap: true
                });
            }
        },
        initCustomPrice: function (el) {
            let base = this;
            let parent = $('.field-price', el);

            let modal = $('#hh-bulk-edit-modal', el),
                loading = $('.hh-loading', modal);

            $('.add-price', modal).click(function (ev) {
                loading.show();
                base.initValidation(modal, true);
                if (Object.size(hh_params.isValidated)) {
                    $("html, body").animate({scrollTop: $('.has-validation.is-invalid', form).first().offset().top}, 500);
                    return false;
                } else {
                    ev.preventDefault();
                    let data = {
                        type_of_bulk: $('[name="type_of_bulk"]:checked', modal).val(),
                        days_of_week_bulk: $('#days_of_week_bulk', modal).val(),
                        days_of_month_bulk: $('#days_of_month_bulk', modal).val(),
                        month_bulk: $('#month_bulk', modal).val(),
                        year_bulk: $('#year_bulk', modal).val(),
                        price_bulk: $('#price_bulk', modal).val(),
                        available_bulk: ($('#available_bulk', modal).is(':checked')) ? 'on' : 'off',
                        post_id_bulk: $('#post_id_bulk', modal).val(),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    };
                    $.post(modal.data('action'), data, function (respon) {
                        if (typeof respon == 'object') {
                            base.alert(respon);
                            if (respon.status === 1) {
                                $('.price-render', parent).html(respon.html);
                                base.initOnOff($('.price-render', parent));
                            }
                        }
                        loading.hide();
                    }, 'json');
                }

            });
            parent.on('click', '.delete-price', function (ev) {
                ev.preventDefault();
                var t = $(this),
                    container = t.closest('.field-price');
                $.confirm({
                    animation: 'none',
                    title: t.attr('data-title'),
                    content: t.attr('data-content'),
                    buttons: {
                        ok: {
                            text: "Delete it!",
                            btnClass: 'btn-primary',
                            action: function () {
                                let data = {
                                    priceID: t.data('price-id'),
                                    postID: t.data('post-id'),
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                };
                                $('.hh-loading', parent).show();
                                $.post(container.attr('data-delete-url'), data, function (respon) {
                                    if (typeof respon == 'object') {
                                        base.alert(respon);
                                        if (respon.html) {
                                            $('.price-render', parent).html(respon.html);
                                            base.initOnOff($('.price-render', parent));
                                        }
                                    }
                                    $('.hh-loading', parent).hide();
                                }, 'json');
                            }
                        },
                        cancel: function () {

                        }
                    }
                });

            })
        },
        initUpload: function (el) {
            let base = this;
            el.on('click', '.hh-upload-wrapper .add-attachment', function () {
                var t = $(this);
                t.addClass('hh-media-waiting');
                base.mediaRender(el, false);
                $('#hh-media-frame-modal', 'body').on('hidden.bs.modal', function () {
                    t.removeClass('hh-media-waiting');
                });
            });
            el.on('click', '.hh-upload-wrapper .remove-attachment', function (ev) {
                ev.preventDefault();
                $(this).addClass('d-none');
                $(this).closest('.hh-upload-wrapper').find('.attachment-item').remove();
                $(this).closest('.hh-upload-wrapper').find('input').val('');
            });
            el.on('change', 'input.input-upload', function () {
                let t = $(this),
                    parent = t.closest('.hh-upload-wrapper'),
                    url = t.attr('data-url');
                let data = {
                    attachments: t.val(),
                    size: t.attr('data-size'),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.post(url, data, function (respon) {
                    if (typeof respon === 'object') {
                        if (respon.html !== '') {
                            $('.attachments', parent).html(respon.html);
                            t.trigger('input_upload_changed', [respon.url, t.val]);
                        }
                    }
                }, 'json');
            })
        },
        initUploads: function (el) {
            let base = this;
            el.on('click', '.hh-upload-wrapper .add-attachments', function () {
                var t = $(this);
                t.addClass('hh-media-waiting');
                base.mediaRender(el, true);
                $('#hh-media-frame-modal', 'body').on('hidden.bs.modal', function () {
                    t.removeClass('hh-media-waiting');
                });
            });
            el.on('click', '.hh-upload-wrapper .remove-attachment', function (ev) {
                ev.preventDefault();
                $(this).addClass('d-none');
                $(this).closest('.hh-upload-wrapper').find('.attachment-item').remove();
                $(this).closest('.hh-upload-wrapper').find('input').val('');
            });
            el.on('change', 'input.input-uploads', function () {
                let t = $(this),
                    parent = t.closest('.hh-upload-wrapper'),
                    url = t.attr('data-url');
                let data = {
                    attachments: t.val(),
                    size: t.attr('data-size'),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.post(url, data, function (respon) {
                    if (typeof respon === 'object') {
                        if (respon.html !== '') {
                            $('.attachments', parent).html(respon.html);
                        }
                    }
                }, 'json');
            });
        },
        initGallery: function (el) {
            var base = this;
            $('.field-media_advanced', el).each(function () {
                var t = $(this),
                    button = $('.btn-upload-media', t);

                button.click(function (ev) {
                    ev.preventDefault();

                    var t = $(this);
                    t.addClass('hh-media-waiting');
                    base.mediaRender(el, true);
                    $('#hh-media-frame-modal', 'body').on('hidden.bs.modal', function () {
                        t.removeClass('hh-media-waiting');
                    });
                });

                el.on('change', 'input.input-advance-uploads', function () {
                    let t = $(this),
                        parent = t.closest('.hh-upload-wrapper'),
                        url = t.attr('data-url');
                    let data = {
                        attachments: t.val(),
                        postID: t.attr('data-post-id'),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    };

                    $.post(url, data, function (respon) {
                        if (typeof respon === 'object') {
                            if (respon.html !== '') {
                                $('.hh-dashboard-upload-render', parent).html(respon.html);
                                if (respon.featured_image && $('#hh-dashboard-service-preview .img-featured').length) {
                                    $('#hh-dashboard-service-preview .img-featured').attr('src', respon.featured_image);
                                }
                            }
                        }
                    }, 'json');
                });

                t.on('click', '.hh-gallery-delete', function (ev) {
                    ev.preventDefault();
                    var t = $(this),
                        container = t.closest('.field-media_advanced'),
                        gallery = $('.input-advance-uploads', container).val(),
                        id = t.attr('data-id');
                    $.confirm({
                        animation: 'none',
                        title: 'Confirm Alert!',
                        content: 'Are you sure to delete this image?',
                        buttons: {
                            ok: {
                                text: "delete",
                                btnClass: 'btn-primary',
                                action: function () {
                                    let new_gallery = [];
                                    if (gallery !== '') {
                                        gallery = gallery.split(',');
                                        for (let i = 0; i < gallery.length; i++) {
                                            if (gallery[i] != id) {
                                                new_gallery.push(gallery[i]);
                                            }
                                        }
                                    }
                                    new_gallery = new_gallery.join(',');
                                    $('.upload_value', container).attr('value', new_gallery).trigger('change');
                                    t.closest('.item').remove();
                                }
                            },
                            cancel: {}
                        }
                    });
                });
            });
            el.on('click', '.hh-gallery-add-featured', function (ev) {
                ev.preventDefault();

                var t = $(this),
                    parent = t.closest('.gallery-item'),
                    container = t.closest('.hh-upload-wrapper');

                var data = {
                    postID: t.attr('data-post-id'),
                    id: t.attr('data-id'),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };
                $('.hh-loading', parent).show();

                $.post(container.attr('data-action-set-featured'), data, function (respon) {
                    if (typeof respon == 'object') {
                        if (respon.status === 1) {
                            t.closest('.hh-dashboard-upload-render').find('.gallery-item .hh-gallery-add-featured').removeClass('is-featured');
                            t.addClass('is-featured');

                            $('#hh-dashboard-service-preview').find('.thumbnail .img-featured').attr('src', respon.img);
                        }
                        base.alert(respon);
                    }
                    $('.hh-loading', parent).hide();
                }, 'json');
            });
        },
        initUploadMedia: function (el) {
            let base = this;
            $('.hh-dropzone', el).each(function () {
                let form = $(this),
                    container = form.parent();

                let previewNode = $('#hh-dropzone-template', container);

                previewNode.remove();

                var hhDropzone = new Dropzone(form.get(0), {
                    url: form.attr('action'), // Check that our form has an action attr and if not, set one here
                    maxFilesize: 20,
                    acceptedFiles: "image/*",
                    previewTemplate: '<div></div>',
                    previewsContainer: $('#previews', container).get(0),
                    clickable: true,
                    createImageThumbnails: false,
                    dictDefaultMessage: "Drop files here to upload.", // Default: Drop files here to upload
                    dictFallbackMessage: "Your browser does not support drag'n'drop file uploads.", // Default: Your browser does not support drag'n'drop file uploads.
                    dictFileTooBig: "File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.", // Default: File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.
                    dictInvalidFileType: "You can't upload files of this type.", // Default: You can't upload files of this type.
                    dictResponseError: "Server responded with {{statusCode}} code.", // Default: Server responded with {{statusCode}} code.
                    dictCancelUpload: "Cancel upload.", // Default: Cancel upload
                    dictUploadCanceled: "Upload canceled.", // Default: Upload canceled.
                    dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?", // Default: Are you sure you want to cancel this upload?
                    dictRemoveFile: "Remove file", // Default: Remove file
                    dictRemoveFileConfirmation: null, // Default: null
                    dictMaxFilesExceeded: "You can not upload any more files.", // Default: You can not upload any more files.
                    dictFileSizeUnits: {tb: "TB", gb: "GB", mb: "MB", kb: "KB", b: "b"},
                    sending: function (file, xhr, formData) {
                        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    },
                });

                Dropzone.autoDiscover = false;

                hhDropzone.on('dragenter', function () {
                    form.addClass("hover");
                });
                hhDropzone.on('dragleave', function () {
                    form.removeClass("hover");
                });
                hhDropzone.on('drop', function () {
                    form.removeClass("hover");
                });

                hhDropzone.on("success", function (file, response) {
                    response = JSON.parse(response);
                    if (typeof response === 'object') {
                        base.alert(response);
                    }
                });
                hhDropzone.on("queuecomplete", function () {
                    $('#hh-media-frame-modal', 'body').trigger('shown.bs.modal');
                    $('#hh-media-add-new', 'body').removeClass('show');
                });
                hhDropzone.on('error', function (file, response) {
                    if (typeof response.message === 'string') {
                        $(file.previewElement).find('.dz-error-message').text(response.message);
                        base.alert({
                            status: 0,
                            message: response.message
                        });
                    } else {
                        base.alert(response);
                    }
                });
            });
        },
        mediaRender: function (el, multi) {
            let base = this;
            let mediaModal = $('#hh-media-frame-modal', 'body');
            let _class = (multi) ? 'multi' : '';
            if (mediaModal.length) {
                mediaModal.remove();
            }
            $('body').append('<div id="hh-media-frame-modal" class="modal fade ' + _class + '" tabindex="-1" role="dialog" aria-hidden="true">\n' +
                '    <div class="modal-dialog modal-full">\n' +
                '        <div class="modal-content relative">\n' +
                '<div class="hh-loading">\n' +
                '    <div class="lds-ellipsis">\n' +
                '        <div></div>\n' +
                '        <div></div>\n' +
                '        <div></div>\n' +
                '        <div></div>\n' +
                '    </div>\n' +
                '</div>' +
                '                <div class="modal-header">\n' +
                '                    <h4 class="modal-title">Libraries<a class="btn btn-info btn-xs waves-effect waves-light ml-1" data-toggle="collapse" href="#hh-media-add-new" aria-expanded="true">new\n' +
                '                            </a></h4>\n' +
                '                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×\n' +
                '                    </button>\n' +
                '                </div>\n' +
                '                <div class="modal-body">' +
                '                   <div id="hh-media-add-new" class="hh-media-upload-area collapse mt-3">\n' +
                '                            <form action="' + hh_params.add_media_url + '" method="post" class="hh-dropzone"\n' +
                '                                  id="hh-upload-form" enctype="multipart/form-data">\n' +
                '                                <div class="fallback">\n' +
                '                                    <input name="file" type="file" multiple/>\n' +
                '                                </div>\n' +
                '                                <div class="dz-message text-center needsclick">\n' +
                '                                    <i class="h1 text-muted dripicons-cloud-upload"></i>\n' +
                '                                    <h3>Drop files here or click to upload.</h3>\n' +
                '                                    <p class="text-muted">\n' +
                '                                        <span>Only JPG, PNG, JPEG files types are supported.</span>\n' +
                '                                        <span>Maximum file size is 2MB.</span>\n' +
                '                                    </p>\n' +
                '                                </div>\n' +
                '                            </form>\n' +
                '                        </div>' +
                '                <div class="hh-all-media mt-3">\n' +
                '                   <div class="hh-all-media-render">\n' +
                '                       <ul class="render"></ul>\n' +
                '                       </div>\n' +
                '                   </div>\n' +
                '                <div class="modal-footer">\n' +
                '                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>\n' +
                '                      <input type="hidden" name="hh_media_tmp" value="">' +
                '                    <button type="button"\n' +
                '                            class="btn btn-info waves-effect waves-light add-attachment d-none ' + _class + '">Add</button>\n' +
                '                </div>\n' +
                '        </div>\n' +
                '    </div>\n' +
                '</div><!-- /.modal -->');

            mediaModal = $('#hh-media-frame-modal', 'body');
            $('input[name="hh_media_tmp"]', mediaModal).attr('value', '').trigger('change');
            mediaModal.modal('show');

            base.initUploadMedia('body');

            mediaModal.on('shown.bs.modal', function (ev) {
                let data = {
                    type: 'normal',
                    _token: $('meta[name="csrf-token"]').attr('content')
                };
                let loading = $('.hh-loading', mediaModal),
                    url = $('.hh-media-waiting').attr('data-url');

                loading.show();
                $.post(url, data, function (respon) {
                    if (typeof respon === 'object') {
                        if (respon.html != '') {
                            $('.modal-body .render', mediaModal).html(respon.html);
                        }
                        if (respon.status === 0) {
                            $('.modal-body .render', mediaModal).html('<h5 class="title text-muted mt-2">' + respon.message + '</h5>')
                        }
                    }
                    loading.hide();
                }, 'json');
            });
            mediaModal.on('click', '.hh-media-item .link', function (ev) {
                let t = $(this),
                    attachment_id = t.attr('data-attachment-id');

                if (!mediaModal.hasClass('multi')) {
                    if (t.closest('.hh-media-item').hasClass('selected')) {
                        t.closest('.hh-media-item').removeClass('selected').parent().siblings().find('.hh-media-item').removeClass('selected');
                    } else {
                        t.closest('.hh-media-item').addClass('selected').parent().siblings().find('.hh-media-item').removeClass('selected');
                    }
                } else {
                    if (t.closest('.hh-media-item').hasClass('selected')) {
                        t.closest('.hh-media-item').removeClass('selected');
                    } else {
                        t.closest('.hh-media-item').addClass('selected');
                    }
                }
                base.setMediaTmp(mediaModal, attachment_id, t.closest('.hh-media-item').hasClass('selected'), multi);

            });

            mediaModal.on('click', '.add-attachment', function (ev) {
                ev.preventDefault();
                let val = $('input[name="hh_media_tmp"]', mediaModal).val();
                let button = $('.hh-media-waiting', el);
                button.parent().find('.upload_value').attr('value', val).trigger('change');

                mediaModal.modal('hide');
            });
        },
        setMediaTmp: function (el, attachment_id, add, multi) {
            let input = $('input[name="hh_media_tmp"]', el);
            let value = input.val();
            if (multi) {
                if (value !== '') {
                    value = value.split(',');
                    if (add) {
                        if (!value.includes(attachment_id)) {
                            value.push(attachment_id);
                        }
                    } else {
                        if (value.includes(attachment_id)) {
                            value = value.filter(item => item !== attachment_id)
                        }
                    }
                } else {
                    if (add) {
                        value = [attachment_id];
                    }
                }
            } else {
                if (add) {
                    value = [attachment_id];
                } else {
                    value = [];
                }
            }
            value = value.join(',');
            input.attr('value', value).trigger('change');

            if (value !== '') {
                $('.add-attachment', el).removeClass('d-none');
            } else {
                $('.add-attachment', el).addClass('d-none');
            }
        },
        initGoogleMap: function (el) {
            var base = this;
            $('.google-map-content', el).each(function () {
                var googleEl = $(this),
                    googleMap = googleEl.get(0);
                var parent = googleEl.closest('.field-location');
                var options = {
                    center: {lat: googleEl.data('lat'), lng: googleEl.data('lng')},
                    zoom: googleEl.data('zoom'),
                    disableDefaultUI: true
                };
                var markers = [];
                var map = new google.maps.Map(googleMap, options);
                if (googleEl.data('lat') != '' && googleEl.data('lng')) {
                    var marker = new google.maps.Marker({
                        position: {lat: googleEl.data('lat'), lng: googleEl.data('lng')},
                        map: map
                    });
                }
                parent.on('hh_autocomplete_changed', function (el, places) {
                    var myLatLng = {lat: places.geometry.location.lat(), lng: places.geometry.location.lng()};
                    for (var i = 0; i < markers.length; i++) {
                        markers[i].setMap(null);
                    }
                    var marker = new google.maps.Marker({
                        position: myLatLng,
                        map: map
                    });
                    markers.push(marker);
                    map.setCenter(myLatLng);
                });

                map.addListener('zoom_changed', function () {
                    $('.hh-zoom', parent).attr('value', map.getZoom());
                });

                var searchEl = $('.has-google-search', parent),
                    search = searchEl.get(0);
                var fetch = {
                    'locality': $('.hh-city', parent),
                    'administrative_area_level_1': $('.hh-state', parent),
                    'postal_code': $('.hh-postcode', parent),
                    'country': $('.hh-country', parent),
                };
                var componentForm = {
                    locality: 'long_name',
                    administrative_area_level_1: 'short_name',
                    country: 'long_name',
                    postal_code: 'short_name'
                };

                var searchBox = new google.maps.places.Autocomplete(search, {types: ['geocode']});
                searchBox.setFields(['address_component', 'geometry']);

                searchBox.addListener('place_changed', function () {
                    var places = searchBox.getPlace();
                    if (places.length == 0) {
                        return;
                    }
                    for (var key in fetch) {
                        fetch[key].attr('value', '');
                    }
                    for (var i = 0; i < places.address_components.length; i++) {
                        var addressType = places.address_components[i].types[0];
                        if (componentForm[addressType]) {
                            var val = places.address_components[i][componentForm[addressType]];
                            fetch[addressType].attr('value', val).trigger('change');
                        }
                    }

                    $('.hh-lat', parent).attr('value', places.geometry.location.lat()).trigger('change');
                    $('.hh-lng', parent).attr('value', places.geometry.location.lng()).trigger('change');
                    searchEl.trigger('change');
                    parent.trigger('hh_autocomplete_changed', [places]);
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
        initDatePicker: function (el) {
            if ($('[data-plugin="datepicker"]', el).length) {
                $('[data-plugin="datepicker"]', el).each(function () {
                    let t = $(this);
                    t.flatpickr({
                        dateFormat: "Y-m-d",
                    });
                });
            }
            if ($('[data-plugin="daterangepicker"]', el).length) {
                $('[data-plugin="daterangepicker"]', el).flatpickr({
                    mode: 'range',
                    locale: {
                        rangeSeparator: ' :: '
                    }
                });
            }
            if ($('[data-plugin="datetimepicker"]', el).length) {
                $('[data-plugin="datetimepicker"]', el).flatpickr({
                    enableTime: !0,
                    dateFormat: "Y-m-d H:i",
                    minDate: moment().format('YYYY-MM-DD')
                });
            }
            if ($('[data-plugin="timepicker"]', el).length) {
                $('[data-plugin="timepicker"]', el).flatpickr({
                    enableTime: !0,
                    noCalendar: !0,
                    dateFormat: "H:i"
                });
            }
        },
        initRange: function (el) {
            $('input[type="range"]', el).each(function () {
                let t = $(this),
                    parent = t.closest('.field-range'),
                    input = $('input[type="number"]', parent);

                t.rangeslider({
                    polyfill: false
                }).on('input', function () {
                    input.val(this.value);
                });

                input.on('input', function () {
                    t.val(this.value).change();
                });
            });
        },
        initCalendar: function (el) {
            var base = this;
            $('.field-calendar', el).each(function () {
                var container = $(this);
                var calendar = $('.calendar_input', container);
                var options = {
                    parentEl: container,
                    showCalendar: true,
                    alwaysShowCalendars: true,
                    singleDatePicker: true,
                    sameDate: true,
                    autoApply: true,
                    disabledPast: true,
                    dateFormat: 'YYYY-MM-DD',
                    enableLoading: true,
                    showEventTooltip: false,
                    classNotAvailable: ['disabled', 'off'],
                    disableHightLight: true,
                    fetchEvents: function (start, end, el, callback) {
                        var events = [];
                        if (el.flag_get_events) {
                            return false;
                        }
                        el.flag_get_events = true;
                        el.container.find('.hh-loading').show();
                        var data = {
                            action: calendar.data('action'),
                            start: start.format('YYYY-MM-DD'),
                            end: end.format('YYYY-MM-DD'),
                            post_id: calendar.data('id'),
                            security: hh_params.security
                        };
                        $.post(hh_params.ajaxurl, data, function (respon) {
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
                if (typeof locale_daterangepicker == 'object') {
                    options.locale = locale_daterangepicker;
                }
                calendar.daterangepicker(options, function (start, end, label, elmdate, el) {
                    if (label == 'clicked_date') {
                        let data = {
                            post_id: calendar.data('id'),
                            start: start.format('YYYY-MM-DD'),
                            end: end.format('YYYY-MM-DD'),
                            action: 'hh_dashboard_set_available_calendar',
                            security: hh_params.security
                        };
                        el.container.find('.hh-loading').show();
                        $.post(hh_params.ajaxurl, data, function (respon) {
                            if (typeof respon == 'object') {
                                base.alert(respon);
                            }
                            el.updateCalendars();
                            el.container.find('.hh-loading').hide();
                        }, 'json');
                    }
                });
                var dp = calendar.data('daterangepicker');
                dp.show();
            });
        },
        initColorPicker: function (el) {
            if ($('[data-plugin="colorpicker"]', el).length) {
                $('[data-plugin="colorpicker"]', el).colorpicker({
                    format: 'auto'
                }).on('colorpickerChange colorpickerCreate', function (e) {
                    $(e.currentTarget).parents('.input-group').find('.color')
                        .css('background-color', e.color.toString());
                });
            }
        },
        initGoogleFonts: function (el) {
            var base = this;
            $('.hh-google-fonts', el).on('change', function () {
                var t = $(this);
                var parent = t.closest('.form-group');
                var variants = $('option:selected', t).data('variants');
                var subsets = $('option:selected', t).data('subsets');
                if (variants) {
                    $('.hh-font-variants', parent).empty();
                    variants = variants.split(',');
                    for (var i = 0; i < variants.length; i++) {
                        $('.hh-font-variants', parent).append('<div class="checkbox checkbox-success"><input id="font-variant-' + variants[i] + '" type="checkbox" name="font_variants[]" value="' + variants[i] + '"><label for="font-variant-' + variants[i] + '" class="custom-control-label sub-label">' + variants[i] + '</label></div>')
                    }
                }
                if (subsets) {
                    $('.hh-font-subsets', parent).empty();
                    subsets = subsets.split(',');
                    for (var i = 0; i < subsets.length; i++) {
                        $('.hh-font-subsets', parent).append('<div class="checkbox checkbox-success"><input id="font-variant-' + subsets[i] + '" type="checkbox" name="font_subsets[]" value="' + subsets[i] + '"><label for="font-variant-' + subsets[i] + '" class="custom-control-label sub-label">' + subsets[i] + '</label></div>')
                    }
                }
            });
        },
        initListItem: function (el) {
            var base = this;
            base.initListItemSort();
            base.initBindListItem(el);
            $('body').on('click', '.add-list-item', function (ev) {
                ev.preventDefault();
                var t = $(this),
                    parent = t.closest('.form-group');
                t.prop('disable', 'disabled');
                var data = {
                    items: parent.data('items'),
                    id: parent.data('id'),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.post(t.attr('data-url'), data, function (respon) {
                    if (typeof respon == 'object') {
                        var container = $('.hh-render', parent);
                        container.find('.hh-list-item .render').removeClass('toggleFlex');
                        var el = $(respon.html).appendTo(container).find('.render').addClass('toggleFlex');
                        base.initListItemSort('refresh', el);
                        base.initBindListItem();
                        base.initSelect(el);
                        base.initPasswordGenerator(el);
                        base.initOnOff(el);
                        base.initUnique(el);
                        base.initDatePicker(el);
                        base.initRange(el);
                        base.initFontIcon(el);
                        base.initColorPicker(el);
                        base.initGoogleFonts(el);
                        base.initConditions(el);
                        base.initMaxLength(el);
                        base.initValidation(el, false);
                    }
                    t.prop('disable', false);
                }, 'json');
            });
            $('body').on('click', '.hh-list-items li .edit', function (ev) {
                ev.preventDefault();
                var t = $(this);
                t.closest('.hh-list-item').find('.render').toggleClass('toggleFlex');
            });
            $('body').on('click', '.hh-list-items li .close', function (ev) {
                ev.preventDefault();
                if (confirm("Are you sure?") === true) {
                    var t = $(this);
                    t.closest('.hh-list-item').remove();
                    base.initListItemSort('refresh');
                }
            });
        },
        initBindListItem: function (el) {
            el = (el === '') ? $('body') : el;
            var container = $('.field-list_item', el);
            container.each(function () {
                var hasBinded = $(this).data('bind-from') || false;
                if (hasBinded) {
                    $('.hh-list-item', this).each(function () {
                        var parent = $(this);
                        var element = $('[id^="' + hasBinded + '"]', parent);
                        element.on('change keyup', function (ev) {
                            $('.hh-list-item-heading .htext', parent).text(esc_html($(this).val()));
                        }).keyup().change();
                        $('.hh-list-item-heading .htext', parent).text(esc_html(element.val()));
                    });
                }
            });

        },
        initListItemSort: function (refresh, el) {
            var base = this;
            if (refresh == 'refresh') {
                $(".hh-list-items", el).sortable('refresh');
            } else {
                $(".hh-list-items", el).sortable();
            }

        },
        conditionObjects: function () {
            return 'select, input[type="radio"]:checked, input[type="text"], input[type="hidden"], input[type="number"], input[type="checkbox"]';
        },
        initMaxLength: function (el) {
            $('.has-maxLength', el).each(function () {
                var t = $(this);
                t.maxlength({
                    alwaysShow: t.attr('data-always-show') || 0,
                    threshold: t.attr('data-threshold') || 10,
                    showMaxLength: t.attr('data-show-max-length') || true,
                    showCharsTyped: t.attr('data-show-chars-typed') || true,
                    placement: t.attr('data-placement') || 'bottom',
                    warningClass: "badge badge-success",
                    limitReachedClass: "badge badge-danger"
                });
            });
        },
        initValidation: function (el, addEvent) {
            var base = this;
            $('.has-validation', el).each(function () {
                let _id = $(this).attr('id'),
                    validation = $(this).attr('data-validation');
                bootstrapValidate('#' + _id, validation, function (isValid) {
                    if (isValid) {
                        if (typeof hh_params.isValidated[_id] != 'undefined') {
                            delete hh_params.isValidated[_id];
                        }
                    } else {
                        hh_params.isValidated[_id] = 1;
                    }
                });
                if (addEvent) {
                    if ($(this).val() == '') {
                        $(this).trigger('focus').trigger('blur');
                    }
                }
            });
        },
        initConditions: function (el) {
            var base = this;
            var delay = (function () {
                var timer = 0;
                return function (callback, ms) {
                    clearTimeout(timer);
                    timer = setTimeout(callback, ms);
                };
            })();

            $('.form-group[id^="setting-"]', el).off('change.conditionals, keyup.conditionals', base.conditionObjects()).on('change.conditionals, keyup.conditionals', base.conditionObjects(), function (e) {
                if (e.type === 'keyup') {
                    delay(function () {
                        base.parseCondition(el);
                    }, 200);
                } else {
                    base.parseCondition(el);
                }
            });
            base.parseCondition(el);
        },
        parseCondition: function (el) {
            var base = this;
            $('.form-group[id^="setting-"][data-condition]').each(function () {
                var passed;
                var conditions = base.matchConditions($(this).data('condition'));
                var operator = ($(this).data('operator') || 'and').toLowerCase();
                var unique = $(this).data('unique') || '';
                if (conditions.length > 0) {
                    $.each(conditions, function (index, condition) {
                        var target = $('#setting-' + condition.check + unique, el);
                        var targetEl = !!target.length && target.find(base.conditionObjects()).first();

                        if (!target.length || (!targetEl.length && condition.value.toString() != '')) {
                            return;
                        }
                        var v1 = targetEl.length ? targetEl.val().toString() : '';
                        if (targetEl[0].type && targetEl[0].type == 'checkbox') {
                            v1 = [];
                            target.find(base.conditionObjects()).each(function () {
                                if ($(this).is(':checked')) {
                                    v1.push($(this).val().toString());
                                }
                            });
                        }
                        var v2 = condition.value.toString();
                        var result;
                        switch (condition.rule) {
                            case 'less_than':
                                result = (parseInt(v1) < parseInt(v2));
                                break;
                            case 'less_than_or_equal_to':
                                result = (parseInt(v1) <= parseInt(v2));
                                break;
                            case 'greater_than':
                                result = (parseInt(v1) > parseInt(v2));
                                break;
                            case 'greater_than_or_equal_to':
                                result = (parseInt(v1) >= parseInt(v2));
                                break;
                            case 'contains':
                                result = (v1.indexOf(v2) !== -1 ? true : false);
                                break;
                            case 'is':
                                if (typeof v1 != 'object') {
                                    result = (v2 == v1);
                                } else {
                                    result = ($.inArray(v2, v1) != -1) ? true : false;
                                }
                                break;
                            case 'not':
                                if (typeof v1 != 'object') {
                                    result = (v2 != v1);
                                } else {
                                    result = ($.inArray(v2, v1) == -1) ? true : false;
                                }
                                break;
                        }

                        if ('undefined' == typeof passed) {
                            passed = result;
                        }
                        switch (operator) {
                            case 'or':
                                passed = (passed || result);
                                break;
                            case 'and':
                            default:
                                passed = (passed && result);
                                break;
                        }
                    });

                    if (passed) {
                        $(this).animate({opacity: 'show', height: 'show'}, 200);
                    } else {
                        $(this).animate({opacity: 'hide', height: 'hide'}, 200);
                    }
                }

            });
        },
        matchConditions: function (condition) {
            var match;
            var regex = /(.+?):(is|not|contains|less_than|less_than_or_equal_to|greater_than|greater_than_or_equal_to)\((.*?)\),?/g;
            var conditions = [];

            while (match = regex.exec(condition)) {
                conditions.push({
                    'check': match[1],
                    'rule': match[2],
                    'value': match[3] || ''
                });
            }

            return conditions;
        },
        alert: function (respon) {
            if (respon.status === 0) {
                $.toast({
                    heading: respon.title,
                    text: respon.message,
                    icon: 'error',
                    loaderBg: '#bf441d',
                    position: 'top-center',
                    allowToastClose: false,
                    hideAfter: 2000
                });
            } else {
                $.toast({
                    heading: respon.title,
                    text: respon.message,
                    icon: 'success',
                    loaderBg: '#5ba035',
                    position: 'top-center',
                    allowToastClose: false,
                    hideAfter: 2000
                });
            }
        },
        submitTab: function (el) {
            var base = this;
            $('.hh-options-tab a[data-toggle="pill"]', el).on('click', function (e) {
                let currentTab = $(this),
                    prevTab = currentTab.closest('.nav-pills').find('.nav-link.active');
                if ($(prevTab).length) {
                    let parentTab = $(prevTab).attr('href');
                    let form = $('.hh-options-form', parentTab);
                    base.initValidation(form, true);
                    if (Object.size(hh_params.isValidated)) {
                        $("html, body").animate({scrollTop: $('.has-validation.is-invalid', form).first().offset().top}, 500);
                        return false;
                    } else {
                        base.submit(form, 'tab');
                        $('body').trigger('hh_dashboard_submit_tab');
                    }
                }
            })
        },
        doSubmit: function (el) {
            var base = this;
            $('.hh-options-form', el).on('submit', function (ev) {
                ev.preventDefault();
                let form = $(this);
                base.initValidation(form, true);
                if (Object.size(hh_params.isValidated)) {
                    $("html, body").animate({scrollTop: $('.has-validation.is-invalid', form).first().offset().top}, 500);
                } else {
                    let currenTab = form.data().tab,
                        nextTab = $(currenTab).next('.nav-link');

                    if (nextTab.length) {
                        base.gotoTab(nextTab);
                    } else {
                        base.submit(form);
                    }

                }
            });
        },
        gotoTab: function (tab) {
            if (tab.length) {
                tab.click();
            }
        },
        gotoPrevNext: function (el) {
            var base = this;
            $('.btn-prev-tab-option', el).click(function (ev) {
                ev.preventDefault();
                let currenTab = $(this).data().tab,
                    prevTab = $(currenTab).prev('.nav-link');
                base.gotoTab(prevTab);
            });

            $('.btn-next-tab-option', el).click(function (ev) {
                ev.preventDefault();
                let currenTab = $(this).data().tab,
                    prevTab = $(currenTab).next('.nav-link');
                base.gotoTab(prevTab);
            });

            $('.btn-current-tab-option', el).click(function (ev) {
                ev.preventDefault();
                let currenTab = $(this).data().tab;
                base.gotoTab($(currenTab));
            });
        },
        submit: function (form, event) {
            var base = this;
            var container = form.closest('#hh-options-wrapper'),
                loading = $('.loading-options', container);

            if (typeof tinyMCE != 'undefined') {
                tinyMCE.triggerSave();
            }

            var data = form.serializeArray();

            data.push({
                name: '_token',
                value: $('meta[name="csrf-token"]').attr('content')
            }, {
                name: 'option_event',
                value: (typeof event != 'undefined') ? event : ''
            });

            loading.show();
            $.post(form.attr('action'), data, function (respon) {
                if (typeof respon == 'object') {
                    base.alert(respon);

                    if (respon.redirect) {
                        window.location.href = respon.redirect;
                    }
                }
                loading.hide();
            }, 'json');
        }
    };

    HHOptions.init();
})(jQuery);
