<?php 
/**
 * Register custom favorite pages
 */
if( !class_exists('AEMICustomFavoritePage') ){
  class AEMICustomFavoritePage {
    function __construct(){
      add_action( 'init', array( $this, 'register_rewrite_rule' ) );
      add_filter('query_vars', array( $this, 'query_vars' ));
      add_action( 'template_include', array( $this, 'template_redirect' ));
    }

    /**
     * Register custom global vars WordPress
     */
    public function query_vars( $vars ) {
      $vars[] = "favorite_page";
      return $vars;
    }

    /**
     * Register custom endpoint page wordpress
     */
    public function register_rewrite_rule(){
      add_rewrite_rule('favorites/?$', 'index.php?favorite_page=true', 'top');
    }

    /**
     * Redirect template to custom favorite endpoint
     */
    public function template_redirect( $template ){
      if( get_query_var( 'favorite_page' ) == true ){
        add_filter( 'wpseo_breadcrumb_links', array( $this, 'append_custom_endpoint_breadcrumb_yoast' ) );

        return get_template_directory() . '/custom-page-favorite.php';
      }

      return $template;
    }

    /**
     * Append in Plugin Yoast favorites
     */
    public function append_custom_endpoint_breadcrumb_yoast( $crumbs ){
      $crumbs[] = array(
        'url' => home_url() . 'favorites',
        'text' => __( 'Favorites', AEMI_TEXT_DOMAIN ),
      );

      return $crumbs;
    }
  }
  
  new AEMICustomFavoritePage();
}