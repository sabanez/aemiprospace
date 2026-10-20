<?php 

class AemiHomeAdminFieldsAndSettings{ 

  var $post_type_slug = 'page';

  public function __construct(){
    if( is_admin() ){
      add_filter( 'use_block_editor_for_post', array( $this, 'disable_gutenberg_editor'), 10, 2 );
      add_action( 'add_meta_boxes', array( $this, 'meta_box' ), 10, 2 );
      add_action( 'save_post', array( $this, 'save_meta_box' ) );
      add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
    }
  }
  
  /**
   * Register script and style
   */
  function register_scripts( $hook ){
    if( $hook == 'post.php' ){
      if( isset( $_GET['post'] ) && $_GET['post'] == get_option('page_on_front') ){
        wp_enqueue_media();
      }
    }
  }

  /**
   * This method disable gutenber editor for home page in admin
   * and remove the default editor wordpress
   */
  function disable_gutenberg_editor( $useEditor, $post ) {
    if ( get_option('page_on_front') == $post->ID ) {
      //disable default editor wordpress
      remove_post_type_support( $post->post_type, 'editor');
      remove_post_type_support( $post->post_type, 'thumbnail');
      remove_post_type_support( $post->post_type, 'page-attributes');
      remove_post_type_support( $post->post_type, 'author');
      return false;
    }
    return $useEditor;
  }


  /**
   * Save data os post metabox
   * 
   * @param WP_Post $postID Return post object
   */
  public function save_meta_box( $postID ){
    //Case current page not is HOME PAGE
    if ( get_option('page_on_front') != $postID ) return;
    
    // Check nonce exist
    if ( ! isset( $_POST[$this->post_type_slug . '_home_meta_nonce'] ) ) {
      return $postID;
    }
    
    $get_nonce_value = $_POST[$this->post_type_slug . '_home_meta_nonce'];

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

    $data_slide_home = $this->validate_slide_home_and_return_data_to_save( $_POST );
    update_post_meta( $postID, '_slides_home', $data_slide_home );
   
  }

  /**
   * This method will organize and validate if there is valid data from the home slider to be saved
   * 
   * @param Aarray|$_POST $data -Just inform an array that has the data with the slides_home informed is usually in a $_POST after a submit
   */
  private function validate_slide_home_and_return_data_to_save( $datas = array() ){
    $returnDataToSave = array();

    if( $_POST && isset( $_POST['slides_home'] ) ){
      $slides = $_POST['slides_home'];
      
      //langs enabled permitted to save
      $langs = get_aemi_wp_get_available_translations();

      foreach( $langs as $lang_key => $lang ){
        $languages = $lang['language'];

        if( isset($slides[$languages]) ){
          $loop = 0;
          foreach( $slides[$languages] as $slide ){
            $data_to_save = array();
            $containsValueInLoop = false;

            if( $slide ){
              foreach( $slide as $field_key => $field_value ){
                if( $field_value ){
                  $containsValueInLoop = true;
                  $data_to_save[$field_key] = strip_tags($field_value, '<p><b><strong><b><em><i><u><br>');
                }
              }
            }

            if( $containsValueInLoop ){
              $returnDataToSave[$languages][$loop] = $data_to_save;
              $loop++;
            }
          }
        }
      }
    }

    return $returnDataToSave;
  }

  /**
   * Create/Register custom meta box of posts
   * 
   * @param WP_Post $post Return post object
   */
  public function meta_box( $post_type, $post ) {
    if ( $post_type == $this->post_type_slug) {
      //Case current page is HOME PAGE
      if ( get_option('page_on_front') == $post->ID ){
        add_meta_box(
          $this->post_type_slug . '_home_page_settings',
            __( 'Home settings', AEMI_TEXT_DOMAIN ),
            array( $this, 'meta_box_content' ),
            $post_type,
            'advanced',
            'high'
        ); 
      }
    }
  }

  /**
   * Content HTML
   */
  public function meta_box_content( $post ){
    wp_nonce_field( $this->post_type_slug, $this->post_type_slug . '_home_meta_nonce' );
    
    ?>
    <div class="aemi-custom-fields__container">
      <div class="aemi-custom-fields__box align-to-top">
        <div class="aemi-custom-fields__box__title">
          <label><?php echo __( 'Slides', AEMI_TEXT_DOMAIN ); ?>:</label>
        </div>
        <div class="aemi-custom-fields__box__input-container">
          <?php echo $this->generateSlideTabs( $post ); ?>
        </div>
      </div>
    </div>
    <?php
  }

  /**
    * Generate tabs for langs  
    */
  public function generateSlideTabs( $post = false ){
    $slides = array();

    if( isset( $post ) && $post ){
      //Get data in database
      $slides_home = get_post_meta( $post->ID, '_slides_home', true );
      if( $slides_home ) $slide = $slides_home;
    }

    $langs = get_aemi_wp_get_available_translations();

    //habilitar temporariamente apenas o idioma principal do site
    //quando for multi-idioma apenas remova a linha de baixo
    $langs = array( $langs[0] );

    foreach( $langs as $lang_key => $lang ){
      $langs[$lang_key]['tab_name'] = __( $lang['native_name'], AEMI_TEXT_DOMAIN );
      $langs[$lang_key]['tab_content'] = $this->generateSlideContent( 
        array( 
          'prefix' => 'slides_home['.$lang['language'] . '][AEMIGenerateFieldsRepeatLoopID]', 
          'slides' => (isset( $slide[$lang['language']] ) && $slide[$lang['language']] ? $slide[$lang['language']] : array()) 
        ) 
      );
    }

    $tabs = new AEMIAutoGenerateAdminTabs( $langs );
    return $tabs->run();
  }

  /**
   * Generate tab content slides
   */
  private function generateSlideContent( $options = array() ){

    $htmlToReplace = $this->generateSlideFields( $options );
    $htmlPreRendered = array();

    if( isset( $options['slides'] ) && $options['slides'] ){
      foreach ($options['slides'] as $slide_key => $slide_value) {
        $news_values = array_merge( $options, $slide_value );
        $htmlPreRendered[] = $this->generateSlideFields($news_values);
      }
    }

    $fieldsReplace = new AEMIGenerateFieldsRepeat( $htmlToReplace, $htmlPreRendered );
    
    $returnHtml = $fieldsReplace->run();

    return $returnHtml;
  }

  /**
   * 
   */
  public function generateSlideFields( $options = array() ){
    $prefixInputs = ( isset( $options['prefix'] ) && $options['prefix'] ? $options['prefix'] : '' );
    $title = ( isset( $options['title'] ) && $options['title'] ? esc_attr( $options['title'] ) : '' );
    $content = ( isset( $options['content'] ) && $options['content'] ? esc_attr( $options['content'] ) : '' );
    $button_text = ( isset( $options['button_text'] ) && $options['button_text'] ? esc_attr( $options['button_text'] ) : '' );
    $button_link = ( isset( $options['button_link'] ) && $options['button_link'] ? esc_attr( $options['button_link'] ) : '' );
    $thumbnail_id = ( isset( $options['thumbnail_id'] ) && $options['thumbnail_id'] ? esc_attr( $options['thumbnail_id'] ) : '' );
    $thumbnail_id_mobile = ( isset( $options['thumbnail_id_mobile'] ) && $options['thumbnail_id_mobile'] ? esc_attr( $options['thumbnail_id_mobile'] ) : '' );

    $returnHtml = '';

    //Get rendered element to working WordPress midia gallery
    $returnHtml .= AEMIGenerateFieldsRepeat::generate_fields_image( array(
      'input_name' => $prefixInputs . '[thumbnail_id]',
      'input_value' => $thumbnail_id
    ) );

    //Get image for slide mobile
    // $returnHtml .= AEMIGenerateFieldsRepeat::generate_fields_image( array(
    //     'label' => 'Imagem Mobile',
    //     'input_name' => $prefixInputs . '[thumbnail_id_mobile]',
    //     'input_value' => $thumbnail_id_mobile
    // ));

    // $returnHtml .= '<div class="aemi-custom-fields__box">';
    //   $returnHtml .= '<div class="aemi-custom-fields__box__title">';
    //     $returnHtml .= '<label>' . __( 'Title', AEMI_TEXT_DOMAIN ). ':</label>';
    //   $returnHtml .= '</div>';
    //   $returnHtml .= '<div class="aemi-custom-fields__box__input-container">';
    //     $returnHtml .= '<input type="text" name="' . $prefixInputs . '[title]" class="aemi-custom-fields__box__input" value="' . $title . '" />';
    //   $returnHtml .= '</div>';
    // $returnHtml .= '</div>';

    // $returnHtml .= '<div class="aemi-custom-fields__box align-to-top">';
    //   $returnHtml .= '<div class="aemi-custom-fields__box__title">';
    //     $returnHtml .= '<label>' . __( 'Content', AEMI_TEXT_DOMAIN ). ':</label>';
    //   $returnHtml .= '</div>';
    //   $returnHtml .= '<div class="aemi-custom-fields__box__input-container">';
    //     $returnHtml .= '<textarea name="' . $prefixInputs . '[content]" class="aemi-custom-fields__box__input" rows="3">' . $content . '</textarea>';
    //   $returnHtml .= '</div>';
    // $returnHtml .= '</div>';

    // $returnHtml .= '<div class="aemi-custom-fields__box align-to-top">';
    //   $returnHtml .= '<div class="aemi-custom-fields__box__title">';
    //     $returnHtml .= '<label>' . __( 'Button text', AEMI_TEXT_DOMAIN ). ':</label>';
    //   $returnHtml .= '</div>';
    //   $returnHtml .= '<div class="aemi-custom-fields__box__input-container">';
    //     $returnHtml .= '<input type="text" name="' . $prefixInputs . '[button_text]" class="aemi-custom-fields__box__input" value="' . $button_text . '" />';
    //   $returnHtml .= '</div>';
    // $returnHtml .= '</div>';

    // $returnHtml .= '<div class="aemi-custom-fields__box align-to-top">';
    //   $returnHtml .= '<div class="aemi-custom-fields__box__title">';
    //     $returnHtml .= '<label>' . __( 'Button link', AEMI_TEXT_DOMAIN ). ':</label>';
    //   $returnHtml .= '</div>';
    //   $returnHtml .= '<div class="aemi-custom-fields__box__input-container">';
    //     $returnHtml .= '<input type="text" name="' . $prefixInputs . '[button_link]" class="aemi-custom-fields__box__input" value="' . $button_link . '" />';
    //   $returnHtml .= '</div>';
    // $returnHtml .= '</div>';

    return $returnHtml;
  }
}

new AemiHomeAdminFieldsAndSettings();