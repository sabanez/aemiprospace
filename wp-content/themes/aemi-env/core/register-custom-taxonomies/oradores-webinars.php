<?php

function aemi_register_oradores_webinars() {

    $labels = array(
        'name'                       => _x( 'Oradores Webinars', 'Taxonomy General Name', AEMI_TEXT_DOMAIN ),
        'singular_name'              => _x( 'Orador Webinar', 'Taxonomy Singular Name', AEMI_TEXT_DOMAIN ),
        'menu_name'                  => __( 'Oradores Webinars', AEMI_TEXT_DOMAIN ),
        'new_item_name'              => __( 'New Orador Name', AEMI_TEXT_DOMAIN ),
        'add_new_item'               => __( 'Add New Orador', AEMI_TEXT_DOMAIN ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_in_rest'               => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy( 'oradores_webinar', array( 'webinars' ), $args );

}
add_action( 'init', 'aemi_register_oradores_webinars' );