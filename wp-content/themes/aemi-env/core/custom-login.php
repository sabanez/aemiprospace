<?php 

if( !class_exists('AemiCustomizeDefaultLoginScreen')){

  /**
   * Customize Default login WordPress
   */
  class AemiCustomizeDefaultLoginScreen {

    /**
     * Add action and filters in WP
     */
    public function __construct() {
      add_action( 'login_enqueue_scripts', [$this, 'aemi_custom_style_login'] );
      add_action('login_header', [$this, 'aemi_default_login_header_login']);
      add_action('login_footer', [$this, 'aemi_default_login_footer_login']);
      add_filter( 'login_headerurl', [$this, 'aemi_custom_login_url'] );
      add_filter( 'login_display_language_dropdown' , '__return_false' );
    }

    /**
     * SET custom login CSS and JS
     * @return void;
     */
    public function aemi_custom_style_login() {
      wp_enqueue_style( 'custom-login', get_aemi_url_assets_css('style-login.css') );
      wp_enqueue_script( 'custom-login-js', get_aemi_url_assets_js('custom-login.js'), array('jquery') );
    }

    /**
     * SET custom HTML in header login screen
     * @return void;
     */
    public function aemi_default_login_header_login() {
      echo '<style>.login h1 a{ background-image: none,url(' . get_aemi_url_assets_imgs('logo-login.png') . '); }</style>';
      echo '<div class="login-aemi-container">';
        echo '<div class="login-aemi-content">';
          echo '<div class="login-form-content">';
    }
  
    /**
     * SET custom HTML in footer login screen
     * @return void
     */
    public function aemi_default_login_footer_login() {
          echo '</div>';
          echo '<div class="login-icon-content">
            <img src="' . get_template_directory_uri() . '/assets/images/icon-login-default.png" />
          </div>';
        echo '</div>';
      echo '</div>';
    }

    /**
     * Customize url of button with logo in login screen WP
     * @return void
     */
    public function aemi_custom_login_url() {
        return home_url();
    }

  }

  //RUN
  new AemiCustomizeDefaultLoginScreen();
}