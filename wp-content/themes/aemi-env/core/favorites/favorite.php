<?php 
class AEMIFavoritePages {
  
  private static $meta_name = '_aemi_favorite_pages';
  private static $list = false;

  /**
   * Verify sepcific post page
   */
  public static function verify_is_a_favorite_page( $postID, $userID = false, $useCachedValue = true ){
    $userID = ( $userID && is_numeric( $userID ) ? $userID : get_current_user_id() );
    $postID = ( $postID && is_numeric( $postID ) ? $postID : false ); 

    if( $userID && $postID ){
      $pages = [];

      if( $useCachedValue && self::$list != false && is_array( self::$list ) ){
        $pages = self::$list;
      }else{
        $pages = self::get( $userID, $useCachedValue );
      }

      return  ( $pages && is_array( $pages ) && in_array( $postID, $pages ) ? true : false );
    }

    return false;
  }

  /**
   * Return list of favorite pages
   */
  public static function get( $userID, $useCache = true ){
    $getList = [];
    $userID = ( $userID && is_numeric( $userID ) ? $userID : get_current_user_id() );

    if( $userID ){
      if( $useCache && self::$list != false && is_array( self::$list ) ){
        $getList = self::$list;
      }else{
        $getList = get_user_meta( $userID, self::$meta_name, true );
        
        //cache request
        self::$list = $getList;
      }
 
      if( $getList && is_array( $getList ) ){
        return $getList;
      }

    }

    return $getList;
  }

  /**
   * Remove specific post of favorite page
   */
  public static function remove_post_favorite_page( $postID, $userID = false ){
    $userID = ( $userID && is_numeric( $userID ) ? $userID : get_current_user_id() );

    if( $userID && $postID && is_numeric( $postID ) ){
      $post = get_post( sanitize_text_field( $postID ) );

      //verify post exist
      if( $post ){
        $get_favorited = self::get( $userID );

        if( $get_favorited && is_array( $get_favorited ) ){
          if( in_array( $postID, $get_favorited ) ){
            $key = array_search($postID, $get_favorited);
            unset( $get_favorited[$key] );
            self::save_favorite( $userID, $get_favorited );
          }
        }

        //cache request
        self::$list = $get_favorited;

        return true;
      }
    }

    return false;
  }

  /**
   * Set new post favorite page
   */
  public static function set_post_favorite_page( $postID, $userID = false ){
    $userID = ( $userID && is_numeric( $userID ) ? $userID : get_current_user_id() );

    if( $userID && $postID && is_numeric( $postID ) ){
      $post = get_post( sanitize_text_field( $postID ) );

      //verify post exist
      if( $post ){
        $get_favorited = self::get( $userID );

        if( $get_favorited && is_array( $get_favorited ) ){
          if( !in_array( $postID, $get_favorited ) ){
            $get_favorited[] = $postID;
            self::save_favorite( $userID, $get_favorited );
          }
        }else{
          $get_favorited = [ $postID ];
          self::save_favorite( $userID, $get_favorited );
        }

        //cache request
        self::$list = $get_favorited;

        return $get_favorited;
      }
    }

    return false;
  }

  /**
   * Save favorite page
   */
  private static function save_favorite( $userID, $data ){
    $userID = ( $userID && is_numeric( $userID ) ? $userID : get_current_user_id() );

    if( $userID && is_numeric( $userID ) && is_array( $data ) ){
      update_user_meta( $userID, self::$meta_name, $data );
    }
  }
}
