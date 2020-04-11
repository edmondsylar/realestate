@include('dashboard.components.header');
@php
    enqueue_style('confirm-css');
    enqueue_script('confirm-js');
    $addNewValue = true;
    $formUrl = 'add-new-page';
    $textPageHeading = __('Add New Page');
    $pageStatus = 'publish';
    if(!empty($post_id) && $post_id != -1){
        $addNewValue = false;
        $formUrl = 'edit-page';
        $textPageHeading = __('Edit Page');
        if(isset($current_page)){
            $pageStatus = $current_page->status;
        }
    }
    enqueue_script('nice-select-js');
    enqueue_style('nice-select-css');
@endphp
<div id="wrapper">
    @include('dashboard.components.top-bar')
    @include('dashboard.components.nav')
    <div class="content-page">
        <div class="content">
            @include('dashboard.components.breadcrumb', ['heading' => $textPageHeading])
            <form class="form form-action relative form-translation" action="{{ dashboard_url($formUrl) }}" method="post" data-reload-time="1000">
                <?php show_lang_section(); ?>
                <!-- @include('common.loading') -->
                <input type="hidden" name="postID" value="{{ $post_id }}">
                <input type="hidden" name="postEncrypt" value="{{ hh_encrypt($post_id) }}">
                <div class="row">
                    <div class="col-12 col-md-8 order-md-4">
                        <div class="card-box">
                            <h4 class="page-title">
                                {{ $textPageHeading }}
                                @if(!$addNewValue)
                                <a target="_blank" href="{{ get_the_permalink($post_id, $current_page->post_slug, 'page') }}" class="right"><small>{{__('View page')}}</small></a>
                                @endif
                            </h4>
                            <hr />
                            {!!  \ThemeOptions::renderPageMeta('page_settings.content', $post_id, $addNewValue, 'all-page') !!}
                        </div>
                    </div>
                    <div class="col-12 col-md-4 order-md-8">
                        <div class="card-box">
                            <div class="d-flex d-md-block d-xl-flex align-items-center mb-2 justify-content-between">
                                <div class="d-flex align-items-center form-xs">
                                    <label for="hh-page-status" class="mb-0">{{__('Status')}} &nbsp;</label>
                                    <select class="form-control min-w-100" id="hh-page-status" name="post_status" data-plugin="customselect">
                                        <option value="publish" {{ $pageStatus == 'publish' ? 'selected' : ''  }}>{{__('Publish')}}</option>
                                        <option value="draft" {{ $pageStatus == 'draft' ? 'selected' : ''  }}>{{__('Draft')}}</option>
                                    </select>
                                </div>

                                <button class="btn btn-success waves-effect waves-light mb-0 mt-md-2 mt-xl-0" type="submit">{{__('Publish')}}</button>
                            </div>
                            @if(!empty($post_id) && $post_id != -1)
		                        <?php
		                        $data = [
			                        'pageID' => $post_id,
			                        'pageEncrypt' => hh_encrypt($post_id),
			                        'type' => 'in-page'
		                        ];
		                        ?>
                                <a class="hh-link-action hh-link-delete-page d-inline-flex align-items-center a-red"
                                   data-action="{{ dashboard_url('delete-page-item') }}"
                                   data-parent=""
                                   data-params="{{ base64_encode(json_encode($data)) }}"
                                   data-confirm="yes"
                                   data-is-delete="true"
                                   data-confirm-title="{{__('Confirm Delete')}}"
                                   data-confirm-question="{{__('Are you sure want to delete this page?')}}"
                                   data-confirm-button="{{__('Delete it!')}}"
                                   href="javascript: void(0)">
                                   <i class="ti-trash mr-1"></i>
                               {{__('Delete this page')}}</a>
                            @endif
                            <hr />

                        {!!  \ThemeOptions::renderPageMeta('page_settings.sidebar', $post_id, $addNewValue, 'all-page') !!}
                        </div>
                    </div>
                </div>
            </form>
            @include('dashboard.components.footer-content')
        </div>
    </div>
</div>
@include('dashboard.components.footer');
