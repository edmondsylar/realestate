@include('dashboard.components.header')
<div id="wrapper">
    @include('dashboard.components.top-bar')
    @include('dashboard.components.nav')
    <div class="content-page">
        <div class="content">
            @include('dashboard.components.breadcrumb', ['heading' => __('Package')])
            {{--Start Content--}}
            @php
                $search = Input::get('_s');
            @endphp
            <div class="card-box">
                <div class="header-area d-flex align-items-center">
                    <h4 class="header-title mb-0">{{__('All Packages')}}</h4>
                    <form class="form-inline right d-none d-sm-block" method="get">
                        <div class="form-group">
                            <input type="text" class="form-control" name="_s"
                                   value="{{ $search }}"
                                   placeholder="{{__('Search by id, name')}}">
                        </div>
                        <button type="submit" class="btn btn-default"><i class="ti-search"></i></button>
                    </form>
                </div>
                @php
                    enqueue_style('datatables-css');
                    enqueue_script('datatables-js');
                    enqueue_script('pdfmake-js');
                    enqueue_script('vfs-fonts-js');
                @endphp
                <table class="table table-large mb-0 dt-responsive nowrap w-100" data-plugin="datatable"
                       data-paging="false"
                       data-ordering="false">
                    <thead>
                    <tr>
                        <th data-priority="1">
                            {{__('Package name')}}
                        </th>
                        <th data-priority="2">
                            {{__('Description')}}
                        </th>
                        <th data-priority="3">
                            {{__('Price')}}
                        </th>
                        <th data-priority="4">
                            {{__('Time Available')}}
                        </th>
                        <th data-priority="5">
                            {{__('Commission')}}
                        </th>
                        <th data-priority="-1">{{__('Actions')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if ($allPackages['total'])
                        @foreach ($allPackages['results'] as $item)
                            <tr>
                                <td class="align-middle">
                                    {{ $item->package_name }}
                                </td>
                                <td class="align-middle">
                                    {{ $item->package_description }}
                                </td>
                                <td class="align-middle">
                                    @if(!empty($item->package_price))
                                        ${{ $item->package_price }}
                                    @else
                                        {{__('Free')}}
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @php
                                        $package_time = (int)$item->package_time;
                                        if($package_time < 0){
                                            $package_time = 0;
                                        }
                                        if(!empty($package_time)){
                                            if($package_time > 1){
                                                echo esc_html($package_time . ' days');
                                            }else{
                                                echo esc_html($package_time . ' day');
                                            }
                                        }else{
                                            echo __('Unlimited');
                                        }
                                    @endphp
                                </td>
                                <td class="align-middle">
                                    @php
                                        echo (int)$item->package_commission . '%';
                                    @endphp
                                </td>
                                <td class="align-middle text-center">
                                    <div class="dropdown d-inline-block">
                                        <a href="javascript: void(0)" class="dropdown-toggle table-action-link"
                                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                    class="ti-settings"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <?php
                                            $data = [
                                                'packageID' => $item->package_id,
                                                'packageEncrypt' => hh_encrypt($item->package_id),
                                            ];
                                            ?>
                                            <a href="javascript:void(0)" class="dropdown-item" data-toggle="modal"
                                               data-params="{{ base64_encode(json_encode($data)) }}"
                                               data-target="#hh-update-package-modal">Edit</a>
                                            <a class="dropdown-item hh-link-action hh-link-delete-package text-danger"
                                               data-action="{{ dashboard_url('delete-package-item') }}"
                                               data-parent="tr"
                                               data-is-delete="true"
                                               data-params="{{ base64_encode(json_encode($data)) }}"
                                               href="javascript: void(0)">{{__('Delete')}}</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6">
                                <h4 class="mt-3 text-center">{{__('No packages yet.')}}</h4>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                <div class="clearfix">
                    {{ dashboard_pagination(['total' => $allPackages['total']]) }}
                </div>
            </div>
            <div id="hh-update-package-modal" class="modal fade hh-get-modal-content" tabindex="-1" role="dialog"
                 aria-hidden="true"
                 data-url="{{ dashboard_url('get-package-item') }}">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form class="form form-action form-update-package-modal relative"
                              action="{{ dashboard_url('update-package-item') }}">
                            @include('common.loading')
                            <div class="modal-header">
                                <h4 class="modal-title">{{__('Update Package')}}</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                </button>
                            </div>
                            <div class="modal-body">
                            </div>
                            <div class="modal-footer">
                                <button type="submit"
                                        class="btn btn-info waves-effect waves-light">{{__('Update')}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div><!-- /.modal -->
            {{--End content--}}
            @include('dashboard.components.footer-content')
        </div>
    </div>
</div>
@include('dashboard.components.footer')
