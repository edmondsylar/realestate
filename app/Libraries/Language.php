<?php
use  Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class Language {
	private $sessionLanguage = 'hh_language';

	public function __construct() {
		//$this->_setDefaultLanguage();
		//add_action( 'init', [ $this, '_setDefaultLanguage' ] );
	}

	public function _setDefaultLanguage(){
		$lang = Input::get('lang', '');
		if(!empty($lang)){
			$langs = get_languages();
			if(!empty($langs)){
				if(in_array($lang, $langs)){
					Session::put($this->sessionLanguage, $lang);
				}
			}
		}else{
			$currentSectionLang = Session::get($this->sessionLanguage, '');
			$langs = get_languages();
			if(empty($currentSectionLang)){
				if(!empty($langs)) {
					Session::put( $this->sessionLanguage, $langs[0]['code'] );
				}
			}else{
				if(!empty($langs) && !isset($langs[$currentSectionLang])){
					Session::remove($this->sessionLanguage);
				}
			}
		}
	}
}