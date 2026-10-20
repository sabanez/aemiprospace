<?php

/**
 * Register endpoints ajax to save customer interests
 */
if ( !class_exists('AemiCustomerInterests') ) {
    class AemiCustomerInterests {

        public function __construct()
        {
            add_action( 'wp_ajax_set_aemi_customer_interests', array( $this, 'set_aemi_customer_interests' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
        }

        public function load_scripts()
        {
            wp_register_script( 'aemi-customer-interests', get_aemi_url_assets_js('core/interests.js') );
            wp_enqueue_script( 'aemi-customer-interests' );
            wp_localize_script('aemi-customer-interests', 'obj', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('ajax-nonce')
            ));
        }

        public function set_aemi_customer_interests()
        {
            if ( ! wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {
                wp_die ( 400);
            }

            $user = get_current_user_id();
            parse_str($_POST['data'], $data);
            $data = array_values($data);

            if (!empty($data)) {
                $data = serialize($data);
                update_user_meta($user, '_current_user_step_insterest', $data);

            } else {
                update_user_meta($user, '_current_user_step_insterest', '');

            }

            do_action( 'activecampaign_update_insterest', $user );
            wp_send_json(200);
        }
    }

    new AemiCustomerInterests();
}