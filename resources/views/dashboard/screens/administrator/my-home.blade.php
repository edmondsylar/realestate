@include('dashboard.components.header')
@php
    enqueue_style('confirm-css');
    enqueue_script('confirm-js');
@endphp

<div id="wrapper">
    @include('dashboard.components.top-bar')
    @include('dashboard.components.nav')
    <div class="content-page">
        <div class="content">
            @include('dashboard.components.breadcrumb', ['heading' => __('My Home')])
            {{--Start Content--}}
            <div class="card-box">
                <div class="header-area d-flex align-items-center">
                    <h4 class="header-title mb-0">{{__('All Homes')}}</h4>
                    <form class="form-inline right d-none d-sm-block" method="get">
                        <div class="form-group">
                            @php
                                $search = Input::get('_s');
                                $order = Input::get('order', 'desc');
                            @endphp
                            <input type="text" class="form-control" name="_s"
                                   value="{{ $search }}"
                                   placeholder="{{__('Search by id, title')}}">
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
                @php
                    $tableColumns = [0, 1, 2, 3 ,4];
                @endphp
                <table class="table table-large mb-0 dt-responsive nowrap w-100" data-plugin="datatable"
                       data-paging="false"
                       data-export="on"
                       data-csv-name="{{__('Export to CSV')}}"
                       data-pdf-name="{{__('Export to PDF')}}"
                       data-cols="{{ base64_encode(json_encode($tableColumns)) }}"
                       data-ordering="false">
                    <thead>
                    <tr>
                        @php
                            $_order = ($order == 'asc') ? 'desc' : 'asc';
                            $url = add_query_arg([
                                'orderby' => 'post_title',
                                'order' => $_order
                            ]);
                        @endphp
                        <th data-priority="1">
                            <a href="{{ $url }}" class="order">
                                <span class="exp">{{__('Name')}}</span>
                                @if($order == 'asc') <i class="icon-arrow-down"></i> @else <i
                                        class="icon-arrow-up"></i> @endif
                            </a>
                        </th>
                        @php
                            $_order = ($order == 'asc') ? 'desc' : 'asc';
                            $url = add_query_arg([
                                'orderby' => 'base_price',
                                'order' => $_order
                            ]);
                        @endphp
                        <th data-priority="3">
                            <a href="{{ $url }}" class="order">
                                <span class="exp">{{__('Price')}}</span>
                                @if ($order == 'asc') <i class="icon-arrow-down"></i> @else <i
                                        class="icon-arrow-up"></i> @endif
                            </a>
                        </th>
                        <th data-priority="4" class="text-center">
                            <div class="dropdown">
                                <a class="dropdown-toggle not-show-caret" id="dropdownFilterStatus"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="exp">{{__('Status')}}</span>
                                    <i class="icon-arrow-down"></i>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownFilterStatus">
                                    <a class="dropdown-item"
                                       href="{{ remove_query_arg('status') }}">{{__('All')}}</a>
                                    <?php
                                    $status = service_status_info();
                                    foreach ($status as $key => $_status) {
                                    $url = add_query_arg('status', $key);
                                    ?>
                                    <a class="dropdown-item"
                                       href="{{ $url }}">{{ $_status['name'] }}</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </th>
                        <th data-priority="5" class="text-center"><span class="exp">{{__('No. Guest')}}</span></th>
                        <th data-priority="6"><span class="exp">{{__('Home Type')}}</span></th>
                        <th data-priority="-1" class="text-center">{{__('Actions')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if ($allHomes['total'])
                        @foreach ($allHomes['results'] as $item)
                            @php
                                $homeID = $item->post_id;
                                $thumbnail_id = get_home_thumbnail_id($homeID);
                                $thumbnail = get_attachment_url($thumbnail_id, [75, 75]);
                                $homeType = $item->home_type;
                                $term = get_term_by('term_id', $homeType);
                            @endphp
                            <tr>
                                <td class="align-middle">
                                    <div class="media align-items-center">
                                        <img src="{{ $thumbnail }}" class="d-none d-md-block mr-3"
                                             alt="{{ get_attachment_alt($thumbnail_id) }}">
                                        <div class="media-body">
                                            <h5 class="m-0"><a href="{{ get_the_permalink($homeID) }}"
                                                               target="_blank">{{ get_translate($item->post_title) }}</a>
                                                <span class="text-muted"> - {{ $homeID }}</span>
                                            </h5>
                                            <span class="exp d-none">[{{ $homeID }}] {{ get_translate($item->post_title) }}</span>
                                            <p class="text-muted mb-0 mt-2 d-none d-md-block">{{ get_short_address($item) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <span class="exp">{{ convert_price($item->base_price) }}</span>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="service-status {{ $item->status }} status-icon"
                                         data-toggle="tooltip" data-placement="right" title=""
                                         data-original-title="{{ service_status_info($item->status)['name'] }}"><span
                                                class="exp d-none">{{ service_status_info($item->status)['name'] }}</span>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="exp">{{ $item->number_of_guest }}</span>
                                </td>
                                <td class="align-middle">
                                    <span class="exp">@if($term) {{ get_translate($term->term_title) }} @endif</span>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="dropdown dropleft">
                                        <a href="javascript: void(0)" class="dropdown-toggle table-action-link"
                                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                    class="ti-settings"></i></a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" target="_blank"
                                               href="{{ dashboard_url('edit-home', $homeID) }}">{{__('Edit')}}</a>
                                            @php
                                                $service_status_info = service_status_info();
                                            @endphp
                                            @foreach($service_status_info as $key => $status)
                                                @php
                                                    $data = [
                                                        'serviceID' => $homeID,
                                                        'serviceEncrypt' => hh_encrypt($homeID),
                                                        'status' => $key
                                                    ];
                                                @endphp
                                                <a class="dropdown-item hh-link-action hh-link-change-status-home"
                                                   data-action="{{ dashboard_url('change-status-home') }}"
                                                   data-parent="tr"
                                                   data-is-delete="false"
                                                   data-params="{{ base64_encode(json_encode($data)) }}"
                                                   href="javascript: void(0)">{{ __($status['name']) }}</a>
                                            @endforeach
                                            @php
                                                $data = [
                                                    'serviceID' => $homeID,
                                                    'serviceEncrypt' => hh_encrypt($homeID),
                                                ];
                                            @endphp
                                            <a class="dropdown-item hh-link-action hh-link-delete-home"
                                               data-action="{{ dashboard_url('delete-home-item') }}"
                                               data-parent="tr"
                                               data-is-delete="true"
                                               data-confirm="yes"
                                               data-confirm-title="{{__('System Alert')}}"
                                               data-confirm-question="{{__('Are you sure want to delete this home?')}}"
                                               data-confirm-button="{{__('Delete it!')}}"
                                               data-params="{{ base64_encode(json_encode($data)) }}"
                                               href="javascript: void(0)">{{__('Delete')}}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7">
                                <h4 class="mt-3 text-center">{{__('No home yet.')}}</h4>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                <div class="clearfix mt-2">
                    {{ dashboard_pagination(['total' => $allHomes['total']]) }}
                </div>
            </div>
            {{--End content--}}
            @include('dashboard.components.footer-content')
        </div>
    </div>
</div>
@include('dashboard.components.footer')
