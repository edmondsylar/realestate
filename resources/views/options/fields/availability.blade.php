@php
    $layout = (!empty($layout)) ? $layout : 'col-12';
    if (empty($value)) {
        $value = $std;
    }
    $idName = str_replace(['[', ']'], '_', $id);

    enqueue_style('daterangepicker-css');
    enqueue_script('daterangepicker-js');
    enqueue_script('daterangepicker-lang-js');
@endphp
<div id="setting-{{ $idName }}" data-condition="{{ $condition }}"
     data-unique="{{ $unique }}"
     data-operator="{{ $operation }}"
     class="form-group mb-3 col {{ $layout }} field-{{ $type }}">
    <label for="{{ $idName }}">
        {{ __($label) }}
        @if (!empty($desc))
            <i class="dripicons-information field-desc" data-toggle="popover" data-placement="right"
               data-content="{{ __($desc) }}"></i>
        @endif
    </label>
    <div class="hh-availability">
        <input type="hidden" class="calendar_input"
               data-id="{{$post_id}}" data-action="{{ dashboard_url('get-inventory') }}">
    </div>
</div>
@if($break)
    <div class="w-100"></div> @endif
@php
    $homeObject = get_post($post_id, 'home');
    $booking_type = $homeObject->booking_type;
@endphp
@if($booking_type == 'per_hour')
    <div class="modal fade hh-get-modal-content" id="hh-show-availability-time-slot-modal" tabindex="-1" role="dialog"
         aria-hidden="true" data-url="{{ dashboard_url('get-availability-time-slot') }}">
        <div class="modal-dialog">
            <div class="modal-content">
                @include('common.loading')
                <div class="modal-header">
                    <h4 class="modal-title">{{__('Booking Detail')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">{{__('Close')}}</button>
                </div>
            </div>
        </div>
    </div>
@endif
