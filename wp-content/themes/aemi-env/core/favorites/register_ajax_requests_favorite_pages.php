<?php 
/**
 * Register endpoints ajax to save favorite pages
 */
if( !class_exists('AEMIRegisterAjaxRequestsFavoritePages') ){
  class AEMIRegisterAjaxRequestsFavoritePages {

    var $nonce_action = 'A89E2Q7F1V9E6T1IJ2SZ6F8T9A';
    var $nonce_name = '_favorite_page';

    function __construct(){
      add_action( 'wp_ajax_set_favorite_post', array( $this, 'register_ajax_actions' ) );
      //add_action( 'wp_ajax_nopriv_set_favorite_post', array( $this, 'register_ajax_actions' ) );
      add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
    }

    /**
     * Register necessary scripts
     */
    public function register_scripts(){
      wp_register_script( 'aemi-favorite-scripts', get_aemi_url_assets_js('core/favorites.min.js') );
      wp_enqueue_script( 'aemi-favorite-scripts' );
      wp_localize_script( 'aemi-favorite-scripts', 'aemi_favorite_scripts', array( 
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        $this->nonce_name => wp_create_nonce( $this->nonce_action ) 
      ) );
    }
    
    /**
     * Validate and save/remove favorite page to user
     */
    public function register_ajax_actions(){
      if( 
        isset( $_POST['postID'] ) && is_numeric( $_POST['postID'] ) && 
        isset( $_POST[$this->nonce_name] ) &&
        wp_verify_nonce( $_POST[$this->nonce_name], $this->nonce_action ) && 
        is_user_logged_in() &&
        isset( $_POST['favorite'] ) 
      ){

        $dataReturn = array(
          'success' => false,
          'msg' => __( 'The request could not be completed, please try again!', AEMI_TEXT_DOMAIN )
        );

        if( $_POST['favorite'] == 'true' ){
          $favorited = set_post_favorite_page( $_POST['postID'], get_current_user_id() );

          if( $favorited ){
            unset( $dataReturn['msg'] );
            $dataReturn['success'] = true;
          }

        } else{
          $favorited = remove_post_favorite_page( $_POST['postID'], get_current_user_id() );

          if( $favorited ){
            unset( $dataReturn['msg'] );
            $dataReturn['success'] = true;
          }
        }

        echo json_encode( $dataReturn );
      }

	    wp_die();
    }
  }

  new AEMIRegisterAjaxRequestsFavoritePages();
}