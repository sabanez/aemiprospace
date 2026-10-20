<?php

/**
 * Register endpoints ajax to save customer interests
 */
if ( !class_exists('AemiCustomerInterests') ) {
    class AemiCustomerInterests {

        static $aemi_customer_interest_nonce_name = 'aemi_customer_interests';
        static $aemi_customer_interest_nonce_action = 'AemiCustomerInterests';

        public static function get_wp_input_nonce_field(){
            return wp_nonce_field( self::$aemi_customer_interest_nonce_action, self::$aemi_customer_interest_nonce_name, true, false );
        }
        
        public static function set_aemi_customer_interests( $user_id = false ) {
          if( isset( $_POST[self::$aemi_customer_interest_nonce_name] ) && wp_verify_nonce( $_POST[self::$aemi_customer_interest_nonce_name], self::$aemi_customer_interest_nonce_action ) && $user_id ){

                $interests = [];
                if( isset( $_POST['interests_option'] ) && $_POST['interests_option'] ){
            
                    if( isset( $_POST['interests_option'] ) && $_POST['interests_option'] ){
                        foreach ($_POST['interests_option'] as $interest ){
                            if( is_numeric( sanitize_text_field( $interest ) ) ) {
                                $interests[] = sanitize_text_field( $interest );
                            }
                        }
                    }
                }

                update_user_meta( $user_id, '_current_user_step_insterest', $interests );

                do_action( 'activecampaign_update_insterest', $user_id );

                return true;
            }

            return false;
        }
    }
}