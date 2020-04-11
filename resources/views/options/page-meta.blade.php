@php
    if (is_null($newPage) || empty($newPage)) {
    return;
    }
    $allMetaValue = \ThemeOptions::getServiceMeta($newPage, $service);
    $options = Config::get('awebooking.'. $key);
@endphp

<div id="hh-options-wrapper" class="hh-options-wrapper ">
    <div class="hh-options-tab-content">
        <div class="tab-content">
            <div>
                <div class="row">
                    @foreach ($options['fields'] as $_key => $field)
                        @php
                            $field = \ThemeOptions::mergeField($field);
                            $field['service_id'] = $newPage;
                            if (!$addNew) {
                                $field =  \ThemeOptions::applyData($newPage, $field, $allMetaValue);
                            }
                            if($field['id'] == 'post_category'){
                                $categories = get_category($newPage);
                                $res_c = [];
                                if(!empty($categories)){
                                    foreach ($categories as $k_c => $v_c){
                                        array_push($res_c, $v_c->term_id);
                                    }
                                }
                                $field['value'] = implode(',', $res_c);
                            }elseif($field['id'] == 'post_tag'){
                                $tags = get_tag($newPage);

                                $res_c = [];
                                if(!empty($tags)){
                                    foreach ($tags as $k_t => $v_t){
                                        array_push($res_c, get_translate($v_t->term_title));
                                    }
                                }
                                $field['value'] = implode(', ', $res_c);

                            }
                            echo \ThemeOptions::loadField($field);
                        @endphp
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
