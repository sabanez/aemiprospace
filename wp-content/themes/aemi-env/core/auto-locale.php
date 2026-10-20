<?php 

/**
 * Let's check the user's language and configure the access-based language
 */
class AemiSetLocale
{ 

  public $langSelected = false;

  /**
   * Add action and filters in WP
   */
  public function __construct() {
    if( !is_admin() ){
      //add_filter( 'theme_locale', array( $this, 'lang_settings' ) );
      //add_filter( 'locale', array( $this, 'lang_settings' ) );
     //
      //add_filter('language_attributes', array( $this, 'lang_attributes' ), 9, 2);
    }
  }

  /**
   * Return language by user
   */
  public function getCurrentLanguage($toLowercase = false){
    $currentLang = get_aemi_default_locale();

    if ( $this->langSelected == false ){
      if( !is_user_logged_in() ){
        $current_browser_language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5);
        $current_browser_language = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $tempCurrentLang = $current_browser_language[0];
        $tempValidateFile = strtr( $tempCurrentLang, array('-' => '_') );

        if( $tempCurrentLang ){
          if( file_exists( get_aemi_lang_directory() . '/' . $tempValidateFile . '.mo' ) ){
            $currentLang = $tempValidateFile;
          }
        }
      } else {
        $user = wp_get_current_user();
        global $locale;
  
        if( $user && $user->locale ){
          $currentLang = get_user_locale(); 
        } else if( $locale ){
          $currentLang = $locale; 
        }
      }
    } else {
      $currentLang = $this->langSelected;
    }

    $this->langSelected = $currentLang;

    if( $toLowercase ){
      $currentLang = strtolower( $currentLang );
    }

    return $currentLang;
  }

  /**
   * Modify language
   * 
   * @param String $lang
   */
  public function lang_attributes( $output, $doctype ){
    $lang = strtr($this->getCurrentLanguage(), array('_' => '-'));

    if( $lang ) {
      
      $attributes = array();

      if( 'html' === $doctype ) {

        if ( function_exists( 'is_rtl' ) && is_rtl() ) {
          $attributes[] = 'dir="rtl"';
        }

        $attributes[] = 'lang="' . esc_attr( $lang ) . '"';
      }

      if( $attributes ){
        return implode(' ', $attributes);
      }

    }

    return $output;
  }

  /**
   * Configure language
   */
  public function lang_settings($locale){
    if( $this->getCurrentLanguage() ) {
      return $this->getCurrentLanguage();
    }

    return $locale;
  }
}

$aemiSetLocale = new AemiSetLocale();

/**
 * Return current logged/not logged language
 */
if( !function_exists('get_aemi_current_user_locale') ){
  function get_aemi_current_user_locale(){
    global $aemiSetLocale;
    return $aemiSetLocale->getCurrentLanguage();
  }
}

if( !function_exists('get_aemi_default_locale') ){
  function get_aemi_default_locale(){
    return 'en_US';
  }
}

if( !function_exists('get_aemi_array_value_by_user_locale') ){
  /**
   * Return array value by key is current user locale or default array value case not
   * exist array key by current user 
   * 
   * @param Array $array_to_search - Array to search language value
   * @param String $array_key - String contains key name to findo
   * 
   * @return Array 
   */
  function get_aemi_array_value_by_user_locale( $array_to_search = array(), $array_key = false ){

    //default lang
    $default = get_aemi_default_locale();

    //case not set, use by current user
    if( !$array_key ) $array_key = get_aemi_current_user_locale();

    if( isset( $array_to_search[$array_key] ) ) return $array_to_search[$array_key];
    if( isset( $array_to_search[$default] ) ) return $array_to_search[$default];

    return array();
  }
}