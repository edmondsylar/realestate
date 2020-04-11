@php
$layout = (!empty($layout)) ? $layout : 'col-12';
if (empty($value)) {
    $value = $std;
}
$idName = str_replace(['[', ']'], '_', $id);

enqueue_script('tinymce-js');
$langs = $trans == 'none' ? [""] : get_languages_field();
$classLangs = '';
if(!empty($langs) && !empty($langs[0])){
    $classLangs = 'has-editor-translation';
}
@endphp
<div id="setting-{{ $idName }}" data-condition="{{ $condition }}"
     data-unique="{{ $unique }}"
     data-operator="{{ $operation }}"
     class="form-group mb-3 col {{ $layout }} field-{{ $type }} {{ $classLangs }}">
    <label for="{{ $idName }}">
        {{ __($label) }}
        @if (!empty($desc))
            <i class="dripicons-information field-desc" data-toggle="popover" data-placement="right"
               data-content="{{ __($desc) }}"></i>
        @endif
    </label>
    @php
        $value = str_replace('\\', '', $value);
    @endphp
    @foreach($langs as $key => $item)
        <textarea class="form-control {{get_lang_class($key, $item)}} {{ $key == 0 ? ' hh-editor ' : 'hh-editor-dynamic' }}" id="{{ $idName }}{{get_lang_suffix($item)}}"  name="{{ $id }}{{get_lang_suffix($item)}}" @if(!empty($item)) data-lang="{{$item}}" @endif>{!! balanceTags(get_translate($value, $item)) !!}</textarea>
    @endforeach
</div>
<?php if ($break) echo '<div class="w-100"></div>' ?>
