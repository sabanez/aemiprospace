<?php 
function aemi_override_address_placeholders( $address_fields ) {
    $address_fields['address_1']['placeholder'] = '';

    return $address_fields;
}

add_filter( 'woocommerce_default_address_fields', 'aemi_override_address_placeholders', 10, 1 );



/**
 * Subscriptions
 */
function aemi_add_subscriptions_endpoint() {
    add_rewrite_endpoint( 'subscriptions', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'aemi_add_subscriptions_endpoint' );

function aemi_subscriptions_query_vars( $vars ) {
    $vars[] = 'subscriptions';
    return $vars;
}
add_filter( 'query_vars', 'aemi_subscriptions_query_vars', 0 );

function aemi_subscriptions_content() {
    include get_template_directory() . '/woocommerce/myaccount/subscriptions.php';
}

add_action( 'woocommerce_account_subscriptions_endpoint', 'aemi_subscriptions_content' );

function aemi_subscriptions_options(){
    return [
        'newsletter_mensual' => [
            'id' => 'newsletter_mensual',
            'name' => __( 'Newsletter mensual', AEMI_TEXT_DOMAIN )
        ],
        'newsletter_formaciones' => [
            'id' => 'newsletter_formaciones',
            'name' => __( 'Recordatorios formaciones', AEMI_TEXT_DOMAIN )
        ],
//        'recordation_helpmi' => [
//            'id' => 'recordation_helpmi',
//            'name' => __( 'Recordatorio HelpMI', AEMI_TEXT_DOMAIN )
//        ],
        'newsletter_icomi' => [
            'id' => 'newsletter_icomi',
//            'name' => __( 'Newsletter ICoMI - Congreso Internacional de MI', AEMI_TEXT_DOMAIN )
            'name' => __( 'Newsletter ICoMI - Congreso Internacional de Microinmunoterapia', AEMI_TEXT_DOMAIN )
        ]
    ];
}


/**
 * Interests
 */
function aemi_add_interests_endpoint() {
    add_rewrite_endpoint( 'interests', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'aemi_add_interests_endpoint' );

function aemi_interests_query_vars( $vars ) {
    $vars[] = 'interests';
    return $vars;
}
add_filter( 'query_vars', 'aemi_interests_query_vars', 0 );

function aemi_interests_content() {
    //include get_template_directory() . '/woocommerce/myaccount/interests.php';

    wc_get_template('myaccount/interests.php');
}
add_action( 'woocommerce_account_interests_endpoint', 'aemi_interests_content' );
/** End Interests */

/**
 * Order Menu Items in My Account Page
 * @return array
 */
function aemi_reorder_my_account_menu() {
    $newtaborder = array(
        'edit-account' => __( 'Mis datos', 'woocommerce' ),
        'edit-address' => __('Dirección', AEMI_TEXT_DOMAIN),
        'interests' => __( 'Mis intereses', AEMI_TEXT_DOMAIN ),
//        'interests' => __( 'Mis intereses', AEMI_TEXT_DOMAIN ),
        //'orders' => __( 'Mis pagos', 'woocommerce' ),
        AEMICustomizeSimpleMembership__CustomPageInWoo::$simpleMembershipWooSlug => __( 'Mis pagos', 'woocommerce' ),
        'subscriptions' => __( 'Envíos', AEMI_TEXT_DOMAIN ),
    );

    return $newtaborder;
}
add_filter ( 'woocommerce_account_menu_items', 'aemi_reorder_my_account_menu' );