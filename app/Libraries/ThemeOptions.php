<?php

use App\Option;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use App\Home;
use App\Page;
use App\Post;

class ThemeOptions
{
    private static $optionID = 'hh_theme_options';
    private static $fieldTranslate = ['text', 'textarea', 'upload', 'editor'];

    public static function getOptionID()
    {
        return self::$optionID;
    }

    public static function getListItem($request)
    {
        $items = Input::get('items', '');
        $id = Input::get('id', '');
        $html = '';
        if ($items && $id) {
            $items = maybe_unserialize(base64_decode($items));
            $defaultField = self::getDefaultField();
            $html = '<li class="hh-list-item"><span class="hh-list-item-heading"><span class="htext"></span><a href="javascript: void(0)" class="edit"><i class="ti-minus"></i></a><a href="javascript: void(0)" class="close"><i class="ti-close"></i></a></span><div class="render">';
            $unique = time() . rand(0, 9999);
            foreach ($items as $item) {
                $item['unique'] = $unique;
                $item['id'] = $id . '[' . $item['id'] . '][]' . $unique;
                $item = wp_parse_args($item,$defaultField);
                $html .= self::loadField($item, true);
            }
            $html .= '</div></li>';
        }
        return response()->json([
            'status' => 1,
            'html' => $html
        ]);
    }

    public static function getGoogleFonts()
    {
        $fonts = [];
        $key = 'AIzaSyBZ2UQiEZwRnTT1Pq49TNo19L2Toy0NlJA';
        $url = "https://www.googleapis.com/webfonts/v1/webfonts?key={$key}";
        $responsive = file_get_contents($url);
        if ($responsive && !empty($responsive)) {
            $fontsData = json_decode($responsive);
            if (!empty($fontsData) && is_object($fontsData)) {
                foreach ($fontsData->items as $font) {
                    $fonts[Str::slug($font->family)] = [
                        'name' => $font->family,
                        'variants' => $font->variants,
                        'subsets' => $font->subsets
                    ];
                }
            }
        }
        return $fonts;
    }

    public static function url($url = '')
    {
        return url(Config::get('awebooking.prefix_dashboard') . '/' . $url);
    }

    public static function applyData($serviceID, $field, $meta)
    {
        $field['post_id'] = $serviceID;
        if (is_object($meta)) {
            $meta = (array)$meta;
        }
        $field['value'] = (isset($meta[$field['id']])) ? maybe_unserialize($meta[$field['id']]) : '';
        if ($field['type'] == 'location') {
            $prefix = $field['id'] . '_';
            $field['value'] = [
                'address' => $meta[$prefix . 'address'],
                'city' => $meta[$prefix . 'city'],
                'state' => $meta[$prefix . 'state'],
                'postcode' => $meta[$prefix . 'postcode'],
                'country' => $meta[$prefix . 'country'],
                'lat' => $meta[$prefix . 'lat'],
                'lng' => $meta[$prefix . 'lng'],
                'zoom' => $meta[$prefix . 'zoom']
            ];
        }
        return $field;
    }

    public static function getServiceMeta($serviceID, $post_type = 'home')
    {
        $model = null;
        switch ($post_type) {
            case 'page':
                $model = new Page();
                break;
            case 'post':
                $model = new Post();
                break;
            default:
                $model = new Home();
                break;
        }

        $serviceObject = $model->getById($serviceID);

        if (!empty($serviceObject) && is_object($serviceObject)) {
            return $serviceObject;
        }
        return null;
    }

    public static function renderOption()
    {
        return view('options.option');
    }

    public static function renderMeta($key = '', $newHome = null, $addNew = true, $redirectTo = '')
    {
        return view('options.meta', ['newHome' => $newHome, 'key' => $key, 'addNew' => $addNew, 'redirectTo' => $redirectTo]);
    }

    public static function renderPageMeta($key = '', $newPage = null, $addNew = true, $redirectTo = '', $service = 'page')
    {
        return view('options.page-meta', ['newPage' => $newPage, 'key' => $key, 'addNew' => $addNew, 'redirectTo' => $redirectTo, 'service' => $service]);
    }

    public static function loadField($field, $return = false)
    {
        if (View::exists('options.fields.' . $field['type'])) {
            return view('options.fields.' . $field['type'], $field)->render();
        }
        return '';
    }

    public static function fetchField($field)
    {
        if ($field['field_type'] == 'meta') {
            if ($field['type'] == 'list_item') {
	            $value = Input::get( $field['id'], '' );
                $return = [];

                if (!empty($value) && is_array($value)) {
                    $tmp_arr = $value;
                    $tmp_arr = array_shift($tmp_arr);

                    if (!empty($tmp_arr) && is_array($tmp_arr)) {
                    	$count_item = count($tmp_arr);
	                    $langs = get_languages();
	                    $count_lang = count($langs);

	                    if(isset($field['trans']) && $field['trans'] == 'yes' && $count_lang > 0){
		                    $count_item = $count_item / $count_lang;
	                    }

	                    for($i = 0; $i < $count_item; $i++){
		                    $item = [];
		                    foreach ($field['items'] as $key => $val){
								if(isset($val['trans']) && $val['trans'] == 'yes' && $count_lang > 0){
									$trans_val = '';
									foreach($langs as $klang => $vlang){
										$val_temp = (isset($value[$val['id']]) && isset($value[$val['id']][(count($langs) * $i + $klang)])) ? $value[$val['id']][(count($langs) * $i + $klang)] : '';
										$trans_val .= '[:'. $vlang .']' . $val_temp;
									}
									$trans_val .= '[:]';
									$item[$val['id']] = $trans_val;
								}else{
									$item[$val['id']] = (isset($value[$val['id']]) && isset($value[$val['id']][$i])) ? $value[$val['id']][$i] : '';
								}
		                    }
		                    $return[] = $item;
	                    }
                    }
                }
                return serialize($return);
            } elseif ($field['type'] == 'checkbox' || $field['type'] == 'select2_multiple') {
                $value = (array)Input::get($field['id'], '');
                $value = implode(',', $value);
                return $value;
            } elseif ($field['type'] == 'google_font') {
                $font = Input::get($field['id'], '');
                $font_variants = Input::get('font_variants', '');
                $font_subsets = Input::get('font_subsets', '');
                if (!empty($font)) {
                    $value = $font;
                    if (!empty($font_variants)) {
                        $value .= ';' . implode(',', $font_variants);
                    } else {
                        $value .= ';';
                    }
                    if (!empty($font_subsets)) {
                        $value .= ';' . implode(',', $font_subsets);
                    } else {
                        $value .= ';';
                    }
                } else {
                    $value = ';;';
                }
                return $value;
            } elseif ($field['type'] == 'on_off') {
                return Input::get($field['id'], 'off');
            } elseif ($field['type'] == 'permalink') {
                return Str::slug(Input::get($field['id'], ''));
            } else {
	            if(isset($field['trans']) && $field['trans'] == 'yes' && in_array($field['type'], self::$fieldTranslate)) {
		            $value = set_translate( $field['id'] );
	            }else{
		            $value = Input::get( $field['id'], '' );
	            }
                return $value;
            }
        } else {
            if ($field['type'] == 'location') {
                $return = [];
                $value = Input::get( $field['id'], '' );
                if(!empty($value['address']) && is_array($value['address'])){
                    $return['postcode'] = $value['postcode'];
                    $return['lat'] = $value['lat'];
                    $return['lng'] = $value['lng'];
                    $return['zoom'] = $value['zoom'];

                    $need_filter = ['address', 'city', 'state', 'country'];
                    foreach ($need_filter as $item) {
                        $val_temp = '';
                        foreach ($value[$item] as $key => $val) {
                            $val_temp .= '[:' . $key . ']' . $val;
                        }
                        $val_temp .= '[:]';
                        $return[$item] = $val_temp;
                    }
                    return $return;
                }else{
                    return $value;
                }
            }else {
                if (isset($field['trans']) && $field['trans'] == 'yes' && in_array($field['type'], self::$fieldTranslate)) {
                    $value = set_translate($field['id']);
                } else {
                    $value = Input::get($field['id'], '');
                }
            }
	        return $value;
        }
    }

    public static function getOption($key, $default = '', $in_setting = false)
    {
        $option = new Option();
        $options = $option->getOption(self::getOptionID());
        $current_options = [];

        if (!empty($options)) {
            $current_options = unserialize($options->option_value);
        }

        if(isset($current_options[$key])){
        	$value = maybe_unserialize($current_options[$key]);
	        if(is_string($value) && !$in_setting){
		        return get_translate($value);
	        }else{
				return $value;
	        }
        }
        return $default;
    }

    public static function filterFields($options)
    {
        $return = [];
        foreach ($options['fields'] as $field) {
            if ($field['type'] == 'tab') {
                foreach ($field['tab_content'] as $content) {
                    $return[] = $content;
                }
            } else {
                $return[] = $field;
            }
        }

        return $return;
    }

    public static function mergeField($field)
    {
        $default = self::getDefaultField();
        foreach ($default as $key => $value) {
            if (isset($field[$key]) && !empty($field[$key])) {
                $default[$key] = $field[$key];
            }
        }

        return $default;
    }

    public static function getDefaultField()
    {
        return [
            'id' => '',
            'type' => '',
            'label' => '',
            'desc' => '',
            'std' => '',
            'value' => '',
            'choices' => [], // array, taxonomy:{taxonomy_name}, page, number_range:0:100
            'items' => [], //only for list item
            'binded_from' => '',//only for list item
            'field_type' => 'meta', //taxonomy
            'layout' => 'col-12',
            'style' => '', // 'wide', 'form-check-inline', 'col' -  used for select, checkbox, radio
            'tab_title' => [],
            'tab_content' => [],
            'post_id' => '',
            'break' => false,
            'maxlength' => false, /* 'maxlength' => [ 'max-length' => 20, 'always-show' => true, 'threshold' => 10 ],  */
            'validation' => '', // bootstrap validation js: min, max, required, email, matches, ...
            'condition' => '',
            'minlength' => 0,
            'min_date' => date('Y-m-d'), // or '-1' if not set. Used for datapicker
            'operation' => 'and',
            'unique' => '',
            'escape' => false,
            'section' => '',
            'post_type' => '',
            'excluded' => false,
            'enqueue_scripts' => [], // used for list_item
            'enqueue_styles' => [], // used for list_item,
            'permission' => '',// ['administrator', 'partner']
            'trans' => 'yes'
        ];
    }

    public static function get_inst()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }

}
