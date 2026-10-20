<?php

if( !function_exists('aemi_replace_breadcrumbs_links_default_title') ){
  /**
   * Replace breacrumb default texts
   */
  function aemi_replace_breadcrumbs_links_default_title( $crumbs ){
    
    if( is_search() ){
      if( isset( $crumbs[1] ) && $crumbs[1] ){
        $searchText = 'You searched for';

        if( str_contains($crumbs[1]['text'], $searchText) ){
          $replaceText = explode( $searchText, $crumbs[1]['text'] );
          $replaceText[0] = __( 'You searched for', AEMI_TEXT_DOMAIN );
          $crumbs[1]['text'] = implode('', $replaceText);
        }
      }
    }

    return $crumbs;
  }

  add_filter( 'wpseo_breadcrumb_links', 'aemi_replace_breadcrumbs_links_default_title', 999 );
}

if( !function_exists('aemi_replace_variable_texts_wpseo_frontend_presentation') ){
  /**
   * Replace default texts
   */
  function aemi_replace_variable_texts_wpseo_frontend_presentation( $data ){
    if( $data ){
      if( $data->context && $data->context->indexable && $data->context->indexable->title){

        //Search title
        $searchText = 'You searched for';
        if( str_contains( $data->context->indexable->title, $searchText ) ){
          $replaceText = explode( $searchText, $data->context->indexable->title );
          $replaceText[0] = __( 'You searched for', AEMI_TEXT_DOMAIN );
          $data->context->indexable->title = implode('', $replaceText);
        }
      }
    }
  
    return $data;
  }

  add_filter( 'wpseo_frontend_presentation', 'aemi_replace_variable_texts_wpseo_frontend_presentation' );
}