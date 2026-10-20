<?php 

/**
 * On update plan by webhook
 */
add_action( 'activecampaign_swpm_update_plan', function( $user_id ){
    $user_id     = absint( $user_id );
    $user_data = get_user_by('id', $user_id);

    if( $user_data ){
        if( $user_data->data ){
            $user_data = $user_data->data;
            if( $user_data ){
                $user = SwpmMemberUtils::get_user_by_email( $user_data->user_email );

                if( $user ){
                    $user = (Array) $user;

                    $opts = get_option( 'swpm-settings' );

                    if( isset( $user['account_state'] ) && $user['account_state'] == 'active' ){
                        do_action( 'activecampaign_swpm_update_plan_' . $user['membership_level'], $user_id );
                    } else if( isset( $opts['free-membership-id'] ) && $opts['free-membership-id'] ) {
                        do_action( 'activecampaign_swpm_update_plan_' . $opts['free-membership-id'], $user_id );
                    }
                }
            }
            
        }
    }
}, 999 );

/**
 * On update manualy user
 */
add_action( 'swpm_admin_end_edit_complete_user_data', function( $member ){

    if( isset( $member['email'] ) && $member['email'] != '' &&  isset( $member['membership_level'] ) && $member['membership_level'] != '' ){
        $user_data = get_user_by( 'email', $member['email'] );
    
        if( $user_data ){
            if( $user_data->data ){
                $user_data = $user_data->data;
                if( $user_data ){
                    $user = SwpmMemberUtils::get_user_by_email( $user_data->user_email );
    
                    if( $user ){
                        $user = (Array) $user;
    
                        $opts = get_option( 'swpm-settings' );
                        
                        if( isset( $user['account_state'] ) && $user['account_state'] == 'active' ){
                            do_action( 'activecampaign_swpm_update_plan_' . $member['membership_level'], $user_data->ID );
                        } else if( isset( $opts['free-membership-id'] ) && $opts['free-membership-id'] ) {
                            do_action( 'activecampaign_swpm_update_plan_' . $opts['free-membership-id'], $user_data->ID );
                        }
                    }
                }
                
            }
        }
    }
}, 999 );

/**
 * cancel subscription by API
 */
add_action('swpm_subscription_payment_cancelled', function( $ipn_data ){
    if( isset( $ipn_data['member_id'] ) ){
        $returnEmail = SwpmMemberUtils::get_member_field_by_id( $ipn_data['member_id'], 'email', false );

        if( isset( $returnEmail ) && $returnEmail ){
            $user_data = get_user_by( 'email', $returnEmail );
    
            if( $user_data ){
                if( $user_data->data ){
                    $user_data = $user_data->data;
                    if( $user_data ){
                        do_action( 'activecampaign_swpm_cancel_plan', $user_data->ID );
                    }
                }
            }
        }
    }
}, 999);