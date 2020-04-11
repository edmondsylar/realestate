<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;

class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    	if(is_multi_language()) {
		    $lang = Input::get( 'lang', '' );

		    if ( ! session_id() ) {
			    session_start();
		    }
		    if ( ! empty( $lang ) ) {
			    $langs = get_languages();
			    if ( ! empty( $langs ) ) {
				    if ( in_array( $lang, $langs ) ) {
					    $_SESSION['hh_language'] = $lang;
				    }
			    }
		    } else {
			    $currentSectionLang = isset( $_SESSION['hh_language'] ) ? $_SESSION['hh_language'] : '';
			    $langs              = get_languages();
			    if ( empty( $currentSectionLang ) ) {
				    if ( ! empty( $langs ) ) {
					    $_SESSION['hh_language'] = $langs[0];
				    }
			    } else {
				    if ( ! empty( $langs ) && ! in_array( $currentSectionLang, $langs ) ) {
					    if ( isset( $_SESSION['hh_language'] ) ) {
						    unset( $_SESSION['hh_language'] );
					    }
				    }
			    }
		    }

		    if ( isset( $_SESSION['hh_language'] ) ) {
			    $language = $_SESSION['hh_language'];
		    } else {
			    $lang_option = get_option( 'site_language', '' );
			    if ( empty( $lang_option ) ) {
				    $lang_option = config( 'app.locale' );
			    }
			    $language = $lang_option;
		    }

	    }else{
		    $lang_option = get_option( 'site_language', '' );
		    if ( empty( $lang_option ) ) {
			    $lang_option = config( 'app.locale' );
		    }
		    $language = $lang_option;
	    }

	    app()->setLocale($language);

        return $next($request);
    }
}
