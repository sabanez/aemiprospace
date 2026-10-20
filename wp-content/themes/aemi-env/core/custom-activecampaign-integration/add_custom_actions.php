<?php

if( !class_exists('AemiActiveCampaignIntegration') ){
    class AemiActiveCampaignIntegration extends AemiCustomActiveCampaignIntegrationBase
    {
        public $actions_datas = [];

        public function __construct(){
            add_action( 'profile_update', [ $this, 'update_or_register_custom_user_data' ] );
            add_action( 'aemi_gamification_and_content_level_after_upate_user', [ $this, 'update_user_level_points' ] );
            add_action( 'user_register', [ $this, 'update_or_register_custom_user_data' ] );
            add_action( 'activecampaign_update_insterest', [ $this, 'activecampaign_update_insterest'] );
            add_action( 'activecampaign_update_insterest_type', [ $this, 'activecampaign_update_insterest_type'] );
            add_action( 'activecampaign_update_last_access', [ $this, 'update_last_access'] );
            $getDataInterest = $this->get_data();

            if( $getDataInterest ){
                foreach ( $getDataInterest as $tabs) {
                    foreach ( $tabs as $actions => $actions_data ) {
                        $this->actions_datas[$actions] = $actions_data;
                        add_action( $actions, [ $this, 'run_generic_actions_activecampaign' ] );
                    }
                }
            }
        }

        static function getAllListsActiveCampaign(){
            
            $get_list = self::activecampaignApi("list/list_", ['ids'  => 'all' ]);

            if( $get_list ){
                $get_list = (Array) $get_list;
                
                if(
                    isset( $get_list['success'] ) && $get_list['success'] == '1' &&
                    isset( $get_list['http_code'] ) && $get_list['http_code'] == '200'
                ){
                    $get_return_list = [];

                    foreach ( $get_list as $key => $value ) {
                        if( is_object( $value ) && $value ){
                            $value = (Array) $value;
                
                            if( is_array( $value ) && $value ){
                
                                if(
                                    isset( $value['id'] ) && $value['id'] &&
                                    isset( $value['name'] ) && $value['name']
                                ){
                
                                    $get_return_list[] = array(
                                        'id' => $value['id'],
                                        'name' => $value['name'],
                                    );  
                                }
                            }
                        }
                    }
                    
                    if( $get_return_list ){
                        return $get_return_list;
                    }
                }
            }

           return [];
        }

        static function custom_logger($message) {
            // Caminho para o arquivo de log
            $log_file = WP_CONTENT_DIR . '/custom-activecampaign-api.log';
        
            // Formato da mensagem de log
            $log_message = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        
            // Escrever a mensagem de log no arquivo
            error_log($log_message, 3, $log_file);
        }

        /**
         * Active sync activecampaingn
         */
        static function activecampaignApi( $endpoint = '', $args = array() ){
            if( $endpoint && $args){
                $aemi_get_instance_active_campaign = aemi_get_instance_active_campaign();
                
                if( $aemi_get_instance_active_campaign ){
                    $returnApi = aemi_get_instance_active_campaign()->api( $endpoint, $args );
                    $extraLog = ' | endpoint: ' . $endpoint . ( isset( $args['email'] ) ? ' | email: ' . $args['email'] : '' );

                    if( is_string( $returnApi ) ){
                        self::custom_logger($returnApi . $extraLog);
                    } else if( is_object( $returnApi ) ){
                        try {
                            if( !$returnApi->success ){
                                $argsTemp = (array) $returnApi;
                                $message = 'Succes: false | http_code: ' . ( isset( $argsTemp['http_code'] ) ? $argsTemp['http_code'] : '' ) . ' | subscriber_id: ' . ( isset( $argsTemp['subscriber_id'] ) ? $argsTemp['subscriber_id'] : '' ) . ' | result_code: ' . ( isset( $argsTemp['result_code'] ) ? $argsTemp['result_code'] : '' ) . ' | result_message: ' . ( isset( $argsTemp['result_message'] ) ? $argsTemp['result_message'] : '' );
                                self::custom_logger($message . $extraLog);
                            }
                        } catch (Exception $e) {
                            self::custom_logger('Exceção capturada: ' . $e->getMessage());
                        }
                    }

                    return $returnApi;
                }
            }

            return false;
        }

        /**
         * Add user in list id from user email activecampaingAPI
         */
        public function add_user_to_list( $email = '', $lists = [] ){
            if( $email && $lists ){
                if( filter_var( $email, FILTER_VALIDATE_EMAIL ) ){
                    if( is_array( $lists ) && $lists ){

                        $ar_add_list = array(
                            "email"     => $email,
                        );

                        foreach( $lists as $list_id ){
                            $ar_add_list['p[' . $list_id . ']'] = $list_id;
                            $ar_add_list['status[' . $list_id . ']'] = '1';
                        }

                        self::activecampaignApi( "contact/sync", $ar_add_list );
                    }
                }
            }
        }

        /**
         * Remove user in list id from user email activecampaingAPI
         */
        public function remove_user_to_list( $email = '', $lists = [] ){
            if( $email && $lists ){
                if( filter_var( $email, FILTER_VALIDATE_EMAIL ) ){
                    if( is_array( $lists ) && $lists ){

                        $ar_remove_list = array(
                            "email"     => $email,
                        );

                        foreach( $lists as $list_id ){
                            $ar_remove_list['p[' . $list_id . ']'] = $list_id;
                            $ar_remove_list['status[' . $list_id . ']'] = '2';
                        }

                        self::activecampaignApi( "contact/sync", $ar_remove_list );
                    }
                }
            }
        }

        /**
         * Add tags from user email activecampaingAPI
         */
        public function add_tag_to_user( $email = '', $tags = [] ){
            if( $email && $tags ){
                if( filter_var( $email, FILTER_VALIDATE_EMAIL ) ){
                    if( is_array( $tags ) && $tags ){

                        $ar_add_tag = array(
                            "email"     => $email,
                            'tags'      => $tags,
                        );

                        self::activecampaignApi( "contact/tag_add", $ar_add_tag );
                    }
                }
            }
        }
        /**
         * remove tags from user email activecampaingAPI
         */
        public function remove_tag_to_user( $email = '', $tags = [] ){
            if( $email && $tags ){
                if( filter_var( $email, FILTER_VALIDATE_EMAIL ) ){
                    if( is_array( $tags ) && $tags ){

                        $ar_remove_tag = array(
                            "email"     => $email,
                            'tags'      => $tags,
                        );

                        self::activecampaignApi( "contact/tag_remove", $ar_remove_tag );
                    }
                }
            }
        }

        /**
         * Return user email by ID
         */
        private function get_email_by_user_id( $user_id = false ){
            if( $user_id ){
                $get_user = get_user_by('id', $user_id);

                if( $get_user ){
                    return $get_user->data->user_email;
                }
            }

            return false;
        }

        public function update_last_access( $user_id ){

            if( $user_id ){
                $get_user = get_user_by('id', $user_id);

                if( $get_user ){
                    $last_access = get_user_meta($user_id, 'user_last_access', true);

                    if( $last_access ){
                        //Prevent register not exists
                        $this->update_or_register_custom_user_data($user_id);

                        $contact = array(
                            "email"                 => $get_user->data->user_email,
                            "field[%LASTACCESS%,0]" => $last_access,
                        );
                        
                        self::activecampaignApi( "contact/sync", $contact );
                    }
                }
            }
        }

        /**
         * When you see a user record or data update, it will also be updated without an active campaign
         */
        public function update_or_register_custom_user_data( $user_id ){

            if( $user_id ){
                $get_user = get_user_by('id', $user_id);

                if( $get_user ){
                    
                    $first_name = get_user_meta( $user_id, 'first_name', true );
                    $last_name = get_user_meta( $user_id, 'last_name', true );

                    $current_user_name = sanitize_text_field( $first_name . ( $first_name ? ' ' : '' ) . $last_name );
                    if( !$current_user_name ){
                        $current_user_name = sanitize_text_field( $get_user->data->display_name );
                    }

                    $current_user_name = explode( ' ', $current_user_name );

                    $f_name = $current_user_name[0];
                    $l_name = '';

                    unset( $current_user_name[0] );

                    if( $current_user_name ){
                        $l_name = implode( ' ', $current_user_name );
                    }
					
					if( $current_user_name ){
    $l_name = implode( ' ', $current_user_name );
}


                    $contact = array(
                        "email"              => $get_user->data->user_email,
                        "first_name"         => $f_name,
                        "last_name"          => $l_name
                    );

                    self::activecampaignApi( "contact/sync", $contact );

                    if( $user_id ){
                        $this->update_user_level_points( $user_id );
                    }
                }
            }
        }


        /**
         * Generic filters users
         */
        public function run_generic_actions_activecampaign( $user_id = false ){
            $user_email = $this->get_email_by_user_id( $user_id );

            if( $user_email ){
                $get_current_actions = current_action();
                
                if( isset( $this->actions_datas[$get_current_actions] ) && $this->actions_datas[$get_current_actions] ){
                    $data_action = $this->actions_datas[$get_current_actions];

                    $tags_add = array();
                    $tags_remove = array();
                    $list_add = array();
                    $list_remove = array();

                    if( isset( $data_action['tags_add'] ) && $data_action['tags_add'] ){
                        $tags_add = explode(',', trim( $data_action['tags_add'] ) );
                    }

                    if( isset( $data_action['tags_remove'] ) && $data_action['tags_remove'] ){
                        $tags_remove = explode(',', trim( $data_action['tags_remove'] ) );
                    }

                    if( isset( $data_action['list_add'] ) && $data_action['list_add'] ){
                        $list_add = $data_action['list_add'];
                    }

                    if( isset( $data_action['list_remove'] ) && $data_action['list_remove'] ){
                        $list_remove = $data_action['list_remove'];
                    }

                    if( is_array( $tags_add ) && $tags_add ){
                        $this->add_tag_to_user( $user_email, $tags_add );
                    }

                    if( is_array( $tags_remove ) && $tags_remove ){
                        $this->remove_tag_to_user( $user_email, $tags_remove );
                    }

                    if( is_array( $list_add ) && $list_add ){
                        $this->add_user_to_list( $user_email, $list_add );
                    }

                    if( is_array( $list_remove ) && $list_remove ){
                        $this->remove_user_to_list( $user_email, $list_remove );
                    }
                }
            }
        }

        /**
         * Atualize a pontuação (level) atual no active campaing em um campo personalizado se o usuário
         */
        public function update_user_level_points( $user_id = false ){
            if( $user_id ){
                $email = $this->get_email_by_user_id( $user_id );

                if( $email ){
                    if( filter_var( $email, FILTER_VALIDATE_EMAIL ) ){
                        global $aemiHelpLevelUsersClassLoaded;
                        $ar_add_list = array(
                            "email"             => $email,
                            'field[%LEVEL_POINTS%,0]' => $aemiHelpLevelUsersClassLoaded->getSpecificUserScore( $user_id )
                        );
                        
                        self::activecampaignApi( "contact/sync", $ar_add_list );
                    }
                }
            }
        }


        /**
         * INTEREST
         */
        public function activecampaign_update_insterest( $user_id = false ){
            $user_email = $this->get_email_by_user_id( $user_id );

            if( $user_email ){
                $data = get_user_meta( $user_id, '_current_user_step_insterest', true );
                $interests = get_interest_list_for_users( [], ['slug']);

                $user_interests = [];
                $remove_interests = [];

                if( $interests && is_array( $data ) ){
                    foreach ( $interests as $interests_key => $interests_value ) {
                        if( in_array( $interests_value['id'], $data ) ){
                            $user_interests[] = $this->interest__prefix . $interests_value['slug'];
                        } else {
                            $remove_interests[] = $this->interest__prefix . $interests_value['slug'];
                        }
                    }

                    if( $user_interests ){
                        $this->add_tag_to_user( $user_email, $user_interests );
                    }

                    if( $remove_interests ){
                        $this->remove_tag_to_user( $user_email, $remove_interests );
                    }
                }

            }
        }

        /**
         * INTEREST
         */
        public function activecampaign_update_insterest_type( $user_id = false ){
            $user_email = $this->get_email_by_user_id( $user_id );

            if( $user_email ){
                if( !is_admin() ){
                    $user_meta_user_interest_type = get_user_meta( $user_id, AEMIFirstAccessLoginStepsModal::$user_meta_user_interest_type, true );
                    $current_interest_type_add = [ 'goal_' . strtr( $user_meta_user_interest_type, [
                        'concrete_case' => 'clinical_case',
                        'general_interest' => 'learning',
                        'both' => 'both'
                    ]) ]; 
                    $current_interest_type_remove = []; 
                    $general_types = AEMIFirstAccessLoginStepsModal::get_interests_type();
                    
                    if( $general_types ){
                        
                        foreach ( $general_types as $key => $value) {
                            //old version
                            $current_interest_type_remove[] = 'interest_type_' . $key;
                            //new version
                            $current_interest_type_remove[] = 'goal_' . strtr( $key, [
                                'concrete_case' => 'clinical_case',
                                'general_interest' => 'learning',
                                'both' => 'both'
                            ]);
                        }
                    }

                    if( $current_interest_type_remove ){
                        $this->remove_tag_to_user( $user_email, $current_interest_type_remove );
                    }

                    if( $current_interest_type_add ){
                        $this->add_tag_to_user( $user_email, $current_interest_type_add );
                    }
                    
                }
            }
        }

        /**
         * Return 
         */
    }
    
    new AemiActiveCampaignIntegration();
}
