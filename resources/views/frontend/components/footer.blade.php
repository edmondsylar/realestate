@php
    $logo_footer = get_option('footer_logo');
    if(empty($logo_footer)){
        $logo_footer = get_option('logo');
    }
    $list_social = get_option('list_social');
    $screen = current_screen();
    $setup_mailc_api = get_option('mailchimp_api_key');
    $setup_mailc_list_id = get_option('mailchimp_list');
    enqueue_script('nice-select-js');
    enqueue_style('nice-select-css');
@endphp
</div>
<footer id="footer" class="{{ $screen == 'page-search-result' ? 'hide-footer' : '' }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-12">
                @if(!empty($logo_footer))
                    <img src="{{ get_attachment_url($logo_footer) }}" alt="footer logo" class="footer-logo"/>
                @endif
                @if(!empty($list_social))
                    <ul class="social">
                        @foreach($list_social as $item)
                            <li>
                                <a href="{{ $item['social_link'] }}">
                                    {!! get_icon($item['social_icon']) !!}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="col-lg-5 col-md-12">
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <h4 class="footer-title">{{ get_option('footer_menu1_label') }}</h4>
                        @php
                            $menu_id = get_option('footer_menu1');
                            get_nav_by_id($menu_id);
                        @endphp
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <h4 class="footer-title">{{ get_option('footer_menu2_label') }}</h4>
                        @php
                            $menu_id = get_option('footer_menu2');
                            get_nav_by_id($menu_id);
                        @endphp
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                @if(!empty($setup_mailc_api) && !empty($setup_mailc_list_id))
                    <h4 class="footer-title">{{ get_option('footer_subscribe_label') }}</h4>
                    <p>{{ get_option('footer_subscribe_description') }}</p>
                    <form action="{{ url('subscribe-email') }}" class="subscribe-form form-sm form-action"
                          method="post" data-reload-time="1000">
                        <input type="email" id="mc-email" name="email" placeholder="{{__('Enter your email')}}"
                               class="form-control has-validation" data-validation="required"/>
                        <button type="submit"><i class="fe-arrow-right"></i> <span class="hh-loading"></span></button>
                        <div class="form-message"></div>
                    </form>
                @else
                    <small><i>{{__('Please setup Mailchimp in Settings')}}</i></small>
                @endif
            </div>
        </div>
        <div class="copy-right d-flex align-items-center justify-content-between">
            <div class="">
             {!! balanceTags(get_option('copy_right')) !!}
            </div>
            <div class="">
                @php
                    $currencies = list_currencies();
                    $url = \Illuminate\Support\Facades\Request::url();
                    $get = $_GET;
                    if(isset($get['currency'])){
                        unset($get['currency']);
                    }
                @endphp
                @if(count($currencies) > 1)
                    @php
                        enqueue_script('nice-select-js');
                        enqueue_style('nice-select-css');
                    @endphp
                    <form class="form-inline form-sm currencies" action="{{$url}}" method="GET">
                        @if(!empty($get))
                            @foreach($get as $key => $val)
                                <input type="hidden" name="{{$key}}" value="{{$val}}" />
                            @endforeach
                        @endif
                        <input type="hidden" />
                        <div class="form-group mb-0">
                            <select data-plugin="customselect"  class="form-control min-w-150 dropup" name="currency" onchange="this.form.submit();">
                                @foreach($currencies as $item)
                                    @php
                                        $selected = '';
                                        if($item['unit'] == current_currency('unit')){
                                            $selected = 'selected';
                                        }
                                    @endphp
                                    <option {{$selected}} value="{{$item['unit']}}">{{get_translate($item['name'])}}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</footer>
<script src="{{asset('js/vendor.min.js')}}"></script>

<?php do_action('footer'); ?>

<script src="{{asset('js/frontend.js')}}"></script>
</body>
</html>
