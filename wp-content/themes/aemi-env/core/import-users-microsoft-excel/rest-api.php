<?php 

if( !class_exists( 'AemiMicrosoftExcelImportUserToWPRest' ) ){
	
	class AemiMicrosoftExcelImportUserToWPRest extends AemiMicrosoftExcelImportUserToWPBase {

        
        public function __construct(){
            add_action( 'rest_api_init', array( $this, 'register_custom_api' ) );
        }
        
        public function register_custom_api(){
            //Token
            register_rest_route( $this->rest_route__namespace, $this->rest_route__route . $this->rest_route__route_parameters, array(
                'methods' => $this->rest_route__methods,
                'callback' => array( $this, 'import_user' ),
              ) );
        }        

        public function import_user( WP_REST_Request $request ) {
            
            $this->unset_data_is_being_imported();

            //adding membership plugin compatibility
            $swpm_settings = get_option('swpm-settings');
            //end temp

            $params = $request->get_params();
            $return_data = array(
                'success' => false,
                'msg' => __( 'We were unable to process your request.', AEMI_TEXT_DOMAIN )
            );

            if( isset( $params['token_validation_rest'] ) && $params['token_validation_rest'] == $this->token_validation_rest ){
                //Preventing if there is already another import running not to process two at the same time.
                if( !$this->get_data_is_being_imported() ){

                    $current_user_to_run_process = ( isset( $_GET['current_user'] ) && $_GET['current_user'] ? $_GET['current_user'] : false );

                    //Informs that the process of importing users in progress
                    $this->set_data_is_being_imported();
                    $this->set_new_import_users_log( 'Started a new user import process', 'info', array(), $current_user_to_run_process );

                    
                    //get datas of users to import on plataform
                    $import_users = $this->get_microsoft_users_formatted();
                    $emails_and_passwords = array();

                    if( $import_users ){
                        $error_on_import_user = [];
                        $success_on_import_user = 0;

                        foreach ( $import_users as $key_import_loop => $user_to_import ) {
                            $meta_datas = false;
                            $user_password = $user_to_import['user_pass'];
                            $user_email = $user_to_import['user_email'];

                            if( isset( $user_to_import['meta_data'] ) && $user_to_import['meta_data'] ){
                                $meta_datas = $user_to_import['meta_data'];
                                unset($user_to_import['meta_data']);
                            }
                            
                            if( $user_password && $user_email ){
                                
                                //validate email
                                if( filter_var( $user_email, FILTER_VALIDATE_EMAIL ) ){

                                    $user_id = wp_insert_user( $user_to_import ) ;

                                    if ( ! is_wp_error( $user_id ) ) {
                                        $success_on_import_user++;
    
                                        if( $meta_datas ){
                                            foreach ( $meta_datas as $meta_key => $meta_value ) {
                                                update_user_meta( $user_id, $meta_key, $meta_value );
                                            }
                                        }
    
                                        //get email and password to send emails on the future with access
                                        $emails_and_passwords[$user_email] = array(
                                            'id' => $user_id,
                                            'email' => $user_email,
                                            'password' => $user_password
                                        );
    
                                    } else {
                                        $error_on_import_user[$user_email] = $user_id->get_error_message();
                                    }

                                    //Add custom actions to use primarily with active campaign integration
                                    if( $user_id && isset( $meta_datas['__aemi__imported_user_type_on_import'] ) && $meta_datas['__aemi__imported_user_type_on_import'] ){
                                        do_action('user_register__being_' . $meta_datas['__aemi__imported_user_type_on_import'], $user_id );


                                        //adding membership plugin compatibility
                                        if( $user_id && $swpm_settings ){
                                            if( 
                                                isset( $swpm_settings['enable-auto-create-swpm-members'] ) && $swpm_settings['enable-auto-create-swpm-members'] &&
                                                isset( $swpm_settings['free-membership-id'] ) && $swpm_settings['free-membership-id']
                                            ){
                                                do_action( 'activecampaign_swpm_update_plan_' . $swpm_settings['free-membership-id'], $user_id );
                                            }
                                        }
                                        //End

                                        //adding gamification compatibility
                                        if( function_exists( 'get_aemi_help_level_users_get_levels' ) ){
                                            $get_aemi_help_level_users_get_levels = get_aemi_help_level_users_get_levels();
                                        
                                            if( isset( $get_aemi_help_level_users_get_levels[0] ) ){
                                                $get_aemi_help_level_users_get_levels = (Array) $get_aemi_help_level_users_get_levels[0];
                                        
                                                if( isset( $get_aemi_help_level_users_get_levels['term_id'] ) && $get_aemi_help_level_users_get_levels['term_id'] ){
                                                    do_action( 'update_user_level_' . $get_aemi_help_level_users_get_levels['term_id'], $user_id );
                                                }
                                            }
                                        }
                                    }

                                } else {
                                    $error_on_import_user[$user_email] =  __( 'Invalid email.', AEMI_TEXT_DOMAIN );
                                }
                            }
                        }

                        $args_users_import = array(
                            'import_users_report' => array(
                                'success' => $success_on_import_user,
                                'error' => $error_on_import_user
                            )
                        );

                        $this->set_new_import_users_log( 'Users import completed successfully!', 'success', $args_users_import, $current_user_to_run_process );
                    } else {
                        $this->set_new_import_users_log( 'The import process ended but there were no new records to be imported.', 'success', array(), $current_user_to_run_process );
                    }
                    
                    $this->unset_data_is_being_imported();
                    $this->set_last_import_date();
                    $return_data['msg'] = __( 'Users import completed successfully!', AEMI_TEXT_DOMAIN );
                    $return_data['success'] = true;

                    //send emails to users
                    $this->send_emails_of_users( $emails_and_passwords );

                } else {

                    $return_data['msg'] = __( 'There is already an import process running, wait for the previous process to finish.', AEMI_TEXT_DOMAIN );
                }
            }

            return $return_data;
        }

        /**
         * Send emails of users
         */
        protected function send_emails_of_users( $groups ){
            if( $groups && count( $groups ) > 0 ){
                foreach( $groups as $user ){
                    if( isset( $user['id'] ) && isset( $user['password'] ) ){
                        $bodymail = $this->get_iugs_body_email_html_new_user_access( $user['id'], $user['password'] );

                        if( $bodymail ){
                			$get_user = get_user_by( 'id', $user['id'] );

                            if( $get_user ){

                                if( $get_user->data->user_email == $user['email'] ){
                                    wp_mail( $get_user->data->user_email, 'Accede a los datos del área de miembros de la AEMI', $bodymail, array('Content-Type: text/html; charset=UTF-8') );
                                }
                            }
                        }
                    }
                }
            }
        }
            
    }

    new AemiMicrosoftExcelImportUserToWPRest();
}