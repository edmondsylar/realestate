@php
    $layout = (!empty($layout)) ? $layout : 'col-12';
    if (empty($value) && !is_array($value)) {
        $value = $std;
    }
    $idName = str_replace(['[', ']'], '_', $id);
    $value = explode(',', $value);
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
    </label><br/>

    <div class="checkbox-wrapper">
        @if (!empty($choices))
            @if (!is_array($choices))
                @php
                    $choicesTemp = explode(':', $choices);
                    if($choicesTemp[0] == 'taxonomy'){
                        $choicesTemp = get_taxonomies();
                    }elseif($choicesTemp[0] == 'terms'){
                        $choicesTemp = get_terms($choicesTemp[1]);
                    }
                @endphp
            @endif
            @if(!empty($choicesTemp))
            @if ($style == 'col')
                <div class="row">
                    @endif
                    @foreach ($choicesTemp as $key => $title)
                        @if ($style == 'col')
                            <div class="col-12 col-sm-4 col-md-3">
                                @endif
                                <div class="checkbox  checkbox-success @if ($style != 'col') {{$style}} @endif">
                                    <input type="checkbox"
                                           name="{{ $id }}[]"
                                           value="{{ $key }}"
                                           @if(in_array($key, $value)) checked @endif
                                           id="{{ $idName }}-{{ $key }}">

                                    <label for="{{ $id }}-{{ $key }}">
                                        @foreach($langs as $key => $item)
                                            <span class="{{get_lang_class($key, $item)}}"
                                                  @if(!empty($item)) data-lang="{{$item}}" @endif>
                                                {{ get_translate($title, $item) }}
                                            </span>
                                        @endforeach
                                    </label>
                                </div>
                                @if ($style == 'col')
                            </div>
                        @endif
                    @endforeach
                    @if ($style == 'col')
                </div>
            @endif
            @else
                <small><i>{{__('No data')}}</i></small>
            @endif
        @endif
    </div>
</div>
@if($break)
    <div class="w-100"></div> @endif
