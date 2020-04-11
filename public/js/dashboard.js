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

    /*==== Tabs calculation ====*/
    $.fn.hhTabsCalculation = function (options) {
        var settings = $.extend({
            'tabItemMargin': 10
        }, options);

        return this.each(function () {
            let $container = $(this),
                tabs = $('[data-tabs]', $container);
            let containerWidth = $container.width(),
                tabsItemWidth = 0;
            let position = -1;

            if ($('.hh-tabs-toggle .dropdown-menu .nav-item', $container).length) {
                $('.hh-tabs-toggle', $container).remove();
                $('[data-tabs-item]', tabs).show();
            }
            $('[data-tabs-item]', tabs).each(function (index) {
                tabsItemWidth += parseFloat($(this).outerWidth()) + settings.tabItemMargin;
                if (tabsItemWidth > containerWidth) {
                    position = index;
                    return false;
                }
            });

            if (position > 0) {
                $('.hh-tabs-toggle', $container).remove();
                $container.append('<div class="hh-tabs-toggle dropdown"><button class="btn btn-secondary dropdown-toggle" type="button" id="dropdown-tab-option" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fe-align-right"></i>' +
                    '</button><ul class="dropdown-menu dropdown-menu-right " aria-labelledby="dropdown-tab-option"></ul></div>');
                $('[data-tabs-item]', tabs).each(function (index) {
                    if (index >= position) {
                        let html = '<li class="nav-item dropdown-item" data-tabs-item><a href="#' + $(this).attr('id') + '" class="nav-link">' + $(this).html() + '</a></li>';
                        $('.hh-tabs-toggle .dropdown-menu', $container).append(html);
                        $(this).hide();
                    }
                });
                $container.find('.hh-tabs-toggle .nav-link').click(function (event) {
                    event.preventDefault();
                    let href = $(this).attr('href');

                    if ($(href).length) {
                        $(href).trigger('click');
                    }
                });
            }
        });
    };

    $(document).ready(function () {
        $('[data-tabs-calculation]').hhTabsCalculation();
    });

    var timeoutResizeTab = false;
    $(window).resize(function () {
        clearTimeout(timeoutResizeTab);
        timeoutResizeTab = setTimeout(function () {
            $('[data-tabs-calculation]').hhTabsCalculation();
        }, 100);
    });
})(jQuery);

jQuery(document).ready(function ($) {
    setTimeout(function () {
        $('.page-loading').hide();
    }, 500);

    $('#hh-choose-langs').on('change', function () {
        var url = $(this).data('url');
        url += '?lang=' + $(this).val();
        window.location.href = url;
    });

    let tagInput = document.querySelector('input[data-tag="tags"]');
    if (tagInput !== null) {
        var tagData = tagInput.getAttribute('data-tags-whitelist');
        var tagWhitelist = [];
        tagData = JSON.parse(tagData);
        if (Object.keys(tagData).length > 0) {
            for (var iTag = 0; iTag < Object.keys(tagData).length; iTag++) {
                tagWhitelist.push(tagData[Object.keys(tagData)[iTag]])
            }
        }
        let tagify = new Tagify(tagInput, {
            whitelist: tagWhitelist
        })
    }

    if ($('.hh-list-menu-box .sortable').length) {
        var menuNested = $('.hh-list-menu-box .sortable').nestedSortable({
            handle: 'div',
            items: 'li',
            toleranceElement: '> div'
        });

        $('.hh-menu-form').on('hh_form_action_before', function (el) {
            var currentEl = $(el.currentTarget);

            var i = 1;
            $('.hh-list-menu-box .sortable li').each(function () {
                $(this).attr('id', 'hh-mn-' + i);
                i++;
            });

            var menuStruture = $('.hh-list-menu-box .sortable').nestedSortable("toArray");
            currentEl.find('input[name="menu_structure"]').attr('value', JSON.stringify(menuStruture));
        });

        $(document).on('click', '.hh-list-menu-box .sortable .item .item-header', function () {
            var t = $(this),
                parent = t.parent(),
                contentWrapper = parent.find('.item-content-wrapper');

            t.closest('.hh-list-menu-box').find('.item .item-content-wrapper').not(contentWrapper).removeClass('active');
            contentWrapper.toggleClass('active');
        });

        $(document).on('click', '.hh-add-menu-box h5.title', function () {
            var t = $(this),
                parent = t.parent();

            t.closest('.hh-add-menu-box-wrapper').find('.hh-add-menu-box').not(parent).removeClass('active');
            parent.toggleClass('active');
        });

        $('.hh-btn-add-menu-item').on('click', function (e) {
            e.preventDefault();
            var t = $(this),
                parent = t.closest('.hh-add-menu-box');

            if (t.hasClass('custom-link')) {
                var menuName = parent.find('.menu-name');
                var menuURL = parent.find('.menu-url');
                var menuData = {
                    name: menuName.val() != '' ? esc_html(menuName.val()) : 'Menu title',
                    type: 'custom',
                    id: '0',
                    url: menuURL.val() != '' ? esc_html(menuURL.val()) : '#'
                };
                hhRenderMenuItem(menuData);
                menuName.val('');
                menuURL.val('');
            } else {
                var menuItem = $('.hh-add-menu-item', parent);
                menuItem.each(function () {
                    if ($(this).is(':checked')) {
                        var dataMenuItem = $(this).data();
                        hhRenderMenuItem(dataMenuItem);
                    }
                });
                menuItem.prop('checked', false);
            }

        });

        function hhRenderMenuItem(data) {
            var menuBox = $('.hh-list-menu-box .sortable'),
                numberMenuItems = menuBox.find('li').length,
                newID = 'hh-mn-' + (numberMenuItems + 1);

            var typeName = data['type'].replace('_', ' ');

            var te = '<li id="' + newID + '" data-type="' + esc_html(data['type']) + '" data-post_id="' + esc_html(data['id']) + '" data-post_title="' + esc_html(data['name']) + '">';
            te += '<div class="item type-' + esc_html(data['type']) + '">';
            te += '<div class="item-header d-flex align-items-center justify-content-between">';
            te += '<span class="name">' + esc_html(data['name']) + '</span>';
            te += '<span class="hh-delete-menu-item ml-3"><i class="fe-trash-2"></i></span>';
            te += '</div>';
            te += '<div class="item-content-wrapper">';
            te += '<div class="item-content">';
            te += '<div class="form-group name">';
            te += '<label>'+ menuBox.parent().data('menu-name') +'</label>';
            te += '<input type="text" class="form-control form-control-sm menu_name" value="' + esc_html(data['name']) + '">';
            te += '</div>';
            te += '<div class="form-group url">';
            te += '<label>'+ menuBox.parent().data('menu-url') +'</label>';
            te += '<input type="text" class="form-control form-control-sm menu_url" value="' + esc_html(data['url']) + '">';
            te += '</div>';
            te += '<div class="menu-info">';
            te += '<p class="menu-type">'+ menuBox.parent().data('menu-type') +' ' + typeName + '</p>';
            te += '<p class="menu-origin-link">'+ menuBox.parent().data('menu-origin') +' <a href="' + esc_html(data['url']) + '">' + esc_html(data['name']) + '</a></p>';
            te += '</div>';
            te += '</div>';
            te += '</div>';
            te += '</div>';
            te += '</li>';

            menuBox.append(te);
        }

        $(document).on('keyup', '.hh-list-menu-box .sortable .item .item-content input.menu_name', function () {
            $(this).closest('.item').find('.item-header .name').text($(this).val());
        });
    }
    $(document).on('click', '.hh-delete-menu-item', function (e) {
        e.preventDefault();
        var t = $(this),
            liEl = t.closest('li'),
            liElPrev = liEl.prev(),
            olEl = t.closest('ol'),
            liChild = liEl.find('ol').first().clone();

        if (liElPrev.length > 0) {
            if (liChild.length > 0) {
                $(liChild[0].innerHTML).insertAfter(liElPrev);
            }

        } else {
            if (liChild.length > 0) {
                olEl.prepend(liChild[0].innerHTML);
            }
        }
        liEl.remove();
    });
});

(function ($) {
    'use strict';

    let body = $('body');

    let HHActions = {
        tmp_data: {},
        is_running: {
            run: true
        },
        interval: {
            interval: true
        },
        init: function (el) {
            this.initCheckboxAction(el);
            this.initLinkAction(el);
            this.initFormAction(el);
            this.initSelectAction(el);
            this.initUploadMedia(el);
            this.listMedia(el);
            this.initTable(el);
            this.initGlobal(el);
            this.initValidation(el);
            this.initBindData(el);
            this.initMatchHeigth(el);
            this.initContextMenu(el);
            this.initScroll(el);
            this.initLoadDelay(el);
            this.initNeedMove(el);
            this.initMultiLanguages(el);
            this.initTableSort(el);
        },

        initTableSort: function(el){
            let base = this;
            $('[data-sort="true"]', el).each(function () {
                var t = $(this);
                t.find('tbody').sortable({
                    update: function( event, ui ) {
                        var data = {},
                            order = {};
                        t.find('tbody tr').each(function(){
                            order[$(this).data(t.data('sort-field'))] = $(this).index() + 1;
                        });
                        data['_token'] = $('meta[name="csrf-token"]').attr('content');
                        data['data'] = order;
                        console.log(data);
                        $.post(t.attr('data-sort-action'), data, function (respon) {
                            if (typeof respon == 'object') {
                                base.alert(respon);
                            }
                        }, 'json');
                    }
                });
            });
        },

        initMultiLanguages: function(el){
            if($('#hh-language-action').length) {
                $('.form.form-translation').each(function () {
                    var form = $(this);

                    var code = $('.item.active', form).data('code');

                    $('.item', form).on('click', function(){
                        $('.item', form).removeClass('active');
                        $(this).addClass('active');
                        code = $(this).data('code');

                        $('[data-lang]', form).addClass('hidden');
                        $('[data-lang="'+ code +'"]', form).removeClass('hidden');

                        if($('#hh-dashboard-service-preview').length){
                            $('[data-lang]', $('#hh-dashboard-service-preview')).addClass('hidden');
                            $('[data-lang="'+ code +'"]', $('#hh-dashboard-service-preview')).removeClass('hidden');
                        }

                        //For editor
                        if($('.hh-editor', form).length){
                            var editor_id = $('.hh-editor', form).first().attr('id');
                            var editor_name = editor_id.substr(0, editor_id.length - 2);
                            $('textarea#' + editor_name + code).addClass('hh-editor');

                            if(!$('#' + editor_name + code + '_ifr').length) {
                                if (typeof tinymce === 'object') {
                                    tinymce.init({
                                        selector: 'textarea#' + editor_name + code,
                                        width: '100%',
                                        height: 350,
                                        nowrap: true
                                    });
                                }
                            }

                            $('.hh-editor', form).each(function(){
                                var editor_id = $(this).attr('id');
                                var editor_code = editor_id.substr(editor_id.length - 2, 2);
                                if(editor_code === code){
                                    $('#' + editor_id + '_ifr').closest('.tox').show();
                                }else{
                                    $('#' + editor_id + '_ifr').closest('.tox').hide();
                                }
                            });
                        }
                    });
                });

                //Scroll
                var navBarHeight = $('#wrapper .navbar-custom').height();
                $(window).scroll(function fixedLangTool() {
                    var langTool = $('.hh-language-action');
                    langTool.each(function(){
                        var lt = $(this);
                        if($(window).scrollTop() > 100){
                            lt.addClass('has-fixed');
                            lt.css({
                                'top': navBarHeight + 'px'
                            });
                        }else{
                            lt.removeClass('has-fixed');
                            lt.css({
                                'top': 'auto'
                            });
                        }
                    });
                    return fixedLangTool;
                }());
            }
        },
        initNeedMove: function (el) {
            let base = this;
            $('.hh-need-move', el).each(function () {
                let t = $(this),
                    to = t.data('to'),
                    reinit = t.data('reinit');

                if($(to).length){
                    let html = t.html();
                    t.remove();
                    $(to).append(html);
                    if(typeof reinit == 'string'){
                        base[reinit](el);
                    }
                }
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
        initLoadDelay: function (el) {
            let base = this;

            $('.hh-get-home-near-by').on('hh_load_delay', function (ev, data, el, base) {
                let t = $(this),
                    unique = t.data('unique');
                if (t.is(el)) {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function (position) {
                            if (typeof position === 'object') {
                                let lat = position.coords.latitude;
                                let lng = position.coords.longitude;

                                if (lat && lng) {
                                    let data = {
                                        _token: $('meta[name="csrf-token"]').attr('content'),
                                        lat: lat,
                                        lng: lng,
                                        radius: t.data('radius')
                                    };
                                    base.tmp_data[unique] = data;
                                    delete base.is_running[unique];
                                }
                            }
                        });
                    }
                }
            });

            $('[data-toggle="hh-reload"]', el).click(function () {
                let t = $(this),
                    target = $(t.data('target'));
                if (target.length) {
                    load_delay(target);
                }
            });

            $('.hh-load-delay', el).each(function () {
                load_delay($(this));
            });

            function load_delay(el) {
                let render = $('.render', el),
                    loading = $('.hh-loading', el),
                    from = el.data('from') || '',
                    unique = el.data('unique'),
                    url = el.data('action');

                base.tmp_data[unique] = JSON.parse(Base64.decode(el.attr('data-data')));
                base.tmp_data[unique].push({
                    name: '_token',
                    value: $('meta[name="csrf-token"]').attr('content')
                });
                el.trigger('hh_load_delay', [base.tmp_data, el, base]);
                if (from == 'js') {
                    base.is_running[unique] = true;
                }
                loading.show();
                if (base.interval[unique]) {
                    clearInterval(base.interval[unique]);
                }
                base.interval[unique] = setInterval(function () {
                    if (typeof base.is_running[unique] == 'undefined') {
                        $.post(url, base.tmp_data[unique], function (respon) {
                            if (typeof respon == 'object') {
                                render.html(respon.html);
                            }
                            loading.hide();
                        }, 'json');
                        clearInterval(base.interval[unique]);
                        delete base.interval[unique];
                    }
                }, 500);
            }
        },
        initContextMenu: function (el) {
            let base = this;
            $.fn.contextMenu && $.contextMenu({
                selector: '.hh-all-media .hh-media-item',
                callback: function (key, options) {
                    let t = options.$trigger;
                    if (key === 'delete') {
                        $.confirm({
                            animation: 'none',
                            title: 'System Alert',
                            content: 'are you sure to delete this attachment?',
                            buttons: {
                                ok: {
                                    text: 'Delete',
                                    btnClass: 'btn-primary',
                                    action: function () {
                                        let data = JSON.parse(Base64.decode(t.attr('data-params')));
                                        if (typeof data == 'object') {
                                            data['_token'] = $('meta[name="csrf-token"]').attr('content');
                                            $.post(t.attr('data-delete-url'), data, function (respon) {
                                                if (typeof respon == 'object') {
                                                    base.alert(respon);
                                                    if (respon.status == 1) {
                                                        t.parent('li').remove();
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
                    }
                },
                items: {
                    "delete": {name: "Delete", icon: "delete"},
                    "quit": {
                        name: "Quit", icon: function () {
                            return 'context-menu-icon context-menu-icon-quit';
                        }
                    }
                }
            });
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
        initMatchHeigth: function (el) {
            $('.has-matchHeight', body).matchHeight();

            setTimeout(function () {
                $('.has-maxHeight').each(function () {
                    $(this).css('max-height', t.css('height'));
                });

                $('[data-max-height]', el).each(function () {
                    $(this).css({
                        'max-height': $(this).attr('data-max-height')
                    });
                });

                $('[data-min-height]', el).each(function () {
                    $(this).css({
                        'min-height': $(this).attr('data-min-height')
                    });
                });

                $('[data-min-width]', el).each(function () {
                    $(this).css({
                        'min-width': $(this).attr('data-min-width')
                    });
                });
            }, 500);
        },
        initBindData: function (el) {
            $('[data-hh-bind-from]', el).each(function () {
                var t = $(this);
                var target = t.data('hh-bind-from');
                if ($(target).length) {
                    let val = $(target).val() || t.data('hh-bind-default');
                    t.text(val);
                    el.on('change keyup', target, function () {
                        var val = $(this).val() || t.data('hh-bind-default');
                        t.text(val);
                    });
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
            let base = this;
            setTimeout(function () {
                $('table[data-plugin="datatable"]', el).each(function () {
                    var t = $(this),
                        columns = t.data('cols') ? JSON.parse(Base64.decode(t.data('cols'))) : [],
                        exp = typeof t.data('export') == 'undefined' ? 'off' : t.data('export');
                    if (!t.hasClass('no-data')) {
                        let dtable = t.dataTable({
                            dom: (exp === 'on') ? 'frtipB' : 'frtip',
                            buttons: [
                                {
                                    extend: (exp === 'on') ? 'csvHtml5' : '',
                                    text: (exp === 'on') ? t.data('csv-name') : '',
                                    exportOptions: {
                                        orthogonal: "exportcsv",
                                        format: {
                                            header: function (data, columnIdx) {
                                                let el = $($.parseHTML(data));
                                                let val = data;
                                                val = (el.hasClass('exp')) ? el.text() : (el.find('.exp').length ? el.find('.exp').text() : val);
                                                return val.trim();
                                            },
                                            body: function (data) {
                                                let el = $($.parseHTML(data));
                                                let val = data;
                                                val = (el.hasClass('exp')) ? el.text() : (el.find('.exp').length ? el.find('.exp').text() : val);
                                                return val.trim();
                                            }
                                        },
                                        columns: (typeof columns === 'object') ? columns : ''
                                    }
                                },
                                {
                                    extend: (exp === 'on') ? 'pdfHtml5' : '',
                                    text: (exp === 'on') ? t.data('pdf-name') : '',
                                    exportOptions: {
                                        format: {
                                            header: function (data, columnIdx) {
                                                let el = $($.parseHTML(data));
                                                let val = data;
                                                val = (el.hasClass('exp')) ? el.text() : el.find('.exp').length ? el.find('.exp').text() : val;
                                                return val.trim();
                                            },
                                            body: function (data) {
                                                let el = $($.parseHTML(data));
                                                let val = data;
                                                val = (el.hasClass('exp')) ? el.text() : el.find('.exp').length ? el.find('.exp').text() : val;
                                                return val.trim();
                                            }
                                        },
                                        columns: (typeof columns === 'object') ? columns : ''
                                    }
                                }
                            ],
                        });
                    }
                });
            }, 500);
        },

        initFormAction: function (el) {
            let base = this;
            $('form.form-action', el).each(function () {
                let form = $(this),
                    url = form.attr('action'),
                    loading = $('.hh-loading', form),
                    reloadTime = form.data('reload-time');

                if (typeof reloadTime == 'undefined') {
                    reloadTime = 0;
                }

                form.submit(function (ev) {
                    ev.stopPropagation();
                    ev.preventDefault();
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
                        $.post(url, data, function (respon) {
                            if (typeof respon === 'object') {
                                if ($('.form-message', form).length) {
                                    $('.form-message', form).html(respon.message);
                                } else {
                                    base.alert(respon);
                                }

                                form.trigger('hh_form_action_complete', [respon]);

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
                });
            });
        },
        initLinkAction: function (el) {
            let base = this;
            $('.hh-link-action', el).each(function () {
                let t = $(this),
                    parent = t.closest(t.data('parent')),
                    hasLoading = t.data('page-loading');

                t.click(function (ev) {
                    ev.preventDefault();

                    let dataConfirm = t.data('confirm');
                    if (dataConfirm == 'yes') {
                        if (hasLoading && typeof hasLoading !== 'undefined') {
                            $('.hh-loading.page-loading').show();
                        }
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
                                                        if (t.attr('data-is-delete') == 'true') {
                                                            $(parent).addClass('is-deleted');
                                                            $(parent).one(whichTransitionEvent(), function () {
                                                                $(parent).hide();
                                                            });
                                                        }
                                                    }
                                                    if (hasLoading && typeof hasLoading !== 'undefined') {
                                                        $('.hh-loading.page-loading').hide();
                                                    }
                                                    if (respon.reload) {
                                                        window.location.reload();
                                                    }
                                                }
                                            }, 'json');

                                        } else {
                                            if (hasLoading && typeof hasLoading !== 'undefined') {
                                                $('.hh-loading.page-loading').hide();
                                            }
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
                            if (hasLoading && typeof hasLoading !== 'undefined') {
                                $('.hh-loading.page-loading').show();
                            }

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
                                        if (t.attr('data-is-delete') == 'true') {
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
                                if (hasLoading && typeof hasLoading !== 'undefined') {
                                    $('.hh-loading.page-loading').hide();
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
                    base.listMedia(el);
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
        listMedia: function (el) {
            var base = this;
            $('.hh-all-media', el).each(function () {
                let t = $(this),
                    form = $('form', t),
                    url = form.attr('action'),
                    loading = $('.hh-loading', t);

                form.submit(function (ev) {
                    ev.preventDefault();
                    let data = form.serializeArray();
                    data.push({
                        name: '_token',
                        value: $('meta[name="csrf-token"]').attr('content')
                    });
                    loading.show();
                    $.post(url, data, function (respon) {
                        if (typeof respon === 'object') {
                            if (respon.status === 0) {
                                base.alert(respon);
                            }
                            if (respon.html !== '') {
                                $('.hh-all-media-render .render', t).html(respon.html);
                            }
                        }
                        loading.hide();
                    }, 'json');
                });
                form.submit();

                $('#hh-media-item-modal').on('show.bs.modal', function (e) {
                    let target = $(e.relatedTarget),
                        t = $(this),
                        loading = $('.hh-loading ', t);

                    let data = {
                        attachment_id: target.attr('data-attachment-id'),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    };
                    $('.modal-body', t).empty();
                    loading.show();
                    $.post(target.attr('data-url'), data, function (respon) {
                        if (typeof respon === 'object') {
                            if (respon.status === 0) {
                                base.alert(respon);
                            }
                            $('.modal-body', t).html(respon.html);
                        }
                        loading.hide();
                    }, 'json');
                });
            });
        },
        getFileType: function (fileName) {
            var fileType = (/[.]/.exec(fileName)) ? /[^.]+$/.exec(fileName) : undefined;
            return fileType[0];
        },
        alert: function (respon) {
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
        },
        initGlobal: function (el) {
            let base = this;

            $('.hh-get-modal-content', el).on('show.bs.modal', function (ev) {
                var t = $(this),
                    loader = $('.hh-loading', t),
                    target = $(ev.relatedTarget);
                let params = target.attr('data-params');
                if(typeof params === 'string' && params !== ''){
                    var data = JSON.parse(Base64.decode(target.attr('data-params')));
                }else{
                    var data = {};
                }
                if (typeof data == 'object') {
                    data['_token'] = $('meta[name="csrf-token"]').attr('content');

                    loader.show();
                    $('.modal-body', t).empty();

                    $.post(t.attr('data-url'), data, function (respon) {
                        if (typeof respon == 'object') {
                            if (respon.status === 1) {
                                $('.modal-body', t).html(respon.html);
                                $('body').trigger('hh_modal_render_content', [t]);
                                setTimeout(function() {
                                    base.initMultiLanguages();
                                }, 200);
                            } else {
                                t.modal('hide');
                                base.alert(respon);
                            }
                        }
                        loader.hide();
                    }, 'json');
                } else {
                    alert('Have a error when parse the data');
                }
            });
        }
    };
    HHActions.init(body);

    $('.booking-analytics', body).each(function () {
        let t = $(this);
        let _label = JSON.parse(Base64.decode(t.data('label')));
        let _data = JSON.parse(Base64.decode(t.data('total')));
        let _date = JSON.parse(Base64.decode(t.data('date')));
        let content = hh_params.currency.position == 'left' ? hh_params.currency.symbol + '%y' : '%y' + hh_params.currency.symbol;
        setTimeout(function () {
            $.plot(t.get(0),
                [
                    {label: _label[0], data: _data['completed'], lines: {show: !0, fill: !0}, points: {show: !0}},
                    {label: _label[1], data: _data['incomplete'], lines: {show: !0}, points: {show: !0}},
                    {label: _label[2], data: _data['canceled'], lines: {show: !0}, points: {show: !0}},
                    {label: _label[3], data: _data['refunded'], lines: {show: !0}, points: {show: !0}},
                ],
                {
                    series: {
                        shadowSize: 0
                    },
                    grid: {
                        hoverable: !0,
                        clickable: !0,
                        tickColor: "#f9f9f9",
                        borderWidth: 1,
                        borderColor: "#eeeeee"
                    },
                    colors: ["#1abc9c", "#f7b84b", "#FF5042", "#516a77"],
                    tooltip: !0,
                    tooltipOpts: {
                        content: content,
                        shifts: {
                            x: -60,
                            y: 25
                        },
                        defaultTheme: !1
                    },
                    legend: {
                        position: "ne",
                        margin: [0, -32],
                        noColumns: 0,
                        labelBoxBorderColor: null,
                        labelFormatter: function (o, t) {
                            return o + "&nbsp;&nbsp;"
                        },
                        width: 30,
                        height: 2
                    },
                    yaxis: {
                        axisLabel: "Point Value (1000)",
                        tickColor: "#f5f5f5",
                        font: {
                            color: "#bdbdbd"
                        }
                    },
                    xaxis: {
                        axisLabel: "Daily Hours",
                        ticks: _date,
                        tickColor: "#f5f5f5",
                        font: {
                            color: "#bdbdbd"
                        }
                    }
                }
            );
        }, 600);
    });


    $("#hh-dropdown-notification").on('show.bs.dropdown', function () {
        var t = $(this),
            data = JSON.parse(Base64.decode(t.data('params')));
        data['_token'] = $('meta[name="csrf-token"]').attr('content');

        $.post(t.data('action'), data, function (respon) {
            if (typeof respon == 'object') {
                console.log(respon.message);
            }
        }, 'json');
    });

    $('.avatar-preview .change-avatar').click(function (ev) {
        let t = $(this),
            parent = t.closest('.profile-preview');
        $('.add-attachment', parent).trigger('click');

        parent.on('input_upload_changed', 'input.input-upload', function (ev, url, val) {
            let t = $(this),
                parent = t.closest('.profile-preview');
            $('.avatar', parent).attr('src', url[0]);

            $('form', parent).submit();
        });
    });

    let ajax_run = false;
    $('.form-quick-settings', body).on('keyup change', 'input[type="text"], input[type="number"],input[type="checkbox"]', function (ev) {
        let t = $(this),
            form = t.closest('form'),
            data = form.serializeArray(),
            loading = $('.hh-loading', form);

        data.push({
            name: '_token',
            value: $('meta[name="csrf-token"]').attr('content'),
        });
        if (ajax_run) {
            ajax_run.abort();
        }
        loading.show();
        ajax_run = $.post(form.attr('action'), data, function (respon) {
            if (typeof respon == 'object') {
                console.log(respon.message);
            }
            loading.hide();
        }, 'json');
    });

    $('select[name="show_analytics_by"]').change(function (ev) {
        let t = $(this),
            val = t.val(),
            url = $('option:selected', t).attr('data-url');
        if (url) {
            window.location.href = url;
        }
    });


})(jQuery);
