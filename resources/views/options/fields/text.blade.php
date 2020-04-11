@php
    $layout = (!empty($layout)) ? $layout : 'col-12';
    if (empty($value)) {
        $value = $std;
    }
    if (!empty($maxlength) && is_array($maxlength)) {
        $maxlengthHtml = '';
        foreach ($maxlength as $k => $v) {
            if ($k == 'max-length') {
                $maxlengthHtml .= ' maxlength="' . $v . '" ';
            } else {
                $maxlengthHtml .= ' data-' . $k . '="' . $v . '" ';
            }
        }
    }
    $idName = str_replace(['[', ']'], '_', $id);
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
    </label>

    <?php
    $current_screen = \Illuminate\Support\Facades\Route::currentRouteName();
    $is_new_home = false;
    if($trans != 'none' && $current_screen == 'add-new-home' && $id == 'post_title'){
        $exp = explode(' - ', $value);
        if($exp[0] == 'New Home'){
            $is_new_home = true;
        }
    }
    ?>

    @foreach($langs as $key => $item)
        <?php
            $valTemp = $value;
            if($is_new_home){
                if($key > 0){
                    $valTemp = '';
                }
            }
        ?>
        <input type="text" id="{{ $idName }}{{get_lang_suffix($item)}}"
           data-validation="{{ $validation }}"
           class="form-control {{get_lang_class($key, $item)}} @if(!empty($maxlengthHtml)) has-maxLength @endif  @if(!empty($validation)) has-validation @endif"
           @if (isset($maxlengthHtml)) {!! balanceTags($maxlengthHtml) !!} @endif
           name="{{ $id }}{{get_lang_suffix($item)}}" value="{{ get_translate($valTemp, $item) }}" @if(!empty($item)) data-lang="{{$item}}" @endif>
    @endforeach
</div>
@if($break) <div class="w-100"></div> @endif
