<div class="post-comment-list">
    <?php
    if(!empty($comments['count'])){
	$comment_number = get_comment_number($post->post_id);
    ?>
        <h3 class="comment-count">
            <?php
                if($comment_number == 1){
                    echo '1 comment for this post';
                }else{
                    echo sprintf('%s comments for this post', esc_attr($comment_number));
                }
            ?>
        </h3>
    <?php
        render_comment_list($comments['results']);
        frontend_pagination([
            'total' => $comments['count'],
            'posts_per_page' => comments_per_page(),
            'query_string' => '',
            'current_url' => '',
            'page' => \Illuminate\Support\Facades\Input::get('comment_page', 1)
        ], true);
    }
    ?>
</div>

<div class="post-comment parent-form" id="hh-comment-section">
    <div class="comment-form-wrapper">
        <form action="{{ url('add-post-comment') }}" class="comment-form form-sm form-action form-add-post-comment" data-google-captcha="yes"
              method="post" data-reload-time="1000">
            <h3 class="comment-title">{{__('Leave a Reply')}}</h3>
            <p class="notice">{{__('Your email address will not be published. Required fields are marked *')}}</p>
            @include('common.loading')
            <input type="hidden" name="post_id" value="{{ $post->post_id }}"/>
            <input type="hidden" name="comment_id" value="0"/>
            <input type="hidden" name="comment_type" value="posts"/>
            <div class="row">
                <?php if(!is_user_logged_in()){ ?>
                <div class="form-group col-lg-6">
                    <input id="comment-name" type="text" name="comment_name" class="form-control has-validation" placeholder="{{__('Your name*')}}" data-validation="required"/>
                </div>
                <div class="form-group col-lg-6">
                    <input id="comment-email" type="email" name="comment_email" class="form-control has-validation" placeholder="{{__('Your email*')}}" data-validation="required"/>
                </div>
                <?php } ?>
                <div class="form-group col-lg-12">
                    <textarea id="comment-content" name="comment_content" placeholder="{{__('Comment*')}}" class="form-control has-validation" data-validation="required"></textarea>
                </div>
            </div>
            <div class="form-message"></div>
            <button type="submit" class="btn btn-primary">{{__('POST COMMENT')}}</button>
        </form>
    </div>
</div>
