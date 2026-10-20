<?php 

if( !class_exists('AEMITemplateContactFields') ){
  
  /**
   * Register custom fields of template fields
   */
  class AEMITemplateContactFields {

    var $nonce_name = 'aemi_template_contact_fields';
    var $nonce_action = 'as987qw54f6q8OFP';
    public static $meta_name = '_template_contact_page';
    
    function __construct(){
      add_action( 'add_meta_boxes', array( $this, 'register_metabox' ), 999, 2 );
      add_action( 'save_post', array( $this, 'save_metabox' ) );
    }

    /**
     * Register metabox
     */
    public function register_metabox( $post_type, $post ){

      if( $post_type == 'page' && $post ){

        $page_template = get_post_meta( $post->ID, '_wp_page_template', true );

        if( $page_template == 'template-page-contact.php' ){
        
          add_meta_box(
            'meta_box_template_contact_page',
            __( 'Contact page information', AEMI_TEXT_DOMAIN ),
            array( $this, 'render_meta_box' ),
            $post_type,
            'advanced',
            'high'
          );
        }
      }
    }

    /**
     * Save data current metabox
     */
    public function save_metabox( $postID ){

      if( 
        !isset( $_POST[$this->nonce_name] ) || 
        isset( $_POST[$this->nonce_name] ) && !wp_verify_nonce( $_POST[$this->nonce_name], $this->nonce_action )
      ){
        return $postID;
      }

      if( !isset( $_POST[self::$meta_name] ) ){
        return $postID;
      }

      $fields = $_POST[self::$meta_name];

      foreach ($fields as $key => $value) {
        $fields[$key] = wp_kses_post( $value );
      }

      //save datas
      update_post_meta( $postID, self::$meta_name, $fields );
    }

    /**
     * Render html of metabox
     */
    public function render_meta_box( $post ){
      wp_nonce_field( $this->nonce_action, $this->nonce_name );

      $data = [];
      
      if( $post && $post->ID ){
        $data = self::get_data( $post->ID );
      }

      //data saved to return in inputs
      $phone = ( $data && $data['phone'] ? esc_attr( $data['phone'] ) : '' );
      $whatsapp = ( $data && $data['whatsapp'] ? esc_attr( $data['whatsapp'] ) : '' );
      $form_shortcode = ( $data && $data['form_shortcode'] ? esc_attr( $data['form_shortcode'] ) : '' );
      $address = ( $data && $data['address'] ? esc_attr( $data['address'] ) : '' );
      $map_shortcode = ( $data && $data['map_shortcode'] ? esc_attr( $data['map_shortcode'] ) : '' );

      ?>
      <div class="aemi-custom-fields__container">

        <div class="aemi-custom-fields__box">
          <div class="aemi-custom-fields__box__title">
            <label><?php echo __( 'Phone', AEMI_TEXT_DOMAIN ); ?>:</label>
          </div>
          <div class="aemi-custom-fields__box__input-container">
            <div class="aemi-d-flex aemi-align-items-center">
              <input type="text" value="<?php echo $phone; ?>" name="<?php echo self::$meta_name; ?>[phone]" class="aemi-custom-fields__box__input" />
            </div>
          </div>
        </div>

        <div class="aemi-custom-fields__box">
          <div class="aemi-custom-fields__box__title">
            <label><?php echo __( 'WhatsApp', AEMI_TEXT_DOMAIN ); ?>:</label>
          </div>
          <div class="aemi-custom-fields__box__input-container">
            <div class="aemi-d-flex aemi-align-items-center">
              <input type="text" value="<?php echo $whatsapp; ?>" name="<?php echo self::$meta_name; ?>[whatsapp]" class="aemi-custom-fields__box__input" />
            </div>
          </div>
        </div>

        <div class="aemi-custom-fields__box">
          <div class="aemi-custom-fields__box__title">
            <label><?php echo __( 'Form short code', AEMI_TEXT_DOMAIN ); ?>:</label>
          </div>
          <div class="aemi-custom-fields__box__input-container">
            <div class="aemi-d-flex aemi-align-items-center">
              <input type="text" value="<?php echo $form_shortcode; ?>" name="<?php echo self::$meta_name; ?>[form_shortcode]" class="aemi-custom-fields__box__input" />
            </div>
          </div>
        </div>

        <div class="aemi-custom-fields__box">
          <div class="aemi-custom-fields__box__title">
            <label><?php echo __( 'Address', AEMI_TEXT_DOMAIN ); ?>:</label>
          </div>
          <div class="aemi-custom-fields__box__input-container">
            <div class="aemi-d-flex aemi-align-items-center">
              <input type="text" value="<?php echo $address; ?>" name="<?php echo self::$meta_name; ?>[address]" class="aemi-custom-fields__box__input" />
            </div>
          </div>
        </div>

        <div class="aemi-custom-fields__box">

          <div class="aemi-custom-fields__box__title">
            <label><?php echo __( 'Map short code (Iframe)', AEMI_TEXT_DOMAIN ); ?>:</label>
          </div>

          <div class="aemi-custom-fields__box__input-container">
            <div class="aemi-d-flex aemi-align-items-center">
              <input type="text" value="<?php echo $map_shortcode; ?>" name="<?php echo self::$meta_name; ?>[map_shortcode]" class="aemi-custom-fields__box__input" />
            </div>
          </div>

        </div>

      </div>
      <?php
    }

    /**
     * Return data of custom fields
     */
    public static function get_data( $postID ){
      $data = [];

      if( $postID && is_numeric( $postID ) ){
        $get_meta_data = get_post_meta( $postID, self::$meta_name, true );

        if( $get_meta_data ){
          $data = $get_meta_data;
        }
      }

      return $data;
    }
  }
  
  //new AEMITemplateContactFields();
}