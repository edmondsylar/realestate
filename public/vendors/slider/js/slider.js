;(function($, window, document, undefined) {
    $.fn.otsSlider = function (options) {
        var defaults = {
            effect: 'ots-slider-scale-transform',
            container: '.ots-slider',
            itemClass: '.item',
            items: 1,
            control: true,
            controlsClass: '.ots-slider-controls',
            pagination: true,
            paginationClass: '.ots-slider-navs',
            currentPageClass: '.active',
            zIndexFrom: 99,
            autoplay: false,
            stopHover: false,
            interval: 5000,
            margin: 20,
            currentClass: 'is-showing',
            iconNext: '<svg stroke="#FFFFFF" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"' +
                '                 viewBox="0 0 32.635 32.635" style="enable-background:new 0 0 32.635 32.635;" xml:space="preserve">' +
                '            <g>' +
                '                <path d="M32.135,16.817H0.5c-0.276,0-0.5-0.224-0.5-0.5s0.224-0.5,0.5-0.5h31.635c0.276,0,0.5,0.224,0.5,0.5' +
                '                    S32.411,16.817,32.135,16.817z"/>' +
                '                <path d="M19.598,29.353c-0.128,0-0.256-0.049-0.354-0.146c-0.195-0.195-0.195-0.512,0-0.707l12.184-12.184L19.244,4.136' +
                '                    c-0.195-0.195-0.195-0.512,0-0.707s0.512-0.195,0.707,0l12.537,12.533c0.094,0.094,0.146,0.221,0.146,0.354' +
                '                    s-0.053,0.26-0.146,0.354L19.951,29.206C19.854,29.304,19.726,29.353,19.598,29.353z"/>' +
                '            </g>' +
                '            </svg>',
            iconPrev: '<svg stroke="#FFFFFF" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" x="0px"' +
                '                 y="0px"' +
                '                 viewBox="0 0 32.635 32.635" style="enable-background:new 0 0 32.635 32.635;" xml:space="preserve">' +
                '            <g>' +
                '                <path d="M32.135,16.817H0.5c-0.276,0-0.5-0.224-0.5-0.5s0.224-0.5,0.5-0.5h31.635c0.276,0,0.5,0.224,0.5,0.5' +
                '                    S32.411,16.817,32.135,16.817z"/>' +
                '                <path d="M13.037,29.353c-0.128,0-0.256-0.049-0.354-0.146L0.146,16.669C0.053,16.575,0,16.448,0,16.315s0.053-0.26,0.146-0.354' +
                '                    L12.684,3.429c0.195-0.195,0.512-0.195,0.707,0s0.195,0.512,0,0.707L1.207,16.315l12.184,12.184c0.195,0.195,0.195,0.512,0,0.707' +
                '                    C13.293,29.304,13.165,29.353,13.037,29.353z"/>' +
                '            </g>' +
                '            </svg>'
        };

        var animationEffect = {
            'ots-slider-scale-transform': {
                next: 'ots-slider-scale-transform-next',
                prev: 'ots-slider-scale-transform-prev',
                current: 'ots-slider-scale-transform-current',
                hasCustomTranslation: false
            },
            'ots-slider-scale': {
                next: 'ots-slider-scale-next',
                prev: 'ots-slider-scale-prev',
                current: 'ots-slider-scale-current',
                hasCustomTranslation: true
            },
            'ots-slider-fade': {
                next: 'ots-slider-fade-next',
                prev: 'ots-slider-fade-prev',
                current: 'ots-slider-fade-current',
                hasCustomTranslation: true
            },
        };

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


        return this.each(function () {
            var t = $(this);
            var currentOptions = $.extend({}, defaults, options);

            var container = $(currentOptions.container, t);
            var items = $(currentOptions.itemClass, container);
            var total = items.length;
            var totalPage = Math.ceil(total / currentOptions.items);
            var currentPos = 0;
            var currentPosTmp = 0;
            var controls = $(currentOptions.controlsClass, t);
            var endEffect = true;
            var transitionEvent = whichTransitionEvent();
            var timeInterval;

            $.data(this, 'otsSlider', this);

            if(currentOptions.control){
                buildControls();
            }
            if(currentOptions.pagination){
                buildNavs();
            }

            setEffect();
            setZIndex();
            setState();
            setAutoPlay();

            openWhenRendered();

            function openWhenRendered() {
                t.imagesLoaded(function () {
                    t.fadeIn();
                });
            }

            function buildControls() {
                t.append('<div class="' + currentOptions.controlsClass.substr(1) + '">');
                $(currentOptions.controlsClass, t).append('<a href="javascript: void(0)" class="ots-slider-control prev">' + currentOptions.iconPrev + '</a><a href="javascript: void(0)" class="ots-slider-control next">' + currentOptions.iconNext + '</a>');
            }

            function buildNavs() {
                t.append('<div class="' + currentOptions.paginationClass.substr(1) + '">');
                let _class = '';
                for (let i = 1; i <= totalPage; i++) {
                    if (i === currentPos + 1) {
                        _class = currentOptions.currentPageClass.substr(1);
                    } else {
                        _class = '';
                    }
                    $(currentOptions.paginationClass, t).append('<a href="#" class="page-item ' + _class + '">' + i + '</a>');
                }
            }

            function setZIndex() {
                items.each(function (index) {
                    if ($.inArray(index, currentPos) === -1) {
                        $(this).css('z-index', currentOptions.zIndexFrom - (index + 1));
                    } else {
                        $(this).css('z-index', currentOptions.zIndexFrom);
                    }
                });
            }


            function setEffect() {
                items.each(function () {
                    $(this).addClass(currentOptions.effect);
                });
            }

            function setState(control) {
                setZIndex();
                if (!control) {
                    items.eq(currentPos).addClass('' + animationEffect[currentOptions.effect].current + ' ' + currentOptions.currentClass);
                    for (let i = 0; i <= total; i++) {
                        if (i !== currentPos) {
                            items.eq(i).addClass(animationEffect[currentOptions.effect].next);
                        }
                    }
                } else {
                    endEffect = false;
                    if (control === 'next') {
                        items.eq(currentPosTmp).removeClass(animationEffect[currentOptions.effect].current + ' ' + currentOptions.currentClass).addClass(animationEffect[currentOptions.effect].prev);
                        items.eq(currentPosTmp).one(transitionEvent, function () {
                            items.eq(currentPosTmp).removeClass(' ' + animationEffect[currentOptions.effect].current + ' ' + animationEffect[currentOptions.effect].prev).addClass(animationEffect[currentOptions.effect].next);
                        });
                        items.eq(currentPos).removeClass(animationEffect[currentOptions.effect].next).addClass(animationEffect[currentOptions.effect].current + ' ' + currentOptions.currentClass);
                        items.eq(currentPos).one(transitionEvent, function () {
                            endEffect = true;
                        });

                    } else {
                        items.eq(currentPosTmp).removeClass(animationEffect[currentOptions.effect].current).addClass(animationEffect[currentOptions.effect].prev);
                        items.eq(currentPosTmp).one(transitionEvent, function () {
                            items.eq(currentPosTmp).removeClass(' ' + animationEffect[currentOptions.effect].current + ' ' + animationEffect[currentOptions.effect].prev).addClass(animationEffect[currentOptions.effect].next);
                        });
                        items.eq(currentPos).removeClass(animationEffect[currentOptions.effect].next).addClass(animationEffect[currentOptions.effect].current);
                        items.eq(currentPos).one(transitionEvent, function () {
                            endEffect = true;
                        });
                    }
                }
                setPagination();
            }

            function setPagination() {
                $('.page-item', t).removeClass(currentOptions.currentPageClass.substr(1));
                $('.page-item', t).eq(currentPos).addClass(currentOptions.currentPageClass.substr(1));
            }

            function setAutoPlay() {
                if (currentOptions.autoplay) {
                    if(timeInterval){
                        clearInterval(timeInterval);
                    }
                    timeInterval = setInterval(function () {
                        if (endEffect) {
                            let next = currentPos;
                            next++;
                            if (next >= total) {
                                next = 0;
                            }
                            goTo(next);
                        }
                    }, currentOptions.interval);
                }
            }

            function goTo(index) {
                if (index === currentPos) {
                    return;
                }
                if (index > currentPos) {
                    currentPosTmp = currentPos;
                    currentPos = index;
                    setState('next');
                } else {
                    currentPosTmp = currentPos;
                    currentPos = index;
                    setState('prev');
                }
            }

            t.hover(function () {
                if (currentOptions.autoplay && currentOptions.stopHover) {
                    clearInterval(timeInterval);
                }
            }, function () {
                setAutoPlay();
            });
            t.on('click', '.ots-slider-control.next', function (ev) {
                ev.preventDefault();
                if (endEffect) {
                    currentPosTmp = currentPos;
                    currentPos++;
                    if (currentPos >= total) {
                        currentPos = 0;
                    }
                    setState('next');
                    t.trigger('ots_slider_next', currentPos, items, t);
                }
            });
            t.on('click', '.ots-slider-control.prev', function (ev) {
                ev.preventDefault();
                if (endEffect) {
                    currentPosTmp = currentPos;
                    currentPos--;
                    if (currentPos < 0) {
                        currentPos = total - 1;
                    }
                    setState('prev');
                    t.trigger('ots_slider_prev', currentPos, items, t);
                }
            });

            t.on('click', '.page-item', function (ev) {
                ev.preventDefault();
                if (endEffect) {
                    let _current = $(this).index();
                    currentPosTmp = currentPos;
                    goTo(_current);

                    t.trigger('ots_slider_page', _current, items, t);
                }

            });
        });
    }

})(window.Zepto || window.jQuery, window, document);

if (typeof Object.create !== "function") {
    Object.create = function (obj) {
        function F() {
        };
        F.prototype = obj;
        return new F();
    };
}

;(function($, window, document, undefined) {
    var otsSlicker = {
        whichTransition: function whichTransitionEvent() {
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
        },
        init: function (options, el) {
            var base = this;
            base.options = $.extend({}, $.fn.otsSlicker.options, options);
            base.$el = $(el);
            base.$items = $(base.options.itemClass, base.$el);
            base.itemWidth = 0;
            base.total = base.$items.length;
            base.itemPerPage = base.options.items;
            base.heightContainer = 0;
            base.endEffect = true;

            setTimeout(function () {
                base.setItemPerPage();
                base.buildControls();
                base.buildSlider();
                base.setDefaultState();
                base.loadedSlider();
                base.events();
            }, 10);
        },
        reInit: function () {
            var base = this;

            base.heightContainer = 0;
            base.$items = $(base.options.itemClass, base.$el);

            base.setItemPerPage();
            base.buildSlider();
            base.setDefaultState();
        },
        loadedSlider: function () {
            var base = this;
            base.$el.imagesLoaded(function () {
                base.$el.fadeIn();
            });
        },
        setItemPerPage: function () {
            var base = this;

            if (typeof base.options.responsive == 'object') {
                $.each(base.options.responsive, function (index, value) {
                    let _width = parseInt(index);
                    if ($(window).width() >= _width) {
                        base.itemPerPage = parseInt(value.items);
                    }
                });
            }
        },
        buildControls: function () {
            var base = this;
            base.$el.append('<div class="' + base.options.controlClass.substr(1) + '"></div>');
            $(base.options.controlClass).append('<a href="javascript: void(0)" class="ots-slick-control prev">' + base.options.iconPrev + '</a><a href="javascript: void(0)" class="ots-slick-control next">' + base.options.iconNext + '</a>');
        },
        buildSlider: function () {
            var base = this;
            var _withContainer = base.$el.width() + (base.options.margin * 2);
            var _widthItem = (_withContainer - (base.options.margin * (base.itemPerPage + 1))) / base.itemPerPage;
            base.itemWidth = _widthItem;

            base.$el.imagesLoaded(function () {
                base.$items.each(function (index) {
                    var t = $(this);
                    let transform = (_widthItem * index) + (base.options.margin * (index + 1));
                    t.css({
                        'width': _widthItem,
                        '-webkit-transform': 'translate(' + transform + 'px, 0px)',
                        '-o-transform': 'translate(' + transform + 'px, 0px)',
                        'transform': 'translate(' + transform + 'px, 0px)',
                        'opacity': '',
                        '-webkit-transition-duration': '',
                        '-o-transition-duration': '',
                        'transition-duration': '',
                        '-webkit-transition-delay': '',
                        '-o-transition-delay': '',
                        'transition-delay': '',
                    });
                    let _height = t.height();
                    if (_height > base.heightContainer) {
                        base.heightContainer = _height;
                    }

                    $(base.options.containerClass, base.$el).css({
                        'height': base.heightContainer,
                        'width': _withContainer,
                        'margin-left': base.options.margin * -1,
                        'margin-right': base.options.margin * -1,
                    });
                });
            });
        },
        setDefaultState: function () {
            var base = this;
            for (let i = 0; i < base.itemPerPage; i++) {
                base.$items.eq(i).addClass(base.options.currentClass.substr(1));
            }
        },
        next: function () {
            var base = this;
            base.endEffect = false;

            let delay = 0.1;
            $('.item.is-showing', base.options.containerClass).each(function (index) {
                var t = $(this);
                let transform = (base.itemWidth * (index + base.itemPerPage)) + (base.options.margin * ((index + base.itemPerPage) + 1));
                t.clone().removeClass(base.options.currentClass.substr(1)).css({
                    '-webkit-transform': 'translate(' + transform + 'px, 0px)',
                    '-o-transform': 'translate(' + transform + 'px, 0px)',
                    'transform': 'translate(' + transform + 'px, 0px)',
                }).appendTo(base.options.containerClass);

                delay += 0.1;
                let _widthContainer = base.$el.outerWidth();
                t.css({
                    'transform': 'translate(' + (base.itemWidth - _widthContainer) + 'px, 0px)',
                    '-webkit-transform': 'translate(' + (base.itemWidth - _widthContainer) + 'px, 0px)',
                    '-o-transform': 'translate(' + (base.itemWidth - _widthContainer) + 'px, 0px)',
                    'transition-delay': '' + delay + 's',
                    '-webkit-transition-delay': '' + delay + 's',
                    '-o-transition-delay': '' + delay + 's',
                    'opacity': 0
                });
                t.one(base.whichTransition(), function () {
                    t.remove();
                });
            });

            delay = 0.5;
            $('.item:not(.is-showing)', base.options.containerClass).each(function (index) {
                var t = $(this);
                if (index + 1 > base.itemPerPage) {
                    let transform = (base.itemWidth * index) + (base.options.margin * (index + 1));
                    t.css({
                        'transform': 'translate(' + transform + 'px, 0px)',
                        '-webkit-transform': 'translate(' + transform + 'px, 0px)',
                        '-o-transform': 'translate(' + transform + 'px, 0px)',
                        '-webkit-transition-duration': '0s',
                        '-o-transition-duration': '0s',
                        'transition-duration': '0s',
                        '-webkit-transition-delay': '0s',
                        '-o-transition-delay': '0s',
                        'transition-delay': '0s',
                        'opacity': 0
                    });
                } else {
                    let transform = (base.itemWidth * index) + (base.options.margin * (index + 1));
                    if (index + 1 <= base.itemPerPage) {
                        delay += 0.1;
                        t.css({
                            'transform': 'translate(' + transform + 'px, 0px)',
                            '-webkit-transform': 'translate(' + transform + 'px, 0px)',
                            '-o-transform': 'translate(' + transform + 'px, 0px)',
                            'transition-delay': '' + delay + 's',
                            '-webkit-transition-delay': '' + delay + 's',
                            '-o-transition-delay': '' + delay + 's',
                            '-webkit-transition-duration': '0.8s',
                            '-o-transition-duration': '0.8s',
                            'transition-duration': '0.8s',
                            'opacity': 1
                        });
                        t.addClass(base.options.currentClass.substr(1));
                    }
                }
                if (index === base.itemPerPage - 1) {
                    t.one(base.whichTransition(), function () {
                        base.endEffect = true;
                    });
                }
            });
        },
        prev: function () {
            var base = this;
            base.endEffect = false;

            let delay = 0.1;

            var _isShowingLen = $('.item.is-showing', base.options.containerClass).length;
            $('.item.is-showing', base.options.containerClass).each(function (index) {
                var t = $(this);
                let transform = (base.itemWidth * (index + base.itemPerPage)) + (base.options.margin * ((index + base.itemPerPage) + 1));
                t.clone().removeClass(base.options.currentClass.substr(1)).css({
                    'transform': 'translate(' + transform + 'px, 0px)',
                }).appendTo(base.options.containerClass);

                let _widthContainer = base.$el.outerWidth();
                t.css({
                    'transform': 'translate(' + (base.itemWidth + _widthContainer) + 'px, 0px)',
                    '-webkit-transform': 'translate(' + (base.itemWidth + _widthContainer) + 'px, 0px)',
                    '-o-transform': 'translate(' + (base.itemWidth + _widthContainer) + 'px, 0px)',
                    '-webkit-transition-duration': '0.8s',
                    '-o-transition-duration': '0.8s',
                    'transition-duration': '0.8s',
                    'transition-delay': '' + (_isShowingLen - index) * delay + 's',
                    '-webkit-transition-delay': '' + (_isShowingLen - index) * delay + 's',
                    '-o-transition-delay': '' + (_isShowingLen - index) * delay + 's',
                    'opacity': 0
                });
                t.one(base.whichTransition(), function () {
                    t.remove();
                });
            });

            delay = 0.5;

            $('.item:not(.is-showing)', base.options.containerClass).each(function (index) {
                var t = $(this);
                if (index + 1 > base.itemPerPage) {
                    let transform = (base.itemWidth * index) + (base.options.margin * (index + 1));
                    t.css({
                        'transform': 'translate(' + transform + 'px, 0px)',
                        '-webkit-transform': 'translate(' + transform + 'px, 0px)',
                        '-o-transform': 'translate(' + transform + 'px, 0px)',
                        '-webkit-transition-duration': '0s',
                        '-o-transition-duration': '0s',
                        'transition-duration': '0s',
                        '-webkit-transition-delay': '0s',
                        '-o-transition-delay': '0s',
                        'transition-delay': '0s',
                        'opacity': 0
                    });
                } else {
                    let transform = -(((base.itemPerPage - index) * base.itemWidth) + ((base.itemPerPage - index) * base.options.margin));
                    t.css({
                        'transform': 'translate(' + transform + 'px, 0px)',
                        '-webkit-transform': 'translate(' + transform + 'px, 0px)',
                        '-o-transform': 'translate(' + transform + 'px, 0px)',
                        '-webkit-transition-duration': '0s',
                        '-o-transition-duration': '0s',
                        'transition-duration': '0s',
                    });

                    transform = (base.itemWidth * index) + (base.options.margin * (index + 1));
                    let _delay = Math.round((base.itemPerPage - (index + 1)) * 0.1 * 100) / 100 + delay;
                    setTimeout(function () {
                        t.css({
                            '-webkit-transform': 'translate(' + transform + 'px,0px)',
                            '-o-transform': 'translate(' + transform + 'px,0px)',
                            'transform': 'translate(' + transform + 'px,0px)',
                            '-webkit-transition-duration': '0.8s',
                            '-o-transition-duration': '0.8s',
                            'transition-duration': '0.8s',
                            '-webkit-transition-delay': '' + _delay + 's',
                            '-o-transition-delay': '' + _delay + 's',
                            'transition-delay': '' + _delay + 's',
                            'opacity': 1
                        });
                        t.addClass(base.options.currentClass.substr(1));
                    }, 50);
                }
                if (index === 0) {
                    t.one(base.whichTransition(), function () {
                        base.endEffect = true;
                    });
                }
            });

        },
        events: function () {
            var base = this;
            base.$el.on('click', 'a.next', function (ev) {
                ev.preventDefault();
                if (base.endEffect) {
                    base.next();
                }
            });

            base.$el.on('click', 'a.prev', function (ev) {
                ev.preventDefault();
                if (base.endEffect) {
                    base.prev();
                }
            })

            $(window).on('resize', function () {
                base.reInit();
            });
        }
    };
    $.fn.otsSlicker = function (options) {
        return this.each(function () {
            var vin = Object.create(otsSlicker);
            vin.init(options, this);
            $.data(this, "otsSlicker", vin);
        });
    };
    $.fn.otsSlicker.options = {
        items: 5,
        margin: 20,
        currentClass: '.is-showing',
        containerClass: '.ots-slick',
        itemClass: '.item',
        controlClass: '.ots-slick-controls',
        paginationClass: '.ots-slick-navs',
        iconNext: '<svg stroke="#FFFFFF" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"' +
            '                 viewBox="0 0 32.635 32.635" style="enable-background:new 0 0 32.635 32.635;" xml:space="preserve">' +
            '            <g>' +
            '                <path d="M32.135,16.817H0.5c-0.276,0-0.5-0.224-0.5-0.5s0.224-0.5,0.5-0.5h31.635c0.276,0,0.5,0.224,0.5,0.5' +
            '                    S32.411,16.817,32.135,16.817z"/>' +
            '                <path d="M19.598,29.353c-0.128,0-0.256-0.049-0.354-0.146c-0.195-0.195-0.195-0.512,0-0.707l12.184-12.184L19.244,4.136' +
            '                    c-0.195-0.195-0.195-0.512,0-0.707s0.512-0.195,0.707,0l12.537,12.533c0.094,0.094,0.146,0.221,0.146,0.354' +
            '                    s-0.053,0.26-0.146,0.354L19.951,29.206C19.854,29.304,19.726,29.353,19.598,29.353z"/>' +
            '            </g>' +
            '            </svg>',
        iconPrev: '<svg stroke="#FFFFFF" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" x="0px"' +
            '                 y="0px"' +
            '                 viewBox="0 0 32.635 32.635" style="enable-background:new 0 0 32.635 32.635;" xml:space="preserve">' +
            '            <g>' +
            '                <path d="M32.135,16.817H0.5c-0.276,0-0.5-0.224-0.5-0.5s0.224-0.5,0.5-0.5h31.635c0.276,0,0.5,0.224,0.5,0.5' +
            '                    S32.411,16.817,32.135,16.817z"/>' +
            '                <path d="M13.037,29.353c-0.128,0-0.256-0.049-0.354-0.146L0.146,16.669C0.053,16.575,0,16.448,0,16.315s0.053-0.26,0.146-0.354' +
            '                    L12.684,3.429c0.195-0.195,0.512-0.195,0.707,0s0.195,0.512,0,0.707L1.207,16.315l12.184,12.184c0.195,0.195,0.195,0.512,0,0.707' +
            '                    C13.293,29.304,13.165,29.353,13.037,29.353z"/>' +
            '            </g>' +
            '            </svg>',
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 3
            },
            1200: {
                items: 5
            }
        }
    };
})(window.Zepto || window.jQuery, window, document);