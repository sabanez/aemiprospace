<?php
function aemi_add_gamification_custom_my_account_page_endpoint()
{
    add_rewrite_endpoint('gamification', EP_ROOT | EP_PAGES);
}

add_action('init', 'aemi_add_gamification_custom_my_account_page_endpoint');

function aemi_gamification_custom_my_account_page_query_vars($vars)
{
    $vars[] = 'gamification';

    return $vars;
}

add_filter('query_vars', 'aemi_gamification_custom_my_account_page_query_vars', 0);

function aemi_gamification_custom_my_account_page_content()
{
    global $aemiHelpLevelUsersClassLoaded;
    ?>
    
    <?php
}
add_action('woocommerce_account_gamification_endpoint', 'aemi_gamification_custom_my_account_page_content');
