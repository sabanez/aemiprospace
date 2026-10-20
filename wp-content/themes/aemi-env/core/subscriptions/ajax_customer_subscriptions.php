<?php


/**
 * Register endpoints ajax to save customer interests
 */
if (!class_exists('AemiCustomerSubscriptions')) {
    class AemiCustomerSubscriptions
    {

        public function __construct()
        {
            add_action('wp_ajax_set_aemi_customer_subscriptions', array($this, 'set_aemi_customer_subscriptions'));
            add_action('wp_enqueue_scripts', array($this, 'load_scripts'));
        }

        public function load_scripts()
        {
            wp_register_script('aemi-customer-subscriptions', get_aemi_url_assets_js('core/subscriptions.js'));
            wp_enqueue_script('aemi-customer-subscriptions');
            wp_localize_script('aemi-customer-subscriptions', 'obj', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ajax-nonce')
            ));
        }

        public function set_aemi_customer_subscriptions()
        {
            if (!wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
                wp_die(400);
            }

            $user = get_current_user_id();
            parse_str($_POST['data'], $data);
            $data = array_values($data);
            $data_compare = array_values($data);


            if (!empty($data)) {
                $data = serialize($data);
                update_user_meta($user, 'customer_subscriptions', $data);

            } else {
                update_user_meta($user, 'customer_subscriptions', '');
            }

            foreach ( aemi_subscriptions_options() as $key => $value) {
                if( isset( $data_compare[0] ) && in_array( $key, $data_compare[0] ) ){
                    do_action( 'aemi_customer_subscriptions__'.$key.'_active', get_current_user_id() );
                } else{
                    do_action( 'aemi_customer_subscriptions__'.$key.'_cancel', get_current_user_id() );
                }
            }

            wp_send_json(200);
        }
    }

    new AemiCustomerSubscriptions();
}