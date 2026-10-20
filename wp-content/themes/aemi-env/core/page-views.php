<?php

class AemiPagePostsViews{
  
  private $page_views_enabled_in = array( 'post' );
  private static $post_meta_name = '_aemi_total_page_views';
  private static $aemi_cookie_name = 'pages_viewed';
  private static $user_meta_name = '_aemi_pages_viewed';

  public function __construct() {
    //auto detect/update page views of posts
    add_action('get_header', array( $this, 'detect_and_auto_update_total_views_of_page'));
    
    //register in admin custom metabox and custom columns to show total page views
    if( is_admin() ){

      add_action( 'add_meta_boxes', array( $this, 'register_meta_boxs_total_page_views' ) );

      foreach( $this->page_views_enabled_in as $post_type ){
        add_filter('manage_' . $post_type . '_posts_columns', array($this, 'custom_column_in_posts_admin_th' ) );
        add_action('manage_' . $post_type . '_posts_custom_column', array( $this, 'custom_column_views_value' ), 20, 2);
      }
    }
  }


  /**
   * ==========================================================================
   *                        ADMIN 
   * ==========================================================================
   */

   /**
    * Register custom metaboxes to show in post total page views
    */
  public function register_meta_boxs_total_page_views(){
    foreach ( $this->page_views_enabled_in as $post_type_enabled ) {
      add_meta_box(
          'aemi_page_total_views',                
          __('Views', AEMI_TEXT_DOMAIN),      
          array( $this, 'meta_box_preview' ),
          $post_type_enabled,
          'side'
      );
    }
  }

  /**
   * Render html preview of metabox
   */
  public function meta_box_preview( $post ){
    echo '<br />';
    echo '<strong>' . __('Total views', AEMI_TEXT_DOMAIN) . ':</strong> ' .AemiPagePostsViews::get_total_page_view( $post->ID );
  }

  /**
   * Insert Head in table
   * 
   * @param String[] $columns - Columns registered
   * 
   * @return String[]
   */
  public function custom_column_in_posts_admin_th($columns) {
    return array_merge($columns, [self::$post_meta_name => __('Views', AEMI_TEXT_DOMAIN)]);
  }

  /**
   * Print value in column views
   * 
   * @param String $column_name - Registered column name 
   * @param Int $postID - ID of post
   */
  public function custom_column_views_value( $column_name, $postID ){
    if( $column_name == self::$post_meta_name ){
      echo AemiPagePostsViews::get_total_page_view( $postID );
    }
  }

  /**
   * ==========================================================================
   *                        FRONTEND DETECT
   * ==========================================================================
   */

  /**
   * This code control and detect current page use and nedd total views
   */
  public function detect_and_auto_update_total_views_of_page(){
    if( is_admin() ) return;
    if( !is_user_logged_in() ) return;

    if( is_single() || is_page() ){
      global $post;
  
      if( $post ){
        $postToArray = ((array)$post);
  
        if( isset($postToArray['post_type']) && in_array( $postToArray['post_type'], $this->page_views_enabled_in ) ){
          $currentPostID = $postToArray['ID'];

          //case not viewed page, update page view
          if( !self::check_current_page_viewed_cookie( $currentPostID ) ){
            //set viewed page
            self::update_page_view( $currentPostID );
          }

        }
      }
    }
  }

  /**
   * ==========================================================================
   *                      GENERAL FUNCTIONS
   * ==========================================================================
   */

  /**
   * Return total page views
   * 
   * @param Int $postID - Post ID
   * @return Int - Total of page views
   */
  static function get_total_page_view($postID){
    $totalViews = get_post_meta( $postID, self::$post_meta_name, true );
    $totalViews = ( $totalViews > 0 ? $totalViews : 0 );

    return $totalViews;
  }

  /**
   * This code auto count and update total views of pages
   * 
   * @param Int $postID - Post ID
   * @param Int $userID - user ID (not required)
   */
  public static function update_page_view( $postID, $userID = false ){
    $totalViews = AemiPagePostsViews::get_total_page_view( $postID ) + 1;

    //get cookies page viewed
    $pagesViewed = self::get_cookies_pages_viewed();

    //verify post exist in page viewed
    if( !in_array( $postID, array( $pagesViewed ) ) ){
      $pagesViewed[$postID] = $postID;
    }

    //preper data to save in $_COOKIE
    $pagesViewed = implode( ',', $pagesViewed );
    setcookie(self::$aemi_cookie_name, $pagesViewed, time()+3600);

    //update in database total page viewed
    update_post_meta( $postID, self::$post_meta_name, $totalViews );

    //set user viewed
    self::set_user_page_viewed( $userID, $postID );
  }

  /**
   * GET COOKIE PAGES VIEWED
   * 
   * @param Int $userID
   * 
   * @return Array
   */
  public static function get_cookies_pages_viewed( $userID = false ){
    if( isset( $_COOKIE ) && isset( $_COOKIE[self::$aemi_cookie_name] ) ){
      $tempValue = explode(',', $_COOKIE[self::$aemi_cookie_name]);
      return $tempValue;
    }else{
      
      //case not informed $userID get current user logged user id
      // if( is_user_logged_in() &&  $userID == false ){
      //   $userID = get_current_user_id();
      // }
      
      //case exist user loggeged user
      // if( $userID ){
      //   return AemiPagePostsViews::get_user_pages_viewed();
      // }
    }
    return array();
  }

  /**
   * Return pages viewed by user
   * 
   * @param Int $userID
   * 
   * @return Array - List os pages user viewed
   */
  public static function get_user_pages_viewed( $userID = false ){

    //case not exist get currente user id
    if( !$userID && is_user_logged_in() ){
      $userID = get_current_user_id();
    }

    //case exist id user/current user return array
    if( $userID ){
      $pages_viewed = get_user_meta( $userID, self::$user_meta_name, true );
      return ( $pages_viewed ? $pages_viewed : array() );
    }

    return array();
  }

  /**
   * Save in database user viewed specific post 
   * 
   * @param Int $userID - ID of user
   * @param Int $postID - ID of posts
   * 
   * @return Boolean
   */
  public static function set_user_page_viewed( $userID = false, $postID = false ){
    if( $postID && is_int( $postID ) ){
      if( get_post( $postID ) ){

        //case not set specific user id, get current user id logged
        if( !$userID && is_user_logged_in() ){
          $userID = get_current_user_id();
        }

        //verify has user ID
        if( $userID ){
          $getPagesUserViewed = AemiPagePostsViews::get_user_pages_viewed();

          //verify return is array
          if( is_array( $getPagesUserViewed ) ){
            $getPagesUserViewed[$postID] = $postID;
            
            return update_user_meta( $userID, self::$user_meta_name, $getPagesUserViewed);
          }
        }
      }
    }

    return false;
  } 

  /**
   * This method will check if the user has previously viewed the post
   * 
   * @param Int $postID - Post ID
   * @return Boolean
   */
  public static function check_current_page_viewed_cookie( $postID ){
    return in_array( $postID, self::get_cookies_pages_viewed() );
  }
}

new AemiPagePostsViews();


/**
 * ================================================================
 *                      HELP FUNCTIONS
 * ================================================================
 */

if( !function_exists( 'verify_user_viewed_post' ) ){
  /**
   * Check user viewed specific post ID
   * 
   * @param Int $postID - ID of post
   * @param Int $userID - ID of user, is not required, default get current user ID
   * 
   * @return Boolean
   */
  function verify_user_viewed_post( $postID, $userID = false ){
    $postsIDsViewed = AemiPagePostsViews::get_user_pages_viewed( $userID );
    return in_array( $postID, $postsIDsViewed );
  }
}

if( !function_exists( 'get_total_page_view' ) ){
  /**
   * Return total of page views of page
   * 
   * @param Int $postID - ID of post
   * @return Int
   */
  function get_total_page_view( $postID ){
    return AemiPagePostsViews::get_total_page_view( $postID );
  }
}