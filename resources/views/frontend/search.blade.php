@include('frontend.components.header')
@php
    enqueue_style('mapbox-gl-css');
    enqueue_style('mapbox-gl-geocoder-css');
    enqueue_script('mapbox-gl-js');
    enqueue_script('mapbox-gl-geocoder-js');
    enqueue_script('search-js');
@endphp
<div class="hh-search-result" data-url="{{ url('get-search-result') }}">
    @include('frontend.search.search_bar')
    <div class="hh-search-content-wrapper">
        @include('common.loading')
        <div class="hh-search-results-render" data-url="{{ url(Route::currentRouteName()) }}">
            <div class="render">
                <div class="hh-search-results-string">
                    <span class="item-found">{{__('Searching home...')}}</span>
                </div>
                <div class="hh-search-content">
                    <div class="service-item list">

                    </div>
                </div>
                <div class="hh-search-pagination">

                </div>
            </div>
        </div>
        <div class="hh-search-results-map">
            @php
                $lat = Input::get('lat');
                $lng = Input::get('lng');
                $zoom = Input::get('zoom', 10);
                $in_map = true;
            @endphp
            <div class="hh-mapbox-search" data-lat="{{ $lat }}"
                 data-lng="{{ $lng }}" data-zoom="{{ $zoom }}"></div>
            <div class="hh-close-map-popup" id="hide-map-mobile">{{__('Close')}}</div>
            <div class="hh-map-tooltip">
                <div class="checkbox checkbox-success">
                    <input id="chk-map-move" type="checkbox" name="map_move" value="1">
                    <label for="chk-map-move">{{__('Search as I move the map')}}</label>
                </div>
                @include('common.loading')
            </div>
        </div>
    </div>
</div>
@include('frontend.components.footer')
