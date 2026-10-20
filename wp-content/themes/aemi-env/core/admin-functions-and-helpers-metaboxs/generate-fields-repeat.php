<?php

class AEMIGenerateFieldsRepeat{

  var $button_add_more = '';
  var $html_pre_generated = '';
  var $html_to_replace = '';
  var $html_to_return = '';
  var $id = '';
  static $base_file_script = 'core/admin/repeater-box-js.js';
  static $loaded_script_in_footer = false;

  /**
   * Define basic instructions to working class
   * 
   * @param {String} $html_to_replace
   * @param {Array} $html_pre_generated
   * @param {Array} $button_add_more
   * @param {String} $id
   */
  function __construct( $html_to_replace = '', $html_pre_generated = array(), $button_add_more = false, $id = false ){
    $this->button_add_more = ( $button_add_more ? $button_add_more : __( 'Add more', AEMI_TEXT_DOMAIN ) );
    $this->html_to_replace = $html_to_replace;
    $this->html_pre_generated = $html_pre_generated;
    $this->id = ( $id ? $id : 'repeat_fields_'.substr( str_shuffle('q1w2e3r4t5y68u9ipalkdjfhgzmxncbv' . rand(0,1000) . '4qw5as534d83q41a3s8asdas5d41a4das6d'), 0, 10)  );    
  }

  /**
   * verify script has loaded
   */
  static function has_loaded_script_in_footer(){
    return self::$loaded_script_in_footer;
  }

  /**
   * Update scripts to footer in loader
   */
  static function update_loaded_script_in_footer(){
    self::$loaded_script_in_footer = true;
  }

  /**
   * Load Script to use thumbnail gallery
   */
  static function load_script_thumbnail(){
    if( AEMIGenerateFieldsRepeat::has_loaded_script_in_footer() == false ){
      wp_enqueue_media();
      wp_enqueue_script('generate-fields-repeat-load-media-enqueue-scripts', get_aemi_url_assets_js(self::$base_file_script), array('jquery'));
      AEMIGenerateFieldsRepeat::update_loaded_script_in_footer();
    }
  }

  /**
   * Responsible for returning a field already prepared to select an image in the wordpress media gallery
   * 
   * @param Array $options
   */
  static function generate_fields_image( $options = array() ){
    $label = ( isset( $options['label'] ) && $options['label'] ? $options['label'] : __( 'Image', AEMI_TEXT_DOMAIN ) );
    $input_name = ( isset( $options['input_name'] ) && $options['input_name'] ? esc_attr( $options['input_name'] ) : '' );
    $input_value = ( isset( $options['input_value'] ) && $options['input_value'] ? esc_attr( $options['input_value'] ) : '' );
    $class_container = ( isset( $options['class_container'] ) && $options['class_container'] ? esc_attr( $options['class_container'] ) : 'aemi-custom-fields__box align-to-top' );
    $class_label_container = ( isset( $options['class_label_container'] ) && $options['class_label_container'] ? esc_attr( $options['class_label_container'] ) : 'aemi-custom-fields__box__title' );
    $class_label = ( isset( $options['class_label'] ) && $options['class_label'] ? esc_attr( $options['class_label'] ) : '' );
    $class_container_image = ( isset( $options['class_container_image'] ) && $options['class_container_image'] ? esc_attr( $options['class_container_image'] ) : 'aemi-custom-fields__box__input-container' );
    $class_container_input = ( isset( $options['class_container_input'] ) && $options['class_container_input'] ? esc_attr( $options['class_container_input'] ) : 'aemi-custom-fields__box__input' );
    $description = ( isset( $options['description'] ) && $options['description'] ? $options['description'] : '' );

    $thumbnail_url = '';

    if( $input_value && is_numeric( $input_value ) ){
      $get_thumbnail = wp_get_attachment_image_src( $input_value, 'medium' );

      if( $get_thumbnail && $get_thumbnail[0] ){
        $thumbnail_url = $get_thumbnail[0];
      }
    }

    $returnHtml = '<div class="' . $class_container . ' aemi-boxs-repeat__change-image-container" data-buttontext="' . __('Use this image', AEMI_TEXT_DOMAIN) . '" data-title="' . __('Change image', AEMI_TEXT_DOMAIN) . '">';
      $returnHtml .= '<div class="' . $class_label_container . ' aemi-boxs-repeat__change-image-label-container">';
        $returnHtml .= '<label class="' . $class_label . ' aemi-boxs-repeat__change-image-label">' . $label . ':</label>';
      $returnHtml .= '</div>';
      $returnHtml .= '<div class="' . $class_container_image . ' aemi-boxs-repeat__change-image-inner">';
        $returnHtml .= '<div class="aemi-boxs-repeat__change-image-content">';
          $returnHtml .= '<div class="aemi-boxs-repeat__change-image-thumbnail' . ( $thumbnail_url ? ' aemi-boxs-repeat__change-image-thumbnail-selected' : '' ) . '" ' . ( $thumbnail_url ? 'style="background-image: url(\'' . $thumbnail_url . '\')"' : '' ) . '>';
            $returnHtml .= '<button type="button" class="aemi-boxs-repeat__change-image-btn-remove"><span class="dashicons dashicons-no"></span></button>';
          $returnHtml .= '</div>';

          if( $description ) $returnHtml .= '<div class="aemi-boxs-repeat__change-image-description">' . $description . '</div>';

          $returnHtml .= '<button type="button" class="button button-primary aemi-boxs-repeat__change-image-btn">' . __('Select thumbnail', AEMI_TEXT_DOMAIN) . '</button>';
          $returnHtml .= '<input type="hidden" name="' . $input_name . '" class="' . $class_container_input . ' aemi-boxs-repeat__change-image-input" value="' . $input_value . '" />';
        $returnHtml .= '</div>';
      $returnHtml .= '</div>';
    $returnHtml .= '</div>';

    return $returnHtml;
  }

  /**
   * Add the html that will be used as a basis for replicating the fields in repeat as a jaavascript variable
   * 
   * to use some value that changes with each new loop use the word "AEMIGenerateFieldsRepeatLoopID"
   * 
   * ex: test[AEMIGenerateFieldsRepeatLoopID][content]
   *     In loop replace to: test[0][content] | test[1][content] | test[3][content] | Loop etc 
   * 
   * after rendered use the number [0], [1], [2] etc.. loop or [AEMIGenerateFieldsRepeatLoopID]
   * 
   * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
   *                    ATTENTION
   * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
   * By default the loop system will use the primary numeric value in the name 
   * attribute to consider the key value that will change when moving or adding a new item in the repeat value
   */
  private function add_elements_to_replace(){
    add_action('admin_footer', function(){

      //insert script in footer to work repeat fields
      //and prevent multiples loaded scripts
      if( AEMIGenerateFieldsRepeat::has_loaded_script_in_footer() == false ){
        echo '<script src="' . get_aemi_url_assets_js(self::$base_file_script) . '"></script>';
        //set script to loaded
        AEMIGenerateFieldsRepeat::update_loaded_script_in_footer();
      }
      //insert var releated to fields of repeat 
      echo "<script>var aemiGenerateFieldsRepeat_" . $this->id . " = \"" . strtr( $this->html_to_replace, array('"' => '&quot;') ) . "\";</script>\n";
    });
  }

  /**
   * This method will generate the previously rendered fields 
   */
  public function generate_html_values(){
    $html_return = '';
    
    if( $this->html_pre_generated ){
      $loop = 1;
      foreach ($this->html_pre_generated as $html_rendered) {
        $html_return .= '<div class="aemi-boxs-repeat">';
          $html_return .= '<div class="aemi-boxs-repeat__number"><span class="aemi-boxs-repeat__number-text">' . $loop . '</span></div>';
          $html_return .= '<div class="aemi-boxs-repeat__content">' . strtr( $html_rendered, array( '[AEMIGenerateFieldsRepeatLoopID]' => '['.( $loop - 1 ).']' )) . '</div>';
          $html_return .= '<div class="aemi-boxs-repeat__options">';
            $html_return .= '<button type="button" class="aemi-boxs-repeat__remove-element">';
              $html_return .= '<span class="dashicons dashicons-trash"></span>';
            $html_return .= '</button>';
          $html_return .= '</div>';
        $html_return .= '</div>';

        $loop++;
      }
    }

    return $html_return;
  }

  /**
   * Process, generate and return html
   */
  public function run(){
    $this->add_elements_to_replace();

    $this->html_to_return .= '<div class="aemi-boxs-repeat-container" id="' . $this->id . '">';
      $this->html_to_return .= '<div class="aemi-boxs-repeat__elements">' . $this->generate_html_values() . '</div>';
      $this->html_to_return .= '<div class="aemi-boxs-repeat__buttons-container">';
        $this->html_to_return .= '<button type="button" class="button button-primary button-large aemi-btn-add-more-section-repeat">' . $this->button_add_more . '</button>';
      $this->html_to_return .= '</div>';
    $this->html_to_return .= '</div>';

    return $this->html_to_return;
  }
}
