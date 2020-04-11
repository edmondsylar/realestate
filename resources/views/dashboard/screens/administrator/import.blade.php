@include('dashboard.components.header')
@php
    enqueue_style('confirm-css');
    enqueue_script('confirm-js');
@endphp
<div id="wrapper">
    @include('dashboard.components.top-bar')
    @include('dashboard.components.nav')
    <div class="content-page">
        <div class="content mt-2">
            {{--Start Content--}}
            <div class="card-box">
                <div class="header-area d-flex align-items-center">
                    <h4 class="header-title mb-0">{{__('Import Data')}}</h4>
                </div>
                @if (file_exists(storage_path('imported')))
                    <div class="row">
                        <div class="alert alert-info">
                            {{__('You already have imported! However, you can still re-import.')}}
                        </div>
                    </div>
                @endif
                <div class="awe-import-progress">
                    <small class="mb-4 d-block text-danger">{{__('Note: Your database will be reset before import demo data.')}}
                    </small>
                    <div id="awe-import-label" class="mt-2">
                        <div class="title"><b>{{__('Import Progress')}}</b>
                            <div class="import-loader"></div>
                            <br/></div>
                    </div>
                    <div class="buttons">
                        <a href="{{ url('/') }}" class="button btn-green" id="awe_import_demo"
                           data-action="{{ dashboard_url('import-data') }}" data-confirm="{{__('Note: Your database will be reset before import demo data. Are you sure want to install demo data?')}}">{{__('Import Data')}}</a>
                    </div>
                </div>
            </div>
            {{--End content--}}
            @include('dashboard.components.footer-content')
        </div>
    </div>
</div>
@include('dashboard.components.footer')

<script>
    (function ($) {
        $(document).ready(function () {
            if ($('#awe_import_demo').length) {
                $('#awe_import_demo').click(function (e) {
                    e.preventDefault();
                    var t = $(this);
                    var cof = confirm(t.data('confirm'));
                    if (cof) {
                        $('#awe-import-label').find('.item.done').remove();
                        $('#awe-import-label').fadeIn();
                        $('#awe_import_demo').css({'opacity': '0.3'});
                        $('#awe-import-label .title').find('.import-loader').css({'display': 'inline-block'});
                        importDemo(1);

                        function importDemo(step = 1) {
                            $.post(t.data('action'), {
                                'step': step,
                                '_token': $('meta[name="csrf-token"]').attr('content')
                            }, function (respon) {
                                if (respon.next_step === 'final') {
                                    $('#awe-import-label .title').find('.import-loader').hide();
                                    $('#awe-import-label').append('<span class="item done">Completed!</span>');
                                    $('#awe_import_demo').css({'opacity': '1'});
                                } else {
                                    $('#awe-import-label').append('<span class="item done">' + respon.label + ' <span>&#10004;</span></span>');
                                    importDemo(respon.next_step);
                                }
                            }, 'json');
                        }
                    }
                });
            }
        });
    })(jQuery);
</script>
