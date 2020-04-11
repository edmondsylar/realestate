<?php
/**
 * Created by PhpStorm.
 * User: HanhDo
 * Date: 2/5/2020
 * Time: 5:02 PM
 */
use App\Language;
use Illuminate\Support\Facades\Input;
use  Illuminate\Support\Facades\Session;

function get_current_language(){
	return app()->getLocale();
}

function show_lang_section($class = ''){
    $langs = get_languages(true);
    if(count($langs) > 0) {
        ?>
        <div id="hh-language-action" class="hh-language-action <?php echo esc_attr($class) ?>">
            <ul>
                <?php foreach ($langs as $k => $lang) { ?>
                    <li>
                        <a href="javascript:void(0);" class="item <?php echo esc_attr($k == 0 ? 'active' : '') ?>"
                           data-code="<?php echo esc_attr($lang['code']); ?>">
                            <img
                                src="<?php echo esc_attr(asset('vendors/countries/flag/32x32/' . $lang['flag_code'] . '.png')) ?>"/>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <?php
    }
}
function lang_render_slug($data, $title, $slug, $field_slug){
	$langs = get_languages();
	if(is_multi_language() && !empty($langs)){
		$slug_temp = '';
		foreach ($langs as $key => $code){
			$slug_temp_input = Input::post($field_slug . '_' .$code );
			if(empty($slug_temp_input)){
				$slug_temp_input = get_translate($slug, $code);
			}
			$slug_temp_input = Str::slug($slug_temp_input, '-');
			if ($slug_temp_input != get_translate($slug, $code)) {
				$slug_temp .= '[:' . $code . ']' . render_service_slug($title, $slug_temp_input);
			}else{
				$slug_temp .= '[:' . $code . ']' . get_translate($slug, $code);
			}
		}
		$slug_temp .= '[:]';
		$data[$field_slug] = $slug_temp;
	}else{
		$post_slug = Input::get($field_slug);
		if (empty($post_slug)) {
			$post_slug = $slug;
		}
		$post_slug_convert = Str::slug($post_slug, '-');
		if ($post_slug_convert != $slug) {
			$data[$field_slug] = render_service_slug($title, $post_slug);
		}
	}
	return $data;
}

function is_multi_language(){
    $option = get_option('multi_language', 'off');
	if($option == 'on'){
		return true;
	}else{
		return false;
	}
}

function get_lang_class($key, $item){
    $class = [];
    if($key > 0){
        array_push($class, 'hidden');
    }
    if(!empty($item)){
        array_push($class, 'has-translation');
    }
    if(!empty($class)){
        return ' ' . implode(' ', $class);
    }
    return '';
}

function get_lang_attribute($item){
    if(!empty($item)){
        return 'data-lang="'. $item .'"';
    }
    return '';
}

function get_lang_suffix($code){
    if(!empty($code)){
        return '_' . $code;
    }
    return '';
}

function get_languages_field(){
    $langs = get_languages();
    if(count($langs) == 0){
        $langs[] = '';
    }
    return $langs;
}

function get_languages($full = false){
	if(is_multi_language()) {
        $langs = Language::where( 'status', 'on' )->orderBy( 'priority', 'ASC' )->get();
		if ( !$full ) {
			$codes = [];
			if ( !$langs->isEmpty() ) {
				foreach ( $langs as $item ) {
					array_push( $codes, $item->code );
				}
			}
			return $codes;
		}
		return $langs;
	}
	return [];
}

function awe_lang($text){
    return $text;
}

function get_translate( $text, $lang = '', $format = false )
{
    $ori_name = $text;
    if ( gettype( $text ) == 'string' ) {
        if ( empty( $lang ) ) {
            $lang = get_current_language();
        }
        if ( $format ) {
            $text = preg_replace( "/(\<p\>)(\[)(:|:[a-zA-Z_-]*)(\])(\<\/p\>)/", "$2$3$4", $text );
        }
        preg_match_all( "/(?<=\[:" . $lang . "\]).*?([^:\[\]]+)(?=\[:)/s", $text, $text );

        $text = (array)$text;

        if ( !empty( $text ) && isset( $text[ 0 ][ 0 ] ) ) {
            return lang_clean_text( $text[ 0 ][ 0 ] );
        } elseif ( !empty( $ori_name ) ) {
            return lang_clean_text( $ori_name );
        }

        return '';
    } else {
        return trim($text);
    }
}

function lang_clean_text( $text )
{
    $text = preg_replace('/\[:(.*?)\]/', '', $text);
    if ( strtolower( $text ) == 'array' ) {
        $text = '';
    }
    return $text;
}

function set_translate( $field_name = '' )
{
	$text = '';
	if ( is_multi_language() ) {
		$langs = get_languages();
		foreach ( $langs as $key => $code ) {
		    $input_name = $field_name . '_' . $code;
		    if(isset($_POST[$input_name])) {
			    $text .= '[:' . $code . ']' . Input::post( $input_name, '' );
		    }else{
			    $text .= '[:' . $code . ']' . Input::post( $field_name, '' );
            }
		}
		$text .= '[:]';
	} else {
		$text = Input::post( $field_name, '' );
	}

	return $text;
}
