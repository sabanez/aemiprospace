<?php 

if( !function_exists('aemi_user_can_view_pro_content') ){
    /**
     * Checks if the user has access to the content
     */
    function aemi_user_can_view_pro_content( $post_id, $user_id = false ){
        if( !$user_id ){
            $user_id = get_current_user_id();
        }

        if( $user_id ){
            $content_for_subscribers = get_post_meta( $post_id, '_content_for_subscribers', true );

            //current content is pro
            if( $content_for_subscribers ){

                //current user is premium
                if( aemi_current_user_is_professional( $user_id ) !== false ){
                    return true;
                }

            } else {
                //content is free
                return true;
            }
        }

        return false;
    }
}

if( !function_exists('aemi_current_user_is_professional')){
    /**
     * Check if the current user is a premium user
     */
    function aemi_current_user_is_professional(){
        // if( !$user_id ){
        //     $user_id = get_current_user_id();
        // }

        //temporariamente é possível alterar o modo usuário premium pelo parametro na url
        //no futuro vamos pegar esses dados pelo plugin de usuário premium.
        // if( isset( $_GET['current_user_premium'] ) && $_GET['current_user_premium'] ){
        //     return true;
        // }

        if( current_user_can('administrator') ){
            return true;
        }

        if( class_exists('SwpmAuth') ){
            /**
            * Adicionando compatibilidade com o plugin wp simple membership
            */
            $auth = SwpmAuth::get_instance();
            $user_data = (array) AEMICustomizeSimpleMembership__CustomPageInWoo::get_current_membership_plan_info();

            //id dois significa que é usuário gratuito
            if( $user_data['membership_level'] != 2 && $user_data['account_state'] == 'active' ){
                return true;
            }
        }

        return false;
    }
}


if( !function_exists('aemi_get_landing_page_premium_account') ){
    /**
     * Returns the url of the landing page where you have premium account information
     */
    function aemi_get_landing_page_premium_account(){
        return get_page_url_subscription_buy();
    }
}


if( !function_exists('get_page_url_subscription_buy') ){
    function get_page_url_subscription_buy(){

        $get_posts = get_posts(array(
            'posts_per_page' => 1,
            'post_type' => 'page',
            'meta_query' => array(
                array(
                    'key'     => '_wp_page_template',
                    'value'   => 'template-page-subscription.php',
                    'compare' => '='
                )
            )
        ));

        if( $get_posts && $get_posts[0] ){
            return get_permalink( $get_posts[0]->ID );
        }
        
        return '';
    }
}