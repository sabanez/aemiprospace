<?php

if( !function_exists('get_aemi_template_categories') ){
  /**
   * Return rendered html list categories
   * 
   * @param Array $args - This array contais args to return categories
   * 
   * @link Reference accept $args [https://developer.wordpress.org/reference/functions/get_categories/]
   * 
   * @return String
   */
  function get_aemi_template_categories( $args = array() ){

    //default config to get posts testimonial
    $args = array_merge(
      array(
        'number' => 12,
        'exclude' => 1
      ),
      $args
    );

    //get testimonials
    $categories = get_categories( $args );

    $htmlRender = '';

    if( $categories ){
      foreach ( $categories as $category ) {
        $htmlRender .= get_aemi_box_category($category, $args);
      }
    }

    return $htmlRender;
  }
}

if( !function_exists('get_aemi_box_category') ){

  /**
   * Return HTML box rendered
   * 
   * @param Int|Object $category
   *
   * @return String
   */
  function get_aemi_box_category( $category, $args ){
    $category = get_category( $category );

    get_template_part('core/templates/contents/content', 'category-box', [
        'category' => $category,
        'params' => $args
    ]);
  }
}