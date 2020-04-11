@php
$layout = (!empty($layout)) ? $layout : 'col-12';
if (empty($value)) {
    $value = $std;
}
$idName = str_replace(['[', ']'], '_', $id);
enqueue_style('dropzone-css');
enqueue_script('dropzone-js');
$langs = $trans == 'none' ? [""] : get_languages_field();
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
    </label> <br/>

    @foreach($langs as $key => $item)
        @php
            $url = get_attachment_url(get_translate($value, $item), 'medium');
        @endphp
        <div class="hh-upload-wrapper {{ get_lang_class($key, $item) }}" @if(!empty($item)) data-lang="{{$item}}" @endif>
            <div class="hh-upload-wrapper clearfix">
                <div class="attachments">
                    @if ($url)
                        <div class="attachment-item"><div class="thumbnail"><img src="{{ $url }}" alt="Image"></div></div>
                    @endif
                </div>
                <div class="mt-1">
                    <a href="javascript:void(0);" class="remove-attachment">{{__('Remove')}}</a>
                    <a href="javascript:void(0);" class="add-attachment"
                       title="{{__('Add Image')}}"
                       data-text="{{__('Add Image')}}"
                       data-url="{{ dashboard_url('all-media') }}">{{__('Add Image')}}</a>
                    <input type="hidden" class="upload_value input-upload" value="{{ get_translate($value, $item) }}"
                           name="{{ $id }}{{get_lang_suffix($item)}}" data-url="{{ dashboard_url('get-attachments') }}">
                </div>
            </div>
        </div>
    @endforeach
</div>
@if($break) <div class="w-100"></div> @endif

