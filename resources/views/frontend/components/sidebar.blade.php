@if($type == 'page')
    <?php
    $args = [
        'orderby' => 'post_id',
        'order' => 'desc',
        'status' => 'publish',
        'number' => 5
    ];
    $home_recent = get_list_homes($args);
    if($home_recent['total'] > 0){
    ?>
    <div class="widget-item home-featured">
        <h3 class="widget-title">{{__('Featured')}}</h3>
        <div class="widget-content">
            <?php foreach ($home_recent['results'] as $k => $v){ ?>
            <div class="home-item">
                <div class="thumbnail-wrapper">
                    <a href="{{ get_the_permalink($v->post_id, $v->post_slug) }}">
                        <?php if(!empty($v->thumbnail_id)){ ?>
                        <img src="{{ get_attachment_url($v->thumbnail_id, [150, 150]) }}" alt="{{ get_translate($v->post_title)  }}"
                             class="img-fluid"/>
                        <?php }else{ ?>
                        <img src="{{ placeholder_image([150, 150]) }}" alt="{{ get_translate($v->post_title) }}"
                             class="img-fluid"/>
                        <?php } ?>
                    </a>
                </div>
                <div class="content">
                    <h3 class="title">
                        <a href="{{ get_the_permalink($v->post_id, $v->post_slug) }}">{{ get_translate($v->post_title) }}</a>
                    </h3>
                    <div class="meta-foooter">
                        <div class="rating">
                            @php
                                $review_count = get_comment_number($v->post_id, 'home');
                            @endphp
                            @if($review_count > 0)
                                <i class="fe-star-on"></i> <b>{{$v->rating}}</b>
                                <span>
                                    @if($review_count == 1)
                                        ({{ __('1 review') }})
                                    @else
                                        (){{ sprintf(__('%s reviews'), $review_count) }})
                                    @endif
                                </span>
                            @else
                                <span>
                                {{ __('No reviews') }}
                                </span>
                            @endif
                        </div>
                        <div class="price">
                            {{ convert_price($v->base_price) }}
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
@else
    <?php
    $categories = get_all_categories();
    if(!empty($categories)){
    ?>
    <div class="widget-item category">
        <h3 class="widget-title">{{__('Categories')}}</h3>
        <div class="widget-content">
            <?php foreach ($categories as $k => $v){ ?>
            <a href="<?php echo get_term_link($v['term_name']); ?>">{{ get_translate($v['term_title']) }}</a>
            <?php } ?>
        </div>
    </div>
    <?php } ?>

    <?php
    $post_recent = get_all_posts('post', 5);
    if($post_recent['total'] > 0){
    ?>
    <div class="widget-item post-recent">
        <h3 class="widget-title">{{__('Recent posts')}}</h3>
        <div class="widget-content">
            <?php foreach ($post_recent['results'] as $k => $v){ ?>
            <div class="post-item">
                <div class="thumbnail-wrapper">
                    <a href="{{ get_the_permalink($v->post_id, $v->post_slug, 'post') }}">
                        <?php if(!empty(get_translate($v->thumbnail_id))){ ?>
                        <img src="{{ get_attachment_url(get_translate($v->thumbnail_id), [150, 150]) }}" alt="{{ get_translate($v->post_title)  }}"
                             class="img-fluid"/>
                        <?php }else{ ?>
                        <img src="{{ placeholder_image([150, 150]) }}" alt="{{ get_translate($v->post_title)  }}"
                             class="img-fluid"/>
                        <?php } ?>
                    </a>
                </div>
                <div class="content">
                    <h3 class="title">
                        <a href="{{ get_the_permalink($v->post_id, $v->post_slug, 'post') }}">{{ get_translate($v->post_title) }}</a>
                    </h3>
                    <p class="date">{{ date('M d, Y', $v->created_at) }}</p>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
@endif
@php
    $image_id = get_option('sidebar_image');
    if(!empty($image_id)){
        $image_url = get_attachment_url($image_id);
        $image_link = get_option('sidebar_image_link');
    }
@endphp
@if(!empty($image_id))
<div class="widget-item">
    <a href="{{ $image_link }}">
        <img src="{{ $image_url }}" alt="Widget sidebar" class="img-fluid"/>
    </a>
</div>
@endif
