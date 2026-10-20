<?php 
class AemiCPTPosts 
{
  private $post_type_slug = 'post';

  function __construct(){
    //Register metabox
    add_action( 'add_meta_boxes', array( $this, 'meta_box' ) );
    add_action( 'save_post', array( $this, 'save_meta_box' ) );
    add_action( 'init', array( $this, 'replace_posts_to_wikimi'), 999 );
  }

  /**
   * Replace default name of POSTS to WikiMi
   */
  public function replace_posts_to_wikimi(){
    global $wp_post_types;
    $labels = &$wp_post_types['post']->labels;
    $wordReplace = 'WikiMi';

    foreach( $labels as $label_key => $label ){
      if( !in_array( $label_key, array( 'all_items', 'items_list' ) ) ){
        $labels->{$label_key} = strtr($label, array( 
          'post' => $wordReplace,
          'posts' => $wordReplace,
          'Post' => $wordReplace,
          'Posts' => $wordReplace,
          'entrada' => $wordReplace,
          'entradas' => $wordReplace,
          'Entrada' => $wordReplace,
          'Entradas' => $wordReplace
        ));
      }
    }
  }

  /**
   * Save data os post metabox
   * 
   * @param WP_Post $postID Return post object
   */
  public function save_meta_box( $postID ){
    
    // Check nonce exist
    if ( ! isset( $_POST[$this->post_type_slug . '_meta_nonce'] ) ) {
      return $postID;
    }
    
    $get_nonce_value = $_POST[$this->post_type_slug . '_meta_nonce'];

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $get_nonce_value, $this->post_type_slug ) ) {
      return $postID;
    }

    //Case is auto save, not process saved parameter
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return $postID;
    }

    //VERIFY is post submit is post_type equals a testimonials
    if( !isset( $_POST['post_type'] ) && isset( $_POST['post_type'] ) && $_POST['post_type'] != $this->post_type_slug ){
      return $postID;
    }

    //verify user contains permission of edit page or edit post
    if( !current_user_can( 'edit_page', $postID ) && !current_user_can( 'edit_post', $postID )  ){
      return $postID;
    }

    //Get data to save
    $data = ( isset( $_POST['_content_for_subscribers'] ) && $_POST['_content_for_subscribers'] ? true : false );

    // Save/Update post meta
    update_post_meta( $postID, '_content_for_subscribers', $data );

    //Get data to save
    $aemi_disable_thumbnail_in_single = ( isset( $_POST['_aemi_disable_thumbnail_in_single'] ) && $_POST['_aemi_disable_thumbnail_in_single'] ? true : false );

    // Save/Update post meta
    update_post_meta( $postID, '_aemi_disable_thumbnail_in_single', $aemi_disable_thumbnail_in_single );


    //Get data to save
    // $reading_time = ( isset( $_POST['_reading_time'] ) && (int) $_POST['_reading_time'] ? (int) $_POST['_reading_time'] : 0 );

    // // Save/Update post meta
    // update_post_meta( $postID, '_reading_time', $reading_time );
   
  }

  /**
   * Create/Register custom meta box of posts
   * 
   * @param WP_Post $post Return post object
   */
  public function meta_box( $post_type ) {
    if ( $post_type == $this->post_type_slug) {
        add_meta_box(
          $this->post_type_slug . '_extra_options',
            __( 'Extra options', AEMI_TEXT_DOMAIN ),
            array( $this, 'meta_box_content' ),
            $post_type,
            'side',
            'high'
        );
    }
  }

  /**
   * Content HTML
   */
  public function meta_box_content( $post ){
    wp_nonce_field( $this->post_type_slug, $this->post_type_slug . '_meta_nonce' );

    //Get in database
    $reading_time = get_post_meta( $post->ID, '_reading_time', true );
    $content_for_subscribers = get_post_meta( $post->ID, '_content_for_subscribers', true );
    $aemi_disable_thumbnail_in_single = get_post_meta( $post->ID, '_aemi_disable_thumbnail_in_single', true );

    echo '<div style="padding-top: 15px;display: flex;flex-direction: column;">';
      echo '<label for="content_for_subscribers" style="margin-right: 15px;"><strong>' . __( 'Exclusive content for subscribers:', AEMI_TEXT_DOMAIN ) . '</strong></label>';
      echo '<label class="aemi-painel-checkbox" style="margin-top: 15px;">
        <input type="checkbox" value="true" name="_content_for_subscribers" id="content_for_subscribers" ' . ( $content_for_subscribers ? 'checked="true"' : '' ) . ' />
        <span></span>
      </label>';
    echo '</div>';

    //echo '<hr />';

    // echo '<div style="padding-top: 15px;display: flex;flex-direction: column;">';
    //   echo '<label for="aemi_disable_thumbnail_in_single" style="margin-right: 15px;"><strong>' . __( 'Do not display featured image on content page:', AEMI_TEXT_DOMAIN ) . '</strong></label>';
    //   echo '<label class="aemi-painel-checkbox" style="margin-top: 15px;">
    //     <input type="checkbox" value="true" name="_aemi_disable_thumbnail_in_single" id="aemi_disable_thumbnail_in_single" ' . ( $aemi_disable_thumbnail_in_single ? 'checked="true"' : '' ) . ' />
    //     <span></span>
    //   </label>';
    // echo '</div>';

    /* ?>
    <div class="aemi-custom-fields__container">
      <div class="aemi-custom-fields__box">
        <div class="aemi-custom-fields__box__title">
          <label><?php echo __( 'Reading time', AEMI_TEXT_DOMAIN ); ?>:</label>
        </div>
        <div class="aemi-custom-fields__box__input-container">
          <div class="aemi-d-flex aemi-align-items-center">
            <input type="number" value="<?php echo $reading_time; ?>" name="_reading_time" class="aemi-custom-fields__box__input" />
            <strong class="aemi-pl-1"><?php echo __( 'Min.', AEMI_TEXT_DOMAIN ); ?></strong>
          </div>
        </div>
      </div>
    </div>
    <?php */
  }
}

new AemiCPTPosts();