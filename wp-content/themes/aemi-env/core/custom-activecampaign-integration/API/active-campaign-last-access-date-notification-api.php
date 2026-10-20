<?php

if( !class_exists('ActiveCampaignLastAccessDateNotificationAPI') ){

    class ActiveCampaignLastAccessDateNotificationAPI extends AemiCustomActiveCampaignIntegrationBase {

        protected $last_access_date_meta = 'aemi_active_campaign_last_access_date';
        /**
         * if equal to true in the database it means that the user has already been 
         * changed to inactive and then in wordpress it prevents extra queries in 
         * the query of inactive users to notify in active campaigning
         */
        protected $synchronized_inactive_user = 'aemi_active_campaign_synchronized_inactive_user';
    
    
        public function __construct() {
            add_action('rest_api_init', array($this, 'register_routes'));
            add_action( 'wp_login', array( $this, 'detect_login_and_register_last_date_access' ), 10, 2);
        }
    
        public function detect_login_and_register_last_date_access($user_login, $user){
            
            try {
                if( $user && $user->ID ){
    
                    if( $this->get_user_synchronized_inactive_user_meta( $user->ID ) == 'yes' ){
                        $contact = [
                            "email"     => $user->data->user_email,
                            'tags'      => [ $this->get_last_access_date_tag_name() ]
                        ];
                        AemiActiveCampaignIntegration::activecampaignApi( "contact/tag_remove", $contact );
                    }
    
                    update_user_meta( $user->ID, $this->last_access_date_meta, date('Y-m-d H:i:s') );
                    update_user_meta( $user->ID, $this->synchronized_inactive_user, 'no' );
                }
            } catch (Exception $e) {
            }
    
        }
    
        public function register_routes() {
            register_rest_route('aemi/activecampaign/v1', '/run_last_access_date_notification', array(
                'methods' => 'GET',
                'callback' => array($this, 'handle_last_access_notification'),
            ));
        }
    
        /**
         * This method is responsible for marking users who no longer access the platform as inactive in the active campaign
         */
        public function handle_last_access_notification( $request ) {
    
            $last_data_update = $this->get_option_meta_active_campaign_last_access_date_notification_api();
            $last_data_update = ( $last_data_update ? date('Y-m-d H:i', strtotime("+15 minutes", strtotime($last_data_update)) ) : false );
    
            if( $last_data_update == false || date('Y-m-d H:i') >= $last_data_update  ){
                $get_users_to_inactive = $this->get_users_to_inactive();
        
                if( $get_users_to_inactive ){
                    $get_last_access_date_tag_name = $this->get_last_access_date_tag_name();
                    foreach ( $get_users_to_inactive as $user ) {
                        
                        $contact = [
                            "email"     => $user->data->user_email,
                            'tags'      => $get_last_access_date_tag_name
                        ];
                        
                        AemiActiveCampaignIntegration::activecampaignApi( "contact/sync", $contact );
                        update_user_meta( $user->ID, $this->synchronized_inactive_user, 'yes' );
                    }
                }
        
                //update date last access
                $this->update_option_meta_active_campaign_last_access_date_notification_api();
    
                $response = array(
                    'message' => 'Data successfully processed!.',
                );
        
                return rest_ensure_response($response);
            } else {
        
                $response = array(
                    'message' => 'Please try to update again in a few minutes.',
                );
        
                return rest_ensure_response($response);
            }
        }
    
        /**
         * Returns the data to inactivate
         */
        public function get_users_to_inactive(){
    
            $args = array(
                'number' => 30, //For prevention every time cron is run it should bring 30 users disabled as a limit
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key'     => $this->last_access_date_meta,
                        'value'   => date('Y-m-d', strtotime('-' . $this->get_number_days_user_considered_inactive() . ' days')),
                        'compare' => '<',
                        'type'    => 'DATE',
                    ),
                    array(
                        'key'   => $this->synchronized_inactive_user,
                        'value' => 'no',
                    ),
                ),
            );
        
            $users = get_users( $args );
    
            return $users;
        }
    
        /**
         * Returns if the user has already been marked as sync on active
         * 
         * This prevents WordPress from running unnecessary queries on all users in the database.
         * 
         * @param Int $user_id
         * 
         * @return Bool
         */
        public function get_user_synchronized_inactive_user( $user_id = false ){
            if( $user_id ){
                return get_user_meta( $user_id, $this->synchronized_inactive_user, true );
            }
            return false;
        }
    
        /**
         * Returns the user's last login date
         * 
         * This value is saved to use as a query of the last access date.
         * 
         * @param Int $user_id
         * 
         */
        public function get_user_last_access_date_meta( $user_id = false ){
            if( $user_id ){
                return get_user_meta( $user_id, $this->last_access_date_meta, true );
            }
            return '';
        }
    
        /**
         * 
         * @param Int $user_id
         * 
         */
        public function get_user_synchronized_inactive_user_meta( $user_id = false ){
            if( $user_id ){
                return get_user_meta( $user_id, $this->synchronized_inactive_user, true );
            }
            return 'no';
        }
    
        public function update_option_meta_active_campaign_last_access_date_notification_api(){
            update_option( 'aemi_active_campaign_last_access_date_cron_run', date('Y-m-d H:i:s'));
        }
    
        public function get_option_meta_active_campaign_last_access_date_notification_api(){
            return get_option( 'aemi_active_campaign_last_access_date_cron_run', false);
        }
    
        /**
         * Returns the total number of days the system considers a user to be inactive
         */
        public function get_number_days_user_considered_inactive(){
            $data = $this->get_data();
            return isset( $data['last_access_date_notification']['number_days_user_considered_inactive'] ) ? $data['last_access_date_notification']['number_days_user_considered_inactive'] : 30;
        }
    
        public function get_last_access_date_tag_name(){
            $data = $this->get_data();
            return isset( $data['last_access_date_notification']['tag_name'] ) ? $data['last_access_date_notification']['tag_name'] : 'last_access_date';
        }
    }
    
    $activeCampaignLastAccessDateNotificationAPI = new ActiveCampaignLastAccessDateNotificationAPI();
}