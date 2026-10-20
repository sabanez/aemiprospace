<?php

class AemiCPTWebinars
{
    private $post_type_slug = 'webinars';

    function __construct(){
        add_action( 'init', array( $this, 'webinars'), 0 );
    }

    public function webinars() {

        $labels = array(
            'name'                  => __( 'Webinars', AEMI_TEXT_DOMAIN ),
            'singular_name'         => __( 'Webinar', AEMI_TEXT_DOMAIN ),
            'menu_name'             => __( 'Webinars', AEMI_TEXT_DOMAIN ),
            'name_admin_bar'        => __( 'Webinars', AEMI_TEXT_DOMAIN ),
            'archives'              => __( 'Webinar archives', AEMI_TEXT_DOMAIN ),
            'attributes'            => __( 'Webinar attributes', AEMI_TEXT_DOMAIN ),
            'parent_item_colon'     => __( 'Parent webinar:', AEMI_TEXT_DOMAIN ),
            'all_items'             => __( 'All webinars', AEMI_TEXT_DOMAIN ),
            'add_new_item'          => __( 'Add New webinar', AEMI_TEXT_DOMAIN ),
            'add_new'               => __( 'Add New', AEMI_TEXT_DOMAIN ),
            'new_item'              => __( 'New webinar', AEMI_TEXT_DOMAIN ),
            'edit_item'             => __( 'Edit webinar', AEMI_TEXT_DOMAIN ),
            'update_item'           => __( 'Update webinar', AEMI_TEXT_DOMAIN ),
            'view_item'             => __( 'View webinar', AEMI_TEXT_DOMAIN ),
            'view_items'            => __( 'View webinars', AEMI_TEXT_DOMAIN ),
            'search_items'          => __( 'Search webinar', AEMI_TEXT_DOMAIN ),
            'not_found'             => __( 'Not found', AEMI_TEXT_DOMAIN ),
            'not_found_in_trash'    => __( 'Not found in Trash', AEMI_TEXT_DOMAIN ),
            'featured_image'        => __( 'Author\'s image', AEMI_TEXT_DOMAIN ),
            'set_featured_image'    => __( 'Set Author\'s image', AEMI_TEXT_DOMAIN ),
            'remove_featured_image' => __( 'Remove Author\'s image', AEMI_TEXT_DOMAIN ),
            'use_featured_image'    => __( 'Use as Author\'s image', AEMI_TEXT_DOMAIN ),
            'insert_into_item'      => __( 'Insert into webinar', AEMI_TEXT_DOMAIN ),
            'uploaded_to_this_item' => __( 'Uploaded to this webinar', AEMI_TEXT_DOMAIN ),
            'items_list'            => __( 'Webinars list', AEMI_TEXT_DOMAIN ),
            'items_list_navigation' => __( 'Webinars list navigation', AEMI_TEXT_DOMAIN ),
            'filter_items_list'     => __( 'Filter webinars list', AEMI_TEXT_DOMAIN ),
        );

        $args = array(
            'label'                 => __( 'Webinars', AEMI_TEXT_DOMAIN ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            //'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
        );

        register_post_type( $this->post_type_slug, $args );
    }
}

//new AemiCPTWebinars();