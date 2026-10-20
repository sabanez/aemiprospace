<?php 

if( !class_exists('AemiCustomAdminPanelAppearance')){

  /**
   * Custom Admin Panel Appearance
   */
  class AemiCustomAdminPanelAppearance {
    
    /**
     * Add action and filters in WP
     */
    public function __construct() {

      /**
       * General actions and filters
       */
      if( is_admin() ){
        add_filter( 'contextual_help', [$this, 'remove_help_tabs'], 999, 3 );
        add_action( 'wp_dashboard_setup', [$this, 'remove_default_dashboard_widgets'] );
        add_action( 'admin_init', [$this, 'customize_wp_admin_default_appearance_style'], 500);
        add_action( 'admin_head', [$this, 'custom_logo_in_post_editor']);
      }
      
      //Toolbar
      add_action( 'wp_before_admin_bar_render', [$this, 'remove_unused_toolbar_wordpress'], 30 );
      add_action( 'admin_bar_menu', [$this, 'add_link_to_admin_bar'], 1);

      if(!is_admin()){
        add_action( 'wp_enqueue_scripts', [$this, 'aemi_register_styles_toolbar_frontend'] );
        add_action( 'wp_head', [$this, 'aemi_inline_style_header'], 999 );
      }
        
      if( is_admin() ){
        //Remove default text of wordpress "Thank you for creating with WordPress.Version 5.9.2"
        add_filter('admin_footer_text', '__return_false');
        //remove in wp admin text in footer of version WP
        add_filter( 'update_footer', '__return_empty_string', 30 );
      }

    }

    /**
     * Customize logo in new EDITOR WP
     */
    public function custom_logo_in_post_editor(){
      ?>
      <style>

        .interface-interface-skeleton__header .edit-post-header .edit-post-fullscreen-mode-close.has-icon:before {
          background-image: url(<?php echo get_aemi_url_assets_imgs('editor-admin-logo.png') ?>);
          background-size: contain;
        }

        .interface-interface-skeleton__header .edit-post-header .edit-post-fullscreen-mode-close.has-icon svg {
          display: none;
        }

      </style>
      <?php
    }

    /**
     * Remove unused toolbars
     */
    public function remove_unused_toolbar_wordpress() {
      global $wp_admin_bar;
      $wp_admin_bar->remove_menu( 'wp-logo' );
      $wp_admin_bar->remove_menu( 'site-name' );
    }

    /**
     * 
     * Remove tab help in admin tab
     * 
     * @param $old_help
     * @param $screen_id
     * @param $screen
     * 
     * @return $old_help;
     */
    public function remove_help_tabs($old_help, $screen_id, $screen){
        $screen->remove_help_tabs();
        return $old_help;
    }

    /**
     * 
     * Add custom menu admin bar
     * 
     * @param $admin_bar
     * 
     * @return $old_help;
     */
    public function add_link_to_admin_bar($admin_bar) {
      $args = array(
          'id' => 'custom-menu-logo',
          'title' => '<img src="' . ( !is_admin() ? get_aemi_url_assets_imgs('logo-admin-toolbar-frontend.png') :  get_aemi_url_assets_imgs('logo-admin-toolbar.png') ) . '" class="logo-admin-toolbar" />', 
          'href' => admin_url()
      );
      $admin_bar->add_node($args);
    }

    /**
     * Remove default widgets WordPress
     * 
     * @return null
     */
    public function remove_default_dashboard_widgets() {
        remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
    }

    /**
     * Set custom admin style of wordPress
     */
    public function customize_wp_admin_default_appearance_style(){
      global $_wp_admin_css_colors;
      $_wp_admin_css_colors['fresh']->url = get_aemi_url_assets_css('custom-wp-admin-style.css');
    }
    
    /**
     * Register styles and scripts
     */
    function aemi_register_styles_toolbar_frontend(){

      if ( is_admin_bar_showing() ) {
        wp_register_style( 'aemi-custom-wp-admin', get_aemi_url_assets_css('custom-wp-admin-style.css') );
        wp_enqueue_style( 'aemi-custom-wp-admin' );
      }
    }

    /**
     * Register inline style
     */
    function aemi_inline_style_header(){
      if ( is_admin_bar_showing() && !is_admin()) {
      ?>
      <style>
        body{
          margin-top: calc( var(--default-toolbar-height) - 32px);
        }

      	@media screen and ( max-width: 782px ) {
          body{
            margin-top: calc( var(--default-toolbar-height) - 46px);
          }
        }
      </style>
      <?php
      }
    }
  }

  new AemiCustomAdminPanelAppearance();
}
