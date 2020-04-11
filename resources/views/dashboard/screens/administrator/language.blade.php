@include('dashboard.components.header')

@php
    enqueue_script('select2-js');
    enqueue_style('select2-css');
    enqueue_style('flag-icon-css');
@endphp

<div id="wrapper">
    @include('dashboard.components.top-bar')
    @include('dashboard.components.nav')
    <div class="content-page">
        <div class="content">
            @include('dashboard.components.breadcrumb', ['heading' => __('Languages')])
            {{--Start Content--}}

            <div class="row">
                <div class="col-lg-4">
                    <div class="card-box">
                        <div class="header-area">
                            <h4 class="header-title mb-0">{{__('Setup Languages')}}</h4>
                        </div>
                        <form action="{{ dashboard_url('update-language') }}" class="form form-action mt-3"
                              data-reload-time="1500"
                              method="post">
                            @include('common.loading')
                            @php
                                $edit_id = '';
                                $edit_lang = '';
                                $edit_name = '';
                                $edit_ficon = '';
                                $edit_fname = '';
                                $edit_status = '';
                                $edit_action = 'new';
                                if($isEdit){
                                    $edit_id = $currentLang->id;
                                    $edit_lang = $currentLang->code;
                                    $edit_name = $currentLang->name;
                                    $edit_ficon = $currentLang->flag_code;
                                    $edit_fname = $currentLang->flag_name;
                                    $edit_status = $currentLang->status;
                                    $edit_action = 'edit';
                                }
                            @endphp
                            <input type="hidden" name="id" value="{{$edit_id}}" />
                            <input type="hidden" name="action" value="{{$edit_action}}" />
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="language">{{__('Language')}}</label>
                                        @php
                                            $languages = config('locales.languages');
                                        @endphp
                                        <select name="language" id="language" class="form-control wide" data-toggle="select2">
                                            <option value="">{{__('---- Select ----')}}</option>
                                            @if(!empty($languages))
                                                @foreach($languages as $key => $value)
                                                    <option {{$edit_lang == $key ? 'selected' : ''}} value="{{ $key }}">{{ $value . ' ('. $key .')' }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="flag_icon">{{__('Flag Icon')}}</label>
                                        <div class="flag-control">
                                            <input type="text" class="form-control hh-icon-input has-validation"
                                                   data-validation="required"
                                                   readonly
                                                   id="flag_icon" name="flag_name"
                                                   data-plugin="flagicon" value="{{$edit_fname}}"
                                                   data-flags="{{ json_encode($countryData) }}"
                                                   data-flag-url="{{asset('vendors/countries/flag/64x64/')}}"
                                                   data-no-flags="{{__('No Flags')}}"
                                                   placeholder="{{__('Flag Icon')}}">
                                            <input type="hidden" name="flag_code" value="{{$edit_ficon}}" class="flag-code"/>
                                            <div class="flag-display">
                                                @if(empty($edit_ficon))
                                                    <span class="flag-icon"></span>
                                                @else
                                                    <span
                                                            style="display: block"
                                                            data-code="{{$edit_ficon}}"
                                                            data-name="{{$edit_fname}}"
                                                            class="item-flag"
                                                            style="margin: 0px 5px;">
                                                        <img src="{{asset('vendors/countries/flag/64x64/' . $edit_ficon . '.png')}}"/>

                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="name">{{__('Name')}}</label>
                                        <input type="text" class="form-control has-validation"
                                               data-validation="required" id="name" name="name"
                                               value="{{$edit_name}}" placeholder="{{__('Display name')}}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="status">{{__('Status')}}</label>
                                        @php
                                            enqueue_script('switchery-js');
                                            enqueue_style('switchery-css');
                                        @endphp
                                        <div>
                                            @php
                                                $checked = 'checked';
                                                if(!empty($edit_status) && ($edit_status == 'off' || empty($edit_status))){
                                                    $checked = '';
                                                }
                                            @endphp
                                            <input type="checkbox" id="status" name="status"
                                                   data-plugin="switchery" data-color="#1abc9c"
                                                   value="on" {{$checked}}/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-message"></div>
                            <button type="submit" class="btn btn-success mt-2">
                                @if(!$isEdit)
                                    {{__('Add new')}}
                                @else
                                    {{__('Edit')}}
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-8">
                    @php
                        $search = Input::get('_s');
                        $order = Input::get('order', 'asc');
                    @endphp
                    <div class="card-box">
                        <div class="header-area d-flex align-items-center">
                            <h4 class="header-title mb-0">{{__('All Languages')}}</h4>
                            <form class="form-inline right d-none d-sm-block" method="get">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="_s"
                                           value="{{ $search }}"
                                           placeholder="{{__('Search by code, name')}}">
                                </div>
                                <button type="submit" class="btn btn-default"><i class="ti-search"></i></button>
                            </form>
                        </div>
                        @php
                            enqueue_style('datatables-css');
                            enqueue_script('datatables-js');
                            enqueue_script('vfs-fonts-js');
                        @endphp
                        <table class="table table-large mb-0 dt-responsive nowrap w-100" data-plugin="datatable" data-sort="true"
                               data-sort-field="lang"
                               data-sort-action="change-language-order"
                               data-paging="false"
                               data-ordering="false">
                            <thead>
                            <tr>
                                <th data-priority="3">
                                    {{__('Flag')}}
                                </th>
                                @php
                                    $_order = ($order == 'asc') ? 'desc' : 'asc';
                                    $url = add_query_arg([
                                        'orderby' => 'name',
                                        'order' => $_order
                                    ]);
                                @endphp
                                <th data-priority="3">
                                    <a href="{{ $url }}" class="order">
                                        {{__('Name')}}
                                        @if($order == 'asc') <i class="icon-arrow-down"></i> @else <i
                                                class="icon-arrow-up"></i> @endif
                                        <span class="exp d-none">{{__('Name')}}</span>
                                    </a>
                                </th>

                                <th data-priority="5" class="text-center">
                                    <div class="dropdown">
                                        <a class="dropdown-toggle not-show-caret" id="dropdownFilterStatus"
                                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{__('Status')}}
                                            <i class="icon-arrow-down"></i>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="dropdownFilterStatus">
                                            <a class="dropdown-item" href="{{ remove_query_arg('status') }}">{{__('All')}}</a>
                                            <a class="dropdown-item" href="{{ add_query_arg('status', 'on') }}">{{__('ON')}}</a>
                                            <a class="dropdown-item" href="{{ add_query_arg('status', 'off') }}">{{__('OFF')}}</a>
                                        </div>
                                        <span class="exp d-none">{{__('Status')}}</span>
                                    </div>
                                </th>
                                <th data-priority="-1" class="text-center">{{__('Actions')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if ($allLanguages['total'])
                                @foreach ($allLanguages['results'] as $item)
                                    <tr data-lang="{{$item->code}}">
                                        <td class="align-middle">
                                            <img src="{{asset('vendors/countries/flag/48x48/' . $item->flag_code . '.png')}}" />
                                        </td>
                                        <td class="align-middle">
                                            <p class="mb-0"><i class="text-muted exp">
                                                    {{ $item->name . ' ('. $item->code .')' }}<br />
                                                </i>
                                            </p>
                                        </td>
                                        <td class="align-middle text-center">
                                            @php
                                                $data = [
                                                    'languageID' => $item->id,
                                                    'languageEncrypt' => hh_encrypt($item->id),
                                                ];
                                            @endphp
                                            @php
                                                enqueue_script('switchery-js');
                                                enqueue_style('switchery-css');
                                            @endphp
                                            <div>
                                                <input type="checkbox" id="language_status" name="language_status" data-parent="tr"
                                                       data-plugin="switchery" data-color="#1abc9c" class="hh-checkbox-action"
                                                       data-action="{{ dashboard_url('change-language-status') }}"
                                                       data-params="{{ base64_encode(json_encode($data)) }}"
                                                       value="on" @if( $item->status == 'on') checked @endif/>
                                                <span class="exp d-none">{{ $item->status }}</span>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="dropdown d-inline-block">
                                                <a href="javascript: void(0)" class="dropdown-toggle table-action-link"
                                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                            class="ti-settings"></i></a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <?php
                                                    $data = [
                                                        'languageID' => $item->id,
                                                        'languageEncrypt' => hh_encrypt($item->id),
                                                    ];

                                                    $url = dashboard_url('language');
                                                    $url = add_query_arg([
                                                        'action' => 'edit',
                                                        'id' => $item->id,
                                                    ], $url);
                                                    ?>
                                                    <a href="{{$url}}" class="dropdown-item">{{__('Edit')}}</a>

                                                    <a class="dropdown-item hh-link-action hh-link-delete-language"
                                                       data-action="{{ dashboard_url('delete-language-item') }}"
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
                                        <h4 class="mt-3 text-center">{{__('No languages yet.')}}</h4>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                        <div class="clearfix">
                            {{ dashboard_pagination(['total' => $allLanguages['total']]) }}
                        </div>
                    </div>
                </div>
            </div>
            {{--End content--}}
            @include('dashboard.components.footer-content')
        </div>
    </div>
</div>
@include('dashboard.components.footer')
