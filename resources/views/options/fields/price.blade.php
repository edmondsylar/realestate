@php
    $layout = (!empty($layout)) ? $layout : 'col-12';
    if (empty($value)) {
        $value = $std;
    }
    $idName = str_replace(['[', ']'], '_', $id);
    enqueue_style('flatpickr-css');
    enqueue_script('flatpickr-js');
@endphp
<div id="setting-{{ $idName }}" data-condition="{{ $condition }}"
     data-unique="{{ $unique }}" data-delete-url="{{ dashboard_url('delete-custom-price-item') }}"
     data-operator="{{ $operation }}"
     class="form-group mb-3 col {{ $layout }} field-{{ $type }} relative">
    @include('common.loading', ['class' => 'loading-custom-price'])
    <label for="{{ $idName }}">
        {{ __($label) }}
        @if (!empty($desc))
            <i class="dripicons-information field-desc" data-toggle="popover" data-placement="right"
               data-content="{{ __($desc) }}"></i>
        @endif
    </label>
    <div class="w-100"></div>
    <a href="javascript: void(0)" data-post-id="{{ $post_id }}" data-toggle="modal"
       data-target="#hh-bulk-edit-modal"
       class="btn btn-success btn-xs">{{ __('Add new') }}</a>
    <div class="price-render mt-4">
        @php
            $customPrice = \App\Http\Controllers\CustomPriceController::getAllPrices($post_id);
        @endphp
        @if ($customPrice['total'])
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{__('Start Date')}}</th>
                        <th scope="col">{{__('End Date')}}</th>
                        <th scope="col">{{__('Price')}}</th>
                        <th scope="col">{{__('Available')}}</th>
                        <th scope="col" width="100">{{__('Action')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($customPrice['results'] as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ date('Y-m-d', $item->start_time) }}</td>
                            <td>{{ date('Y-m-d', $item->end_time) }}</td>
                            <td>{{ convert_price($item->price) }}</td>
                            <td>
                                @php
                                    $data = [
                                        'priceID' => $item->ID,
                                        'priceEncrypt' => hh_encrypt($item->ID),
                                    ];
                                @endphp
                                <input type="checkbox" id="availability" name="availability" data-parent="tr"
                                       data-plugin="switchery" data-color="#1abc9c" class="hh-checkbox-action"
                                       data-action="{{ dashboard_url('change-home-price-status') }}"
                                       data-params="{{ base64_encode(json_encode($data)) }}"
                                       value="on" @if( $item->available == 'on') checked @endif/>
                            </td>
                            <td>
                                <a href="javascript: void(0)" class="btn btn-danger btn-xs delete-price"
                                   data-title="{{__('Delete this item?')}}"
                                   data-content="{{__('Are you sure to delete this item?')}}"
                                   data-post-id="{{ $item->home_id }}"
                                   data-price-id="{{ $item->ID }}">{{__('Delete')}}</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@if($break)
    <div class="w-100"></div> @endif
<div id="hh-bulk-edit-modal" data-action="{{ dashboard_url('add-new-custom-price') }}" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        @include('common.loading')
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{__('Add new Price')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                </button>
            </div>
            <div class="modal-body">
                <div id="setting-type_of_bulk" class="form-group field-radio">
                    <div class="row">
                        <div class="col">
                            <div class="radio radio-success">
                                <input type="radio"
                                       name="type_of_bulk"
                                       value="days_of_week"
                                       id="type_of_bulk_week" checked>
                                <label for="type_of_bulk_week">{{ __('Days of Week') }}</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="radio radio-success">
                                <input type="radio"
                                       name="type_of_bulk"
                                       value="days_of_month"
                                       id="type_of_bulk_month">
                                <label for="type_of_bulk_month">{{ __('Days of Month') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    enqueue_script('select2-js');
                    enqueue_style('select2-css');
                @endphp
                <div id="setting-days_of_week_bulk" class="form-group field-select2_multiple has-validation"
                     data-unique="" data-operator="and"
                     data-condition="type_of_bulk:is(days_of_week)">
                    <label for="days_of_week_bulk">{{ __('Days of Week') }} <span class="text-muted f11">(Leave blank to apply all)</span></label>
                    <select id="days_of_week_bulk" class="form-control select2-multiple" data-toggle="select2"
                            multiple="multiple" data-placeholder="{{ __('Choose ...') }}">
                        <option value="monday">{{__('Monday')}}</option>
                        <option value="tuesday">{{__('Tuesday')}}</option>
                        <option value="wednesday">{{__('Wednesday')}}</option>
                        <option value="thursday">{{__('Thursday')}}</option>
                        <option value="friday">{{__('Friday')}}</option>
                        <option value="saturday">{{__('Saturday')}}</option>
                        <option value="sunday">{{__('Sunday')}}</option>
                    </select>
                </div>
                <div id="setting-days_of_month_bulk" class="form-group field-select2_multiple has-validation"
                     data-unique="" data-operator="and"
                     data-condition="type_of_bulk:is(days_of_month)">
                    <label for="days_of_month_bulk">{{ __('Days of Month') }} <span class="text-muted f11">(Leave blank to apply all)</span></label>
                    <select id="days_of_month_bulk" class="form-control select2-multiple" data-toggle="select2"
                            multiple="multiple" data-placeholder="{{ __('Choose ...') }}">
                        @for($i = 1; $i <= 31; $i++)
                            <option value="{{ $i }}">{{ sprintf('%02d', $i) }}</option>
                        @endfor
                    </select>
                </div>
                <div id="setting-month_bulk" class="form-group field-select2_multiple">
                    <label for="month_bulk">{{ __('Months') }} <span class="text-danger">*</span></label>
                    <select id="month_bulk" class="form-control select2-multiple has-validation" data-validation="required" data-toggle="select2"
                            multiple="multiple" data-placeholder="{{ __('Choose ...') }}">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">{{ sprintf('%02d', $i) }}</option>
                        @endfor
                    </select>
                </div>
                <div id="setting-year_bulk" class="form-group field-select2_multiple">
                    <label for="year_bulk">{{ __('Years') }} <span class="text-danger">*</span></label>
                    <select id="year_bulk" class="form-control select2-multiple has-validation" data-validation="required" data-toggle="select2"
                            multiple="multiple" data-placeholder="{{ __('Choose ...') }}">
                        @for($i = date('Y'); $i <= (date('Y') + 2); $i ++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group form-sm">
                            <label for="price_bulk">{{ __('Price') }} <span class="text-danger">*</span></label>
                            <input id="price_bulk" type="text" name="price" value="0" class="form-control has-validation" data-validation="required">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group form-sm">
                            <label for="available_bulk">{{ __('Avalable') }}</label>
                            <div class="w-100"></div>
                            <input type="checkbox" id="available_bulk" name="available"
                                   data-plugin="switchery" data-color="#1abc9c"
                                   value="on" checked/>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="post_id_bulk" name="post_id_bulk" value="{{ $post_id }}">
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-info waves-effect waves-light add-price">{{__('Add New')}}
                </button>
            </div>
        </div>
    </div>
</div><!-- /.modal -->
<style>
    #hh-bulk-edit-modal .switchery {
        margin-top: 6px;
    }
</style>
