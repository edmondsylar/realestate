@php
    $options = Config::get('awebooking.theme_options');
    $availableFields = \ThemeOptions::filterFields($options);
@endphp
<div id="hh-options-wrapper" class="hh-options-wrapper">
    @include('common.loading')
    <div class="hh-options-tab" data-tabs-calculation>
        <div class="nav nav-pills nav-pills-tab" id="v-pills-tab" role="tablist" data-tabs
             aria-orientation="vertical">
            @foreach ($options['sections'] as $key => $section)
                @php $class = ($key == 0) ? 'active show' : ''; @endphp
                <a class="nav-link mb-2 {{ $class }}" data-tabs-item
                   id="v-pills-{{ $section['id'] }}-tab" data-toggle="pill"
                   href="#v-pills-{{ $section['id'] }}"
                   role="tab" aria-controls="v-pills-{{ $section['id'] }}"
                   aria-selected="true">
                    @if (!empty($section['icon']))
                        <i class="tab-icon {{ $section['icon'] }}"></i>
                    @endif
                    {{ __($section['label']) }}
                </a>
            @endforeach
        </div>
    </div>
    <div class="hh-options-tab-content">
        <div class="tab-content">
            @foreach ($options['sections'] as $key => $section)
                @php $class = ($key == 0) ? 'active show' : ''; @endphp
                <div class="tab-pane fade {{ $class }}"
                     id="v-pills-{{ $section['id'] }}" role="tabpanel"
                     aria-labelledby="v-pills-{{ $section['id'] }}-tab">
                    <form class="form hh-options-form form-translation"
                          action="{{ dashboard_url('settings') }}"
                          method="post" data-tab="">
                        <?php
                        if(!isset($section['trans']) || (isset($section['trans']) && $section['trans'] != 'none')){
                            show_lang_section('mb-2');
                        }
                        ?>
                        <input type="hidden" name="hh_options_fields"
                               value="{{ base64_encode(serialize($options)) }}">
                        <input type="hidden" name="hh_options_available_fields"
                               value="{{ base64_encode(serialize($availableFields)) }}">
                        <input type="hidden" name="action" value="hh_options_save_option">
                        <div class="row">
                            @php $currentOptions = []; @endphp
                            @foreach ($options['fields'] as $_key => $field)
                                @if ($field['section'] === $section['id'])
                                    @if ($field['type'] === 'tab')
                                        <div class="col col-12">
                                            <ul class="nav nav-tabs nav-bordered">
                                                @foreach ($field['tab_title'] as $__key => $title)
                                                    @php $class = ($__key == 0) ? 'active show' : ''; @endphp
                                                    <li class="nav-item">
                                                        <a href="#{{ $title['id'] }}"
                                                           data-toggle="tab"
                                                           aria-expanded="false"
                                                           class="nav-link {{ $class }}">
                                                            {{ __($title['label']) }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <div class="tab-content">
                                                @foreach ($field['tab_title'] as $__key => $title)
                                                    @php $class = ($__key == 0) ? 'active show' : ''; @endphp
                                                    <div class="tab-pane {{ $class }}"
                                                         id="{{ $title['id'] }}">
                                                        <div class="row">
                                                            @foreach ($field['tab_content'] as $___key => $content)
                                                                @php
                                                                    if ($content['section'] == $title['id']) {
                                                                        $currentOptions[] = $content;
                                                                        $content['value'] = \ThemeOptions::getOption($content['id']);
                                                                        $content = \ThemeOptions::mergeField($content);
                                                                       echo \ThemeOptions::loadField($content);
                                                                    }
                                                                @endphp
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        @php
                                            $currentOptions[] = $field;
                                            $field['value'] = \ThemeOptions::getOption($field['id'], null, true);
                                            $field = \ThemeOptions::mergeField($field);
                                            $field['field'] = $field;
                                            echo \ThemeOptions::loadField($field);
                                        @endphp
                                    @endif
                                @endif
                            @endforeach
                            <input type="hidden" name="currentOptions"
                                   value="{{ base64_encode(serialize($currentOptions)) }}">
                        </div>
                        <div class="clearfix mt-3">
                            <button class="btn btn-success btn-has-spinner right width-xl waves-effect waves-light "
                                    type="submit">
                                {{__('Save Options')}}
                            </button>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</div>
