@if($data['total'] > 0)
    @foreach ($data['results'] as $k => $item)
        <div class="hh-service-item list" data-lng="{{ $item->location_lng }}"
             data-lat="{{ $item->location_lat }}" data-id="{{ $item->post_id }}">
            <div class="item-inner">
                <div class="thumbnail-wrapper">
                    @if($item->is_featured == 'on')
                        <div class="is-featured">{{ get_option('featured_text', __('Featured')) }}</div>
                    @endif
                    @if(!empty($item->gallery))
                        @php
                            $galleries = explode(',', $item->gallery);
                        @endphp
                        <div id="hh-item-carousel-{{ $item->post_id }}" class="hh-item-carousel carousel slide"
                             data-ride="carousel">
                            <div class="carousel-inner" role="listbox">
                                @foreach ($galleries as $key => $imageID)
                                    <div class="carousel-item @if($key == 0) active @endif">
                                        <a href="{{ get_the_permalink($item->post_id, $item->post_slug) }}"
                                           class="carousel-cell">
                                            <img src="{{ get_attachment_url($imageID, [400, 300]) }}"
                                                 alt="{{ get_translate($item->post_title) }}"/>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            <a class="carousel-control-prev" href="#hh-item-carousel-{{ $item->post_id }}" role="button"
                               data-slide="prev">
                                {!! balanceTags(get_icon('004_left_arrow', '#353535', '15px')) !!}
                                <span class="sr-only">{{__('Previous')}}</span>
                            </a>
                            <a class="carousel-control-next" href="#hh-item-carousel-{{ $item->post_id }}" role="button"
                               data-slide="next">
                                {!! balanceTags(get_icon('005_right', '#353535', '15px')) !!}
                                <span class="sr-only">{{__('Next')}}</span>
                            </a>
                        </div>
                    @else
                        <a href="{{ get_the_permalink($item->post_id, $item->post_slug) }}" class="no-gallery">
                            <img src="{{ placeholder_image([400, 300]) }}" alt="{{ get_translate($item->post_title)  }}"
                                 class="img-fluid"/>
                        </a>
                    @endif
                </div>
                <div class="content">
                    <div class="d-flex justify-content-between">
                        <div class="home-type">
                            @php
                                $type = get_term_by('id', $item->home_type);
                                $type_name = $type? get_translate($type->term_title): '';
                            @endphp
                            {{ $type_name }}
                        </div>
                        @if(enable_review())
                            <div class="rating">
                                @php
                                    $review_number = get_comment_number($item->post_id, 'home');
                                    if($review_number > 0){
                                        echo '<i class="fe-star-on"></i> ';
                                        echo '<b>'. esc_attr($item->rating ).'</b> ';
                                    }
                                    echo '<span>(';
                                    if($review_number == 1){
                                        echo sprintf(__('%s review'), esc_attr($review_number));
                                    }elseif ($review_number > 1){
                                        echo sprintf(__('%s reviews'), esc_attr($review_number));
                                    }else{
                                        echo __('No reviews');
                                    }
                                    echo ')</span>';
                                @endphp
                            </div>
                        @endif
                    </div>
                    <h3 class="title">
                        <a href="{{ get_the_permalink($item->post_id, $item->post_slug) }}">{{ get_translate($item->post_title) }}</a>
                    </h3>
                    <p class="address text-overflow"><i class="fe-map-pin mr-1"></i>{{ get_short_address($item) }}</p>
                    <div class="facilities">
                        <div class="item max-people">
                            {{ _n(__('%s guest'), __('%s guests'), (int)$item->number_of_guest) }}
                        </div>
                        <div class="item max-bedrooms">
                            {{ _n(__('%s bedroom'), __('%s bedrooms'), (int)$item->number_of_bedrooms) }}
                        </div>
                        <div class="item max-baths">
                            {{ _n(__('%s bathroom'), __('%s bathrooms'), (int)$item->number_of_bathrooms) }}
                        </div>
                    </div>
                    <div class="meta-footer">
                        <div class="price-wrapper">
                            <span class="price">{{ convert_price($item->base_price) }}</span><span
                                    class="unit">/{{get_home_booking_type($item)}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
