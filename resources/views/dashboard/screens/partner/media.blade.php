@include('dashboard.components.header')
@php
    enqueue_style('context-menu');
    enqueue_script('context-menu-pos');
    enqueue_script('context-menu');

    enqueue_style('confirm-css');
    enqueue_script('confirm-js');
@endphp
<div id="wrapper">
    @include('dashboard.components.top-bar')
    @include('dashboard.components.nav')
    <div class="content-page">
        <div class="content">
            {{--start content--}}
            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        <h4 class="page-title">
                            {{__('Media library')}}
                            <a class="btn btn-info btn-xs waves-effect waves-light ml-1"
                               data-toggle="collapse" href="#hh-media-add-new" aria-expanded="true">new
                            </a>
                        </h4>
                        @php
                            enqueue_style('dropzone-css');
                            enqueue_script('dropzone-js');
                        @endphp
                        <div id="hh-media-add-new" class="hh-media-upload-area collapse mt-3">
                            <form action="{{ dashboard_url('add-media') }}" method="post" class="hh-dropzone"
                                  id="hh-upload-form" enctype="multipart/form-data">
                                <div class="fallback">
                                    <input name="file" type="file" multiple/>
                                </div>
                                <div class="dz-message text-center needsclick">
                                    <i class="h1 text-muted dripicons-cloud-upload"></i>
                                    <h3>{{__('Drop files here or click to upload.')}}</h3>
                                    <p class="text-muted">
                                        <span>{{__('Only JPG, PNG, PDF, DOC (Word), XLS (Excel), PPT, ODT and RTF files types are supported.')}}</span>
                                        <span>{{__('Maximum file size is 2MB.')}}</span>
                                    </p>
                                </div>
                            </form>
                        </div>
                        <div class="hh-all-media mt-3">
                            <form action="{{ dashboard_url('all-media') }}" class="form" method="post"></form>
                            <div class="hh-all-media-render relative">
                                @include('common.loading')
                                <ul class="render"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--end content--}}
            @include('dashboard.components.footer-content')
        </div>
    </div>
</div>
<div id="hh-media-item-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <form class="form form-action form-update-media-item-modal relative"
                  action="{{ dashboard_url('update-media-item-detail') }}" method="post">
                @include('common.loading')
                <div class="modal-header">
                    <h4 class="modal-title">{{__('Attachment Details')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit"
                            class="btn btn-info waves-effect waves-light">{{__('Update')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div><!-- /.modal -->
@include('dashboard.components.footer')
