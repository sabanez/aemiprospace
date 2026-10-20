<?php 

class ContentTypeTaxonomyCustomizedFields
{
  
  private $term_slug = 'content_type';

  function __construct(){
    add_action($this->term_slug . '_edit_form_fields', array( $this, 'custom_meta_box' ));
    add_action('created_' . $this->term_slug, array( $this, 'save_meta_data' ));
    add_action('edited_' . $this->term_slug, array( $this, 'save_meta_data' ));
    add_action('admin_enqueue_scripts', array($this, 'custom_scripts'));
    add_action( 'init', array( $this, 'register_taxonomy' ) );
  }
  
  /**
   * Register custom taxonomy
   */
  public function register_taxonomy() {

    $labels = array(
      'name'                       => _x( 'Content types', 'Taxonomy General Name', AEMI_TEXT_DOMAIN ),
      'singular_name'              => _x( 'Content type', 'Taxonomy Singular Name', AEMI_TEXT_DOMAIN ),
      'menu_name'                  => __( 'Content type', AEMI_TEXT_DOMAIN ),
      'all_items'                  => __( 'All content types', AEMI_TEXT_DOMAIN ),
      'parent_item'                => __( 'Parent level', AEMI_TEXT_DOMAIN ),
      'parent_item_colon'          => __( 'Parent level:', AEMI_TEXT_DOMAIN ),
      'new_item_name'              => __( 'New content type Name', AEMI_TEXT_DOMAIN ),
      'add_new_item'               => __( 'Add New content type', AEMI_TEXT_DOMAIN ),
      'edit_item'                  => __( 'Edit content type', AEMI_TEXT_DOMAIN ),
      'update_item'                => __( 'Update content type', AEMI_TEXT_DOMAIN ),
      'view_item'                  => __( 'View content type', AEMI_TEXT_DOMAIN ),
      'separate_items_with_commas' => __( 'Separate content types with commas', AEMI_TEXT_DOMAIN ),
      'add_or_remove_items'        => __( 'Add or remove content type', AEMI_TEXT_DOMAIN ),
      'choose_from_most_used'      => __( 'Choose from the most used', AEMI_TEXT_DOMAIN ),
      'popular_items'              => __( 'Popular content types', AEMI_TEXT_DOMAIN ),
      'search_items'               => __( 'Search content types', AEMI_TEXT_DOMAIN ),
      'not_found'                  => __( 'Not Found', AEMI_TEXT_DOMAIN ),
      'no_terms'                   => __( 'No content types', AEMI_TEXT_DOMAIN ),
      'items_list'                 => __( 'Content types list', AEMI_TEXT_DOMAIN ),
      'items_list_navigation'      => __( 'Content types list navigation', AEMI_TEXT_DOMAIN ),
    );
    $args = array(
      'labels'                     => $labels,
      'hierarchical'               => true,
      'public'                     => false,
      'show_in_rest'               => true,
      'show_ui'                    => true,
      'show_admin_column'          => true,
      'show_in_nav_menus'          => true,
      'show_tagcloud'              => true,
    );
            
    //A temporary code to disable hierarchical
    if( is_admin() && isset( $_SERVER['REQUEST_URI'] ) && $_SERVER['REQUEST_URI'] ){
        $temp = explode( '/wp-admin/', $_SERVER['REQUEST_URI'] );

        if( isset($temp[1]) && strpos( $temp[1],'edit-tags.php?taxonomy=' . $this->term_slug ) !== -1 ){
            $args['hierarchical'] = false;
        }
    }

    register_taxonomy( $this->term_slug, array( 'post' ), $args );

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
   
  }

  /**
   * Create fields os taxonomy meta
   * 
   * @param WP_Term $term return object of term
   */
  public function custom_meta_box( $term ){
    $category_thumbnail_id = get_term_meta( $term->term_id, '_category_thumbnail_id', true );
    $url = '';

    if( $category_thumbnail_id ){
      $url = wp_get_attachment_url($category_thumbnail_id);
    }
  
    //fields
    wp_nonce_field( $this->term_slug, $this->term_slug . '_meta_nonce' );
    ?>
    <tr class="form-field">
      <th>
        <label for="_content_editor"><?php echo __('Icon', AEMI_TEXT_DOMAIN); ?>:</label>
      </th>
      <td>
        <div style="height: 230px; width: 230px; border-radius: 5px; border: 4px solid #fff; margin-bottom: 25px; background-color: #ccc; box-shadow: 0 0 11px -4px rgb(0,0,0,0.30); background-size: cover; background-position: center;<?php echo ( $url ? 'background-image: url(' . $url . ');' : '' ); ?>" id="attachment-url-thumbnail">
          <button type="button" id="attachment-remove-thumbnail" style="background: #e73535;color: #fff;border: none;border-radius: 70%;line-height: 25px;padding-top: 6px;float: right;margin: -17px -18px 0 0; cursor:pointer;<?php echo ( $url ? 'display: block;' : 'display: none;' ); ?>"><span class="dashicons dashicons-no"></span></button>
        </div>
        <input type="hidden" id="attachment-id-thumbnail" name="_category_thumbnail_id" value="<?php echo $category_thumbnail_id; ?>" />
        <button type="button" class="button button-primary" id="attachment-id-btn"><?php echo __('Select thumbnail', AEMI_TEXT_DOMAIN); ?></button>
      </td>
    </tr>
    <?php
  }
}

new ContentTypeTaxonomyCustomizedFields();