<?php
if( !function_exists('get_aemi_lang_directory') ){
  /**
   * Return path of files languages  
   */
  function get_aemi_lang_directory(){
    return get_template_directory() . '/languages';
  }
}

if( !function_exists('get_aemi_url_assets') ){
  /**
   * Return url assets files, optional return URL file set in parameter
   * 
   * @param $file
   * @return String
   */
  function get_aemi_url_assets( $file = '' ){
    return get_stylesheet_directory_uri() . '/assets/' . ( $file != '' ? $file : '' );
  }
}

if( !function_exists('get_aemi_url_assets_css') ){
  /**
   * Return url assets css files, optional return URL file set in parameter
   * 
   * @param $file
   * @return String
   */
  function get_aemi_url_assets_css( $file = '' ){
    return get_aemi_url_assets() . 'css/' . ( $file != '' ? $file : '' );
  }
}

if( !function_exists('get_aemi_url_assets_js') ){
  /**
   * Return url assets js files, optional return URL file set in parameter
   * 
   * @param $file
   * @return String
   */
  function get_aemi_url_assets_js( $file = '' ){
    return get_aemi_url_assets() . 'js/' . ( $file != '' ? $file : '' );
  }
}

if( !function_exists('get_aemi_url_assets_third_party_plugins') ){
  /**
   * Return url assets js files, optional return URL file set in parameter
   * 
   * @param $file
   * @return String
   */
  function get_aemi_url_assets_third_party_plugins( $file = '' ){
    return get_aemi_url_assets() . 'third-party-plugins/' . ( $file != '' ? $file : '' );
  }
}

if( !function_exists('get_aemi_url_assets_imgs') ){
  /**
   * Return url assets imgs files, optional return URL file set in parameter
   * 
   * @param $file
   * @return String
   */
  function get_aemi_url_assets_imgs( $file = '' ){
    return get_aemi_url_assets() . 'images/' . ( $file != '' ? $file : '' );
  }
}

if( !function_exists('get_aemi_current_user_name') ){
  /**
   * Return current user name
   * 
   * @return String
   */
  function get_aemi_current_user_name(){
    if( is_user_logged_in() ){
      $user_id = get_current_user_id();
      $first_name = get_user_meta( $user_id, 'first_name', true );
      $last_name = get_user_meta( $user_id, 'last_name', true );

      $current_user_name = sanitize_text_field( htmlentities( $first_name . ( $first_name ? ' ' : '' ) . $last_name ) );
      if( !$current_user_name ){
        global $current_user;
        $current_user_info = wp_get_current_user();
        $current_user_name = sanitize_text_field( htmlentities( $current_user_info->display_name ) );
      }

      return $current_user_name;
    }

    return '';
  }
}

if( !function_exists('get_aemi_my_account_url') ){
  /**
   * Retunr url my account url
   * 
   * @return String
   */
  function get_aemi_my_account_url(){
    return get_permalink( get_option('woocommerce_myaccount_page_id') );
  }
}

if( !function_exists('get_aemi_register_page_url') ){
  /**
   * Retunr url my account url
   * 
   * @return String
   */
  function get_aemi_register_page_url(){
    $url = '#';

    $pages = get_posts(array(
        'post_type'  => 'page',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'template-page-register.php'
    ));

    if(isset($pages[0])) {
        $url = get_page_link($pages[0]->ID);
    }

    return $url;
  }
}

if( !function_exists('estimateReadingTime') ){
  /**
   * Code link https://ourcodeworld.com/articles/read/1603/how-to-determine-the-estimated-reading-time-of-a-text-with-php
   * @author Carlos Delgado
   * 
   * Function to calculate the estimated reading time of the given text.
   * 
   * @param string $text The text to calculate the reading time for.
   * @param string $wpm The rate of words per minute to use.
   * @return Array
   */
  function estimateReadingTime($text, $wpm = 200) {
    $totalWords = str_word_count(strip_tags($text));
    $minutes = floor($totalWords / $wpm);
    $seconds = floor($totalWords % $wpm / ($wpm / 60));
    
    return array(
        'minutes' => $minutes,
        'seconds' => $seconds
    );
  }
}

if( !function_exists('get_aemi_title_page') ){
  function get_aemi_title_page(){
    $title = '';

    if( is_single() || is_page() ){
      $title = get_the_title();
    } else if ( is_favorite_page() ){
      $title = __( 'Favorites', AEMI_TEXT_DOMAIN );
    } else if ( is_search() ){
      $title = __( 'Search results', AEMI_TEXT_DOMAIN );
    } else if ( is_archive() ){
      $title = get_the_archive_title();
    }

    return $title;
  }
}

if( !function_exists('get_aemi_content_types_post') ){
  /**
   * Return list / links taxonomies related a posts
   */
  function get_aemi_content_types_post( $postID, $taxonomy = 'category', $returnLinks = true ){
    $categories = get_the_terms( $postID, $taxonomy);
    $category_list = '';
    if( !is_wp_error( $categories ) && $categories){
      foreach( $categories as $category ){
        $category_list .= ( $category_list ? ', ' : '' );
        $category_list .= ( $returnLinks ? '<a href="' . get_term_link( $category->term_id, $category->taxonomy ) . '" title="' . __( 'Go to ', AEMI_TEXT_DOMAIN ) . ' ' . $category->name . '">' : '' ) . $category->name . ( $returnLinks ? '</a>' : '' );
      }
    }
    return $category_list;
  }
}

if( !function_exists('get_aemi_text_excerpt') ){
  /**
   * This function will crop a text or Post object. 
   * It cuts the text based on the boundary, but without cutting the last word 
   * if the boundary is on top of some word.
   * 
   * @param WP_Post|Object|Array|String $content - Content contais post content
   * @param Int $limit - Set limite to crop
   * @param String $endPoints - endpoint to use
   */
  function get_aemi_text_excerpt( $content = false, $limit = 80, $endPoints = '...' ){
    $text = '';
    
    if( $content ){

      /**
       * Verify content type String, Object ou WP_Post
       */
      if( is_object( $content ) || is_array( $content ) ){
        $content = (array) $content;
        if( isset( $content['post_excerpt'] ) && trim( $content['post_excerpt'] ) != '' ){
          $text = $content['post_excerpt'];
        } else if(  isset( $content['post_content'] ) && $content['post_content']  ){
          $text = $content['post_content'];
        }
      } else if ( is_string( $content ) ){
        $text = $content;
      }

      if( $text && trim( $text ) ){
        //Clear space and tags html
        $text = trim( strtr( strip_tags( $text ), array( "\n" => '', "\r" => '' ) ) );

        //Verify min text to crop
        if( strlen( $text ) > $limit ){
          $matches = array();
  
          preg_match('/([\s\S+]{' . $limit . '}\w+|.*)/', $text, $matches);
  
          if( isset( $matches[0] ) ){
            return substr( $matches[0], 0, $limit ) . $endPoints;
          } else {
            $text = '';
          }
        }
      }
    }

    return $text;
  }
}

if( !function_exists( 'get_aemi_wp_get_available_translations' ) ){
  /**
   * This code is based on the code wp_dropdown_languages() with the only difference that instead of returning a 
   * SELECT it returns an array with the translations enabled
   * 
   * @path location wp-includes/l10n.php 
   * 
   * @param Array
   */
  function get_aemi_wp_get_available_translations( $args = array() ){
    $parsed_args = wp_parse_args(
      $args,
      array(
        'id'                          => 'locale',
        'name'                        => 'locale',
        'languages'                   => get_available_languages(),
        'translations'                => array(),
        'selected'                    => '',
        'echo'                        => 1,
        'show_available_translations' => true,
        'show_option_site_default'    => false,
        'show_option_en_us'           => true,
        'explicit_option_en_us'       => true,
      )
    );

    $translations = $parsed_args['translations'];
    if ( empty( $translations ) ) {

      //prevent re-include functions to translate
      if( !function_exists('wp_get_available_translations') ){
        require_once ABSPATH . 'wp-admin/includes/translation-install.php';
      }
      
      $translations = wp_get_available_translations();
    }
    
    if ( $parsed_args['show_option_en_us'] ) {
      $parsed_args['languages'][] = ( $parsed_args['explicit_option_en_us'] ) ? 'en_US' : '';
      if ( !isset( $translations['en_US'] ) ){
        $translations['en_US'] = array(
          'language' => 'en_US',
          'english_name' => 'English (United States)',
          'native_name' => 'English (United States)',
          'iso' => array(
            'en'
          )
        );
      }     
    }

    $languages = array();
    foreach ( $parsed_args['languages'] as $locale ) {

      if ( isset( $translations[ $locale ] ) ) {
        $translation = $translations[ $locale ];
        $languages[] = array(
          'language'    => $translation['language'],
          'native_name' => $translation['native_name'],
          'lang'        => current( $translation['iso'] ),
        );

        // Remove installed language from available translations.
        unset( $translations[ $locale ] );
      } else {
        $languages[] = array(
          'language'    => $locale,
          'native_name' => $locale,
          'lang'        => '',
        );
      }
    }

    return $languages;
  }
}

if( !function_exists('wporg_recursive_sanitize_text_field') ){
  /***
   * To ensure arrays are properly sanitized to WordPress Codex standards,
   * they encourage usage of sanitize_text_field(). That only works with a single
   * variable (string). This function allows for a full blown array to get sanitized
   * properly, while sanitizing each individual value in a key -> value pair.
   *
   * Source: https://wordpress.stackexchange.com/questions/24736/wordpress-sanitize-array
   * Author: Broshi, answered Feb 5 '17 at 9:14
   */
  function wporg_recursive_sanitize_text_field( $array, $stripslashes = false ) {
    foreach ( $array as $key => &$value ) {
        if ( is_array( $value ) ) {
            $value = wporg_recursive_sanitize_text_field( $value, $stripslashes );
        } else {
            $value = sanitize_textarea_field( $value );
            if( $stripslashes ){
              $value = stripslashes( $value );
            }
        }
    }
    return $array;
  }
}

if( !class_exists('get_interest_list_for_users') ){
  function get_interest_list_for_users( $custom_args = array(), $custom_retuns = array() ){
    $list = array();

    $args = array_merge( array(
      'taxonomy' => 'aemi_tags',
      'hide_empty' => false,
      'numberint' => 0,
      'meta_query' => array(
        'relation' => 'OR',
        array(
           'key'       => '__common_tag_for_all_users',
           'value'     => true,
           'compare'   => '!='
        ),
        array(
           'key'       => '__common_tag_for_all_users',
           'value'     => '',
           'compare'   => 'NOT EXISTS'
        )
      )
    ), $custom_args );

    $get_categories = get_categories( $args );

    if( $get_categories ){
      foreach ($get_categories as $key => $value) {

        $args_temp = array(
          'id' => $value->term_id,
          'title' => $value->name
        );

        if( $custom_retuns ){
          foreach ( $custom_retuns as $custom_arg ) {
            $args_temp[$custom_arg] = $value->{$custom_arg};
          }
        }

        $list[] = $args_temp;
      }
    }

    return $list;
  }
}

function aemi_get_post_view_user($post_id)
{
    if (is_user_logged_in()) {
        $posts = get_user_meta( get_current_user_id(), '_aemi_pages_viewed' );

        if (in_array($post_id, $posts[0])) {
            return true;
        }

        return false;
    }
}

function aemi_get_level_related_to_post_id( $postID = FALSE, $return_meta_datas = true ){
  $returnData = array();
  $aemi_level_by_post = get_the_terms( $postID, 'aemi_level' );
  $points_tax = get_post_meta( $postID, '__aemi_level_post_score', true );

  if( $aemi_level_by_post ){
    foreach( $aemi_level_by_post as $element ){
      if( $element->parent == 0 ){
        $element->sub_categories = array();

        foreach( $aemi_level_by_post as $subelement ){
          if( $subelement->parent == $element->term_id ){

            if( $return_meta_datas ){
              $meta = get_term_meta( $subelement->term_id );
    
              if( $meta ){
                $subelement->meta = $meta;
              }
            }

            $subelement->level_points = ( $points_tax && isset( $points_tax[$subelement->term_id] ) ? $points_tax[$subelement->term_id] : 0 );
        
            $element->sub_categories[] = $subelement;
          }
        }

        if( $return_meta_datas ){
          $meta = get_term_meta( $element->term_id );

          if( $meta ){
            $element->meta = $meta;
          }
        }

        $element->level_points = ( $points_tax && isset( $points_tax[$element->term_id] ) ? $points_tax[$element->term_id] : 0 );

        $returnData[] = $element;
      }
    }
  }

  return $returnData;

}

if( !function_exists( 'get_replace_month_by_current_lang' ) ){
  function get_replace_month_by_current_lang( $string ){
    return strtr( $string, array(
      'January' => __( 'January' ),
      'February' => __( 'February' ),
      'March' => __( 'March' ),
      'April' => __( 'April' ),
      'May' => __( 'May' ),
      'June' => __( 'June' ),
      'July' => __( 'July' ),
      'August' => __( 'August' ),
      'September' => __( 'September' ),
      'October' => __( 'October' ),
      'November' => __( 'November' ),
      'December' => __( 'December' ),
      'january' => __( 'january' ),
      'february' => __( 'february' ),
      'march' => __( 'march' ),
      'april' => __( 'april' ),
      'may' => __( 'may' ),
      'june' => __( 'june' ),
      'july' => __( 'july' ),
      'august' => __( 'august' ),
      'september' => __( 'september' ),
      'october' => __( 'october' ),
      'november' => __( 'november' ),
      'december' => __( 'december' ),
    ) );
  }
}