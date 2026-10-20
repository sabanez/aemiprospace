<?php 

if( !function_exists( 'get_aemi_template_testimonials_slide' ) ){
  /**
   * Return HTML of slide testimonials of users in plataform
   * 
   * @param Array $args - This array contais args to return posts
   * 
   * @link Reference accept $args [https://developer.wordpress.org/reference/functions/get_posts/]
   */
  function get_aemi_template_testimonials_slide( $args = array() ){

    //default config to get posts testimonial
    $args = array_merge(
      array(
        'posts_per_page' => 20,
        'post_type' => 'testimonials'
      ),
      $args
    );

    //get testimonials
    $testimonials = get_posts( $args );

    //Variable return html
    $htmlReturn = '<div class="owl-carousel owl-theme aemi-testimonial-slide">';

    if( $testimonials ){
      foreach ( $testimonials as $testimonial ) {
        //get template testimonial HTML
        $htmlReturn .= get_aemi_box_testimonial( $testimonial );
      }
    }
    $htmlReturn .= '</div>';

    return $htmlReturn;
  }
}

if( !function_exists( 'get_aemi_box_testimonial' ) ){
  function get_aemi_box_testimonial( $post ){
    $post = get_post( $post );
    $get_thumbnail = get_the_post_thumbnail_url( $post->ID, 'thumbnail' );
    $text_below_name = get_post_meta( $post->ID, '_text_below_name', true );

    $html = '<div class="box-testimonial">';
      $html .= '<div class="box-testimonial__content">';
        $html .= apply_filters('the_content', $post->post_content);
      $html .= '</div>';
      $html .= '<div class="box-testimonial__user-info">';
        $html .= '<div class="box-testimonial__user-info-thumbnail" style="background-image: url(' . $get_thumbnail . ');"></div>';
        $html .= '<div class="box-testimonial__user-info-texts">';
          $html .= '<p class="box-testimonial__user-info-name">' . esc_html( $post->post_title ) . '</p>';
          if( trim ( $text_below_name ) != '' ){
            $html .= '<p class="box-testimonial__user-info-text-below">' . esc_html( $text_below_name ) . '</p>';
          }
        $html .= '</div>';
      $html .= '</div>';
    $html .= '</div>';

    return $html;
  }
}