@php
    if (is_null($newHome)) {
        return;
    }
    $metaValue = get_post($newHome);
    $options = Config::get('awebooking.'. $key);
    $availableField = \ThemeOptions::filterFields($options);
@endphp

<div id="hh-options-wrapper" class="hh-options-wrapper">
    @include('common.loading')
    <div class="hh-options-tab">
        <div class="nav nav-pills nav-pills-tab @if ($addNew) disabled-link @endif" id="v-pills-tab"
             role="tablist"
             aria-orientation="vertical">
            @foreach ($options['sections'] as $key => $section)
                @php $class = ($key == 0) ? 'active show' : ''; @endphp
                <a class="nav-link mb-2 {{ $class }}"
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
            @php
                $totalTab = count($options['sections']);
            @endphp
            @foreach ($options['sections'] as $key => $section)
                @php $class = ($key == 0) ? 'active show' : ''; @endphp
                <div class="tab-pane fade {{ $class }}"
                     id="v-pills-{{ $section['id'] }}" role="tabpanel"
                     aria-labelledby="v-pills-{{ $section['id'] }}-tab">
                    <form class="form hh-options-form @if ($key < $totalTab - 1) save-and-next @endif form-translation"
                          action="{{ dashboard_url('post-new-home') }}"
                          method="post" data-tab="#v-pills-{{ $section['id'] }}-tab">
                        <?php
                            if(!isset($section['trans']) || (isset($section['trans']) && $section['trans'] != 'none')){
                                show_lang_section('mb-2');
                            }
                        ?>
                        <input type="hidden" name="postID" value="{{ $newHome }}">
                        <input type="hidden" name="postEncrypt"
                               value="{{ hh_encrypt($newHome) }}">
                        <input type="hidden" name="hh_options_available_fields"
                               value="{{ base64_encode(serialize($availableField)) }}">
                        <input type="hidden" name="action" value="hh_options_save_metabox">
                        <div class="row">
                            @php
                                $currentOptions = [];
                            @endphp
                            @foreach ($options['fields'] as $_key => $field)
                                @if ($field['section'] === $section['id'])
                                    @if ($field['type'] === 'tab')
                                        <div class="col col-12">
                                            <ul class="nav nav-tabs nav-bordered">
                                                @foreach ($field['tab_title'] as $__key => $title)
                                                    @php $_class = ''; @endphp
                                                    @php $class = ($__key == 0) ? 'active show' : ''; @endphp
                                                    <li class="nav-item">
                                                        <a href="#{{ $title['id'] }}"
                                                           data-toggle="tab"
                                                           aria-expanded="false"
                                                           class="nav-link {{ $_class }}">
                                                            {{ $title['label'] }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <div class="tab-content">
                                                @foreach ($field['tab_title'] as $__key => $title)
                                                    @php $class = ($__key == 0) ? 'active show' : ''; @endphp
                                                    <div class="tab-pane {{ $_class }}"
                                                         id="{{ $title['id'] }}">
                                                        <div class="row">
                                                            @foreach ($field['tab_content'] as $___key => $content)
                                                                @if ($content['section'] == $title['id'])
                                                                    @php
                                                                        $content = \ThemeOptions::mergeField($content);
                                                                        $content['post_id'] = $newHome;
                                                                        $currentOptions[] = $field;
                                                                        $content = \ThemeOptions::applyData($newHome, $content, $metaValue);
                                                                        echo \ThemeOptions::loadField($content);
                                                                    @endphp
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        @php
                                            $field = \ThemeOptions::mergeField($field);
                                            $field['service_id'] = $newHome;
                                            $currentOptions[] = $field;
                                            $field =  \ThemeOptions::applyData($newHome, $field, $metaValue);
                                            echo \ThemeOptions::loadField($field);
                                        @endphp
                                    @endif
                                @endif
                            @endforeach
                            <input type="hidden" name="currentOptions"
                                   value="{{ base64_encode(serialize($currentOptions)) }}">
                        </div>
                        <div class="clearfix mt-3">
                            @if ($key > 0)
                                <div class="button-list">
                                    <button class="btn btn-secondary left waves-effect waves-light btn-prev-tab-option"
                                            data-tab="#v-pills-{{ $section['id'] }}-tab"
                                            type="button">
                                        <span class="btn-label"><i class="icon-arrow-left"></i></span>
                                        {{__('Previous')}}
                                    </button>
                                </div>
                            @endif
                            @if ($key < $totalTab - 1)
                                <input type="hidden" name="step" value="next">
                                @if ($addNew)
                                    <div class="button-list">
                                        <button class="btn btn-success right waves-effect waves-light btn-next-tab-option"
                                                data-tab="#v-pills-{{ $section['id'] }}-tab"
                                                type="button">
                                            {{__('Save & Next')}}
                                            <span class="btn-label-right"><i
                                                        class="icon-arrow-right"></i></span>
                                        </button>
                                    </div>
                                @else
                                    <div class="button-list">
                                        <button class="btn btn-success right waves-effect waves-light btn-current-tab-option"
                                                data-tab="#v-pills-{{ $section['id'] }}-tab"
                                                type="button">
                                            {{__('Save')}}
                                            <span class="btn-label-right"><i
                                                        class="fe-check"></i></span>
                                        </button>
                                    </div>
                                @endif
                            @else
                                <input type="hidden" name="step" value="finish">
                                @if (!empty($redirectTo))
                                    <input type="hidden" name="redirect" value="{{ $redirectTo }}">
                                @endif
                                <div class="button-list">
                                    <button class="btn btn-success right waves-effect waves-light"
                                            type="submit">
                                        {{__('Save & Finish')}}
                                        <span class="btn-label-right"><i class="mdi mdi-check-all"></i></span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</div>
