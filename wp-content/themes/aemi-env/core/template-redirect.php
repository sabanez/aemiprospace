<?php 

/**
 * Automatically redirect specific pages to open specific template files
 */
class DeliveryAutoRedirectTemplateFiles
{
  
  /**
   * Add action and filters in WP
   */
  public function __construct() {
    add_action('template_include', array($this, 'manage_file_redirection'));
  }

  /**
   * Return my account page id
   */
  public function get_woocommerce_myaccount_page_id(){
    return get_option('woocommerce_myaccount_page_id');
  }

  public function manage_file_redirection( $template ){
    
    if( is_single() ){

    } else if( is_page() ){
      //redirect to custom login page
      if( is_page( $this->get_woocommerce_myaccount_page_id() ) ){
        $template = get_template_directory() . '/my-account.php';
      }
    }

    return $template;
  }
}

if( !is_admin() ){
  new DeliveryAutoRedirectTemplateFiles();
}