<?php 

class CategoryCustomizedFields
{
  private $term_slug = 'category';

  function __construct(){
    add_action($this->term_slug . '_edit_form_fields', array( $this, 'custom_meta_box' ));
    add_action('created_' . $this->term_slug, array( $this, 'save_meta_data' ));
    add_action('edited_' . $this->term_slug, array( $this, 'save_meta_data' ));
    add_action('admin_enqueue_scripts', array($this, 'custom_scripts'));
    add_action( 'init', array($this, 'replace_default_category_name' ));
    //add_filter( "rest_category_query", array( $this, 'filter_rest_category_query' ), 9999, 2 ); 
    //add_filter( 'rest_request_after_callbacks', array( $this, 'filter_return_api' ), 9999, 3 );
    add_action('init', function(){
       global $wp_taxonomies;
      
       unregister_taxonomy_for_object_type( 'post_tag', 'post' );
       if ( taxonomy_exists( 'post_tag') ){
          unset( $wp_taxonomies['post_tag']);
       }
       unregister_taxonomy('post_tag');
    });

    add_action( 'admin_footer', function(){
      global $current_screen;

      if( $current_screen ){
        if( $current_screen->parent_file == 'edit.php' && $current_screen->post_type == 'post' ){
          ?>
          <script>
            window.addEventListener('load', function(){
              function aemiAddButtonTermMarkAllUnMark($){
                var sideBarTaxonomyToAdd = $('.editor-post-taxonomies__hierarchical-terms-list[aria-label="Tags"]');
                  if( sideBarTaxonomyToAdd.length > 0 ){
                    if( sideBarTaxonomyToAdd.parent().find('.aemi-mark-input-box').length <= 0 ){
                      var templateBTNHTML = '<button type="button" style="margin-top: 15px;margin-bottom: 10px;font-size: 12px;padding: 0 4px;min-height: 34px;margin-right: 10px;" class="button button-primary aemi-term-unmark-all">';
                        templateBTNHTML += '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="width: 13px;margin-right: 5px;position: relative;top: 3px;"><!--! Font Awesome Pro 6.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path fill="#fff" d="M384 32C419.3 32 448 60.65 448 96V416C448 451.3 419.3 480 384 480H64C28.65 480 0 451.3 0 416V96C0 60.65 28.65 32 64 32H384zM384 80H64C55.16 80 48 87.16 48 96V416C48 424.8 55.16 432 64 432H384C392.8 432 400 424.8 400 416V96C400 87.16 392.8 80 384 80z"/></svg>';
                        templateBTNHTML += '<?php echo __( 'Unmark all', AEMI_TEXT_DOMAIN ); ?>';
                        templateBTNHTML += '</button>';
                        templateBTNHTML += '<button type="button" style="margin-top: 15px;margin-bottom: 10px;font-size: 12px;padding: 0 4px;min-height: 34px;margin-right: 10px;" class="button button-secondary aemi-term-mark-all">';
                        templateBTNHTML += '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="width: 13px;margin-right: 5px;position: relative;top: 3px;"><!--! Font Awesome Pro 6.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path fill="#2271b1" d="M211.8 339.8C200.9 350.7 183.1 350.7 172.2 339.8L108.2 275.8C97.27 264.9 97.27 247.1 108.2 236.2C119.1 225.3 136.9 225.3 147.8 236.2L192 280.4L300.2 172.2C311.1 161.3 328.9 161.3 339.8 172.2C350.7 183.1 350.7 200.9 339.8 211.8L211.8 339.8zM0 96C0 60.65 28.65 32 64 32H384C419.3 32 448 60.65 448 96V416C448 451.3 419.3 480 384 480H64C28.65 480 0 451.3 0 416V96zM48 96V416C48 424.8 55.16 432 64 432H384C392.8 432 400 424.8 400 416V96C400 87.16 392.8 80 384 80H64C55.16 80 48 87.16 48 96z"/></svg>';
                        templateBTNHTML += '<?php echo __( 'Mark all', AEMI_TEXT_DOMAIN ); ?>';
                        templateBTNHTML += '</button>';

                      $( '<div class="aemi-mark-input-box">' + templateBTNHTML + '</div>' ).insertAfter( sideBarTaxonomyToAdd );

                      
                      jQuery('.aemi-term-mark-all').on('click', function( e ){
                          e.preventDefault();
                          var parentElement = jQuery(this).closest('.components-panel__body');

                          if( parentElement.length > 0 ){
                            var currentLabelElement = parentElement.find('.components-checkbox-control__label');

                            if( currentLabelElement ){
                              currentLabelElement.each( function( index ){
                                var currentLabel = $( currentLabelElement[index] );

                                if( currentLabel.parent().find('input:checked').length == 0 ){
                                  currentLabel.click();
                                }
                              } );
                            }
                          }
                      });

                      jQuery('.aemi-term-unmark-all').on('click', function( e ){
                          e.preventDefault();
                          var parentElement = jQuery(this).closest('.components-panel__body');

                          if( parentElement.length > 0 ){
                            var currentLabelElement = parentElement.find('.components-checkbox-control__label');

                            if( currentLabelElement ){
                              currentLabelElement.each( function( index ){
                                var currentLabel = $( currentLabelElement[index] );

                                if( currentLabel.parent().find('input:checked').length > 0 ){
                                  currentLabel.click();
                                }
                              } );
                            }
                          }
                      });
                    }
                  }
              }

              jQuery('body').on('click', '.edit-post-sidebar__panel-tabs button.components-button.edit-post-sidebar__panel-tab', function(){
                aemiAddButtonTermMarkAllUnMark(jQuery);
              });

                setTimeout(() => { aemiAddButtonTermMarkAllUnMark(jQuery); }, 2000);
                setTimeout(() => { aemiAddButtonTermMarkAllUnMark(jQuery); }, 3000);
                setTimeout(() => { aemiAddButtonTermMarkAllUnMark(jQuery); }, 4000);
                setTimeout(() => { aemiAddButtonTermMarkAllUnMark(jQuery); }, 5000);
                setTimeout(() => { aemiAddButtonTermMarkAllUnMark(jQuery); }, 6000);
                setTimeout(() => { aemiAddButtonTermMarkAllUnMark(jQuery); }, 7000);
            });
          </script>
          <?php
        }
      }
    }, 9999 );
  }
  // public function filter_return_api( $response, $handler, $request ){

  //   $params = $request->get_params(); 
  //   if( $request->get_route() == '/wp/v2/categories' ){
  //     if( isset( $params['_locale'] ) && isset( $params['context'] ) && $params['context'] == 'view' ){

  //       if( isset( $handler['show_in_index'] ) && $handler['show_in_index'] && isset( $handler['methods']['GET'] ) && $handler['methods']['GET'] ){
  //         $returnData = array();

  //         foreach ( $response->data as $category ) {
  //           if( $category['parent'] == '1' ){
  //             $category['parent'] = 0;
  //             $returnData[] = $category['parent'];
  //           }
  //         }

  //         echo '<pre>';
  //         print_r( $response->data );
  //         echo '</pre>';
  //         exit();
  //         //$response->data = $returnData;
  //       }
        
  //     }
  //   }

  //   return $response;
  // }

  // public function filter_rest_category_query( $arg, $request ){
  //   $params = $request->get_params(); 
  //   if( isset( $params['_locale'] ) &&  $arg['taxonomy'] == 'category' ){
  //     $arg['parent'] = 1;
  //   }
  //   return $arg;
  // }

  public function replace_default_category_name(){
    global $wp_taxonomies;
    $labels = &$wp_taxonomies['category']->labels;
    $labels->name                 = __( 'Therapeutic areas', AEMI_TEXT_DOMAIN );
    $labels->singular_name        = __( 'Therapeutic areas', AEMI_TEXT_DOMAIN );
    $labels->add_new              = __( 'Add therapeutic area', AEMI_TEXT_DOMAIN );
    $labels->add_new_item         = __( 'Add therapeutic area', AEMI_TEXT_DOMAIN );
    $labels->edit_item            = __( 'Edit therapeutic area', AEMI_TEXT_DOMAIN );
    $labels->new_item             = __( 'Therapeutic areas', AEMI_TEXT_DOMAIN );
    $labels->view_item            = __( 'View therapeutic areas', AEMI_TEXT_DOMAIN );
    $labels->search_items         = __( 'Search therapeutic areas', AEMI_TEXT_DOMAIN );
    $labels->not_found            = __( 'No therapeutic areas found', AEMI_TEXT_DOMAIN );
    $labels->not_found_in_trash   = __( 'No therapeutic areas found in Trash', AEMI_TEXT_DOMAIN );
    $labels->all_items            = __( 'All therapeutic areas', AEMI_TEXT_DOMAIN );
    $labels->menu_name            = __( 'Therapeutic area', AEMI_TEXT_DOMAIN );
    $labels->name_admin_bar       = __( 'Therapeutic area', AEMI_TEXT_DOMAIN );
  }

  /**
   * Add custom styles and javascript
   * @param String $hook_suffix 
   */
  public function custom_scripts($hook_suffix){
    if( $hook_suffix == 'term.php' ){
      global $taxonomy;

      if( $taxonomy == 'category' ){
        if ( ! did_action( 'wp_enqueue_media' ) ) {
          wp_enqueue_media();
        }

        wp_enqueue_script( 'aemi_custom_meta_box', get_stylesheet_directory_uri() . '/assets/js/core/register-custom-taxonomies/category-edit.js', array( 'jquery' ), '1.0.0', true );

        wp_localize_script( 'aemi_custom_meta_box', 'aemi_custom_meta_box_vars', array(
          'aemiTitleUploadImage' => __('Change thumbnail', AEMI_TEXT_DOMAIN),
          'aemiBtnTextUploadImage' => __('Use this image', AEMI_TEXT_DOMAIN),
        ) );
      }
    }
  }

  /**
   * Save custom meta data term
   * 
   * @param INT $term_id Return current term id
   */
  public function save_meta_data( $term_id ){
    
    // Check nonce exist
    if ( ! isset( $_POST[$this->term_slug . '_meta_nonce'] ) ) {
      return $term_id;
    }
    
    $get_nonce_value = $_POST[$this->term_slug . '_meta_nonce'];

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $get_nonce_value, $this->term_slug ) ) {
      return $term_id;
    }

    //Case is auto save, not process saved parameter
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return $term_id;
    }
    
    //VERIFY is post submit is taxonomy equals a testimonials
    if( !isset( $_POST['taxonomy'] ) && isset( $_POST['taxonomy'] ) && $_POST['taxonomy'] != $this->term_slug ){
      return $term_id;
    }
    
    //verify user contains permission of edit page or edit post
    if( !current_user_can('edit_posts') ){
      return $term_id;
    }

    //thumnail id
    $category_thumbnail_id = ( isset( $_POST['_category_thumbnail_id'] ) && $_POST['_category_thumbnail_id'] ? esc_attr( $_POST['_category_thumbnail_id'] ) : '' );

    if( $category_thumbnail_id ){
      if( !get_post($category_thumbnail_id) ){
        $category_thumbnail_id = '';
      }
    }
    //save data thumbnail
    update_term_meta( $term_id, '_category_thumbnail_id', $category_thumbnail_id );

    //Editor content
    $content_data = ( isset( $_POST['_content_editor'] ) && $_POST['_content_editor'] ? wp_kses_post( $_POST['_content_editor'] ) : '' );
    update_term_meta( $term_id, '_content_editor', $content_data );
   
  }

  /**
   * Create fields os taxonomy meta
   * 
   * @param WP_Term $term return object of term
   */
  public function custom_meta_box( $term ){
    $content = get_term_meta( $term->term_id, '_content_editor', true );
    $category_thumbnail_id = get_term_meta( $term->term_id, '_category_thumbnail_id', true );
    $url = '';
    $tags = aemi_get_tags_by_category( $term->term_id );

    if( $category_thumbnail_id ){
      $url = wp_get_attachment_url($category_thumbnail_id);
    }
  
    //fields
    wp_nonce_field( $this->term_slug, $this->term_slug . '_meta_nonce' );
    ?>
    <tr class="form-field">
      <th>
        <label for="_content_editor"><?php echo __('Content', AEMI_TEXT_DOMAIN); ?>:</label>
      </th>
      <td>
        <?php
          wp_editor( $content, '_content_editor', array() );
        ?>
      </td>
    </tr>
    <tr class="form-field">
      <th>
        <label for="_content_editor"><?php echo __('Thumbnail', AEMI_TEXT_DOMAIN); ?>:</label>
      </th>
      <td>
        <div style="height: 230px; width: 230px; border-radius: 5px; border: 4px solid #fff; margin-bottom: 25px; background-color: #ccc; box-shadow: 0 0 11px -4px rgb(0,0,0,0.30); background-size: cover; background-position: center;<?php echo ( $url ? 'background-image: url(' . $url . ');' : '' ); ?>" id="attachment-url-thumbnail">
          <button type="button" id="attachment-remove-thumbnail" style="background: #e73535;color: #fff;border: none;border-radius: 70%;line-height: 25px;padding-top: 6px;float: right;margin: -17px -18px 0 0; cursor:pointer;<?php echo ( $url ? 'display: block;' : 'display: none;' ); ?>"><span class="dashicons dashicons-no"></span></button>
        </div>
        <input type="hidden" id="attachment-id-thumbnail" name="_category_thumbnail_id" value="<?php echo $category_thumbnail_id; ?>" />
        <button type="button" class="button button-primary" id="attachment-id-btn"><?php echo __('Select thumbnail', AEMI_TEXT_DOMAIN); ?></button>
      </td>
    </tr>
    <tr>
      <th>
        <label><?php echo __('Related tags', AEMI_TEXT_DOMAIN); ?>:</label>
      </th>
      <td>
        <style>
          .custom-tag-link{
            color: #333;text-decoration: none;padding: 10px;display: block;border: 1px solid #ddd;
          }

          .custom-tag-link:hover{
            color: #fff;
            background: #2271b1;
            border-color: #11436a;
          }
        </style>


        <div style="border: 1px solid #ccc;padding: 10px 30px;box-sizing: border-box;background: #fff;width: calc( 100% - 30px );">
          
          <p style="margin-bottom: 20px;"><strong><?php echo __('Check all related tags', AEMI_TEXT_DOMAIN); ?>:</strong></p>
          
          <ul>
            <?php if( $tags ): ?>
              <?php foreach ( $tags as $tag ) : ?>
                <li>
                  <span>
                    <a class="custom-tag-link" target="_blank" href="<?php echo get_admin_url(); ?>term.php?taxonomy=aemi_tags&tag_ID=<?php echo $tag->term_id; ?>"><strong><?php echo esc_attr( $tag->name ) ; ?></strong></a>
                  </span>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        </div>
      </td>
    </tr>
    <?php
  }
}

new CategoryCustomizedFields();