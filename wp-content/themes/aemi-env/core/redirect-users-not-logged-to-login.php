<?php 

/**
 * Redirects not logged in users
 */
add_action('template_redirect', function(){
    if( !is_admin() && !is_user_logged_in() && !is_account_page() && !is_page_template('template-page-register.php') && !is_page_template('template-page-available-to-non-logged-in-users.php') && apply_filters('aemi_custom_template_redirect', true) ){
        $redirect_r = '';
        if( !is_home() ){
            $redirect_r = '?redirect_to=' . urlencode( "http" . (isset($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
        }
        header('Location: ' . get_aemi_my_account_url() . $redirect_r );
        exit();
    }
});