<?php 
if( !class_exists('AEMIAutoGenerateAdminTabs') ){
  class AEMIAutoGenerateAdminTabs {
    var $prefixTab = false;
    var $settings_tabs = array();
    var $tabs = array();
    var $renderHtml = '';
  
    public function __construct( $settings_tabs = array(), $prefix = false ){
      $this->settings_tabs = $settings_tabs;
      $this->prefix = ( $prefix ? $prefix : 'tab-'.substr( str_shuffle('q1w2e3r4t5y68u9ipalkdjfhgzmxncbv' . rand(0,1000) . '4qw5as534d83q41a3s8asdas5d41a4das6d'), 0, 10)  );
    }
  
    /**
     * Preper values to construct tabs elements
     */
    private function process_settings_tabs(){
      $i = 0;
      foreach( $this->settings_tabs as $setting_tab ){
        if( isset( $setting_tab['tab_name'] ) && $setting_tab['tab_name'] && isset( $setting_tab['tab_content'] ) && $setting_tab['tab_content'] ){
          $this->tabs[] = [
            'tab_name' => $setting_tab['tab_name'],
            'tab_id' => $this->prefix . '_' . $i,
            'tab_content' => $setting_tab['tab_content']
          ];
  
          $i++;
        }
      }
    }
  
    /**
     * Generate tabs HTML
     */
    private function generate_tabs(){
      if( $this->tabs ){
  
        $loop = 0;
        $this->renderHtml .= '<div class="aemi-admin-tabs-container">';
        
        foreach( $this->tabs as $tab ){
          $this->renderHtml .= '<label data-tabid="' . $loop . '" for="' . $tab['tab_id'] . '">' . esc_attr( $tab['tab_name'] ) . '</label>';
          $loop++;
        }
        
        $this->renderHtml .= '</div>';
      }
    }
  
    /**
     * Generate inputs controll tabs view
     */
    private function generate_inputs_control(){
      if( $this->tabs ){
        $loop = 0;
        foreach( $this->tabs as $tab ){
          $this->renderHtml .= '<input data-tabid="' . $loop . '" ' . ( $loop === 0 ? 'checked="true"' : '' ) . ' class="aemi-admin-tabs__current-tab-input" type="radio" name="' . $this->prefix . '" value="' . $tab['tab_id'] . '" id="' . $tab['tab_id'] . '">';
          $loop++;
        }
      }
    }
    
    /**
     * Generate content in tab view 
     */
    private function generate_content(){
      if( $this->tabs ){
  
        $loop = 0;
        $this->renderHtml .= '<div class="aemi-admin-tabs__content">';
        
        foreach( $this->tabs as $tab ){
          $this->renderHtml .= '<div  class="aemi-admin-tabs__content-inner" data-tabid="' . $loop . '">';
          $this->renderHtml .= $tab['tab_content'];
          $this->renderHtml .= '</div>';
          $loop++;
        }
        
        $this->renderHtml .= '</div>';
      }
    }
  
    /**
     * Process elements for the tab and retirn HTML rendered
     * 
     * @return String
     */
    public function run(){
      $this->process_settings_tabs();
  
      $this->generate_inputs_control();
      $this->generate_tabs();
      $this->generate_content();
  
      $this->renderHtml = '<div class="aemi-admin-tabs-controlls">' . $this->renderHtml . '</div>';
  
      return $this->renderHtml;
    }
  }
}