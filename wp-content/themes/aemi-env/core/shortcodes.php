<?php 

if( !function_exists('get_aemi_current_user_name_shortcode') ){
  add_shortcode('aemi_current_user_name', 'get_aemi_current_user_name_shortcode');

  function get_aemi_current_user_name_shortcode(){
    return get_aemi_current_user_name();
  }
}