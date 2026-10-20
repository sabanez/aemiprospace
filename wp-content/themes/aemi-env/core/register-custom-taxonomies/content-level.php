<?php

function aemi_register_content_level_taxonomy()
{

    $labels = array(
        'name'                       => _x('Content levels', 'Taxonomy General Name', AEMI_TEXT_DOMAIN),
        'singular_name'              => _x('Content level', 'Taxonomy Singular Name', AEMI_TEXT_DOMAIN),
        'menu_name'                  => __('Content level', AEMI_TEXT_DOMAIN),
        'all_items'                  => __('All content levels', AEMI_TEXT_DOMAIN),
        'parent_item'                => __('Parent level', AEMI_TEXT_DOMAIN),
        'parent_item_colon'          => __('Parent level:', AEMI_TEXT_DOMAIN),
        'new_item_name'              => __('New content level Name', AEMI_TEXT_DOMAIN),
        'add_new_item'               => __('Add New content level', AEMI_TEXT_DOMAIN),
        'edit_item'                  => __('Edit content level', AEMI_TEXT_DOMAIN),
        'update_item'                => __('Update content level', AEMI_TEXT_DOMAIN),
        'view_item'                  => __('View content level', AEMI_TEXT_DOMAIN),
        'separate_items_with_commas' => __('Separate content levels with commas', AEMI_TEXT_DOMAIN),
        'add_or_remove_items'        => __('Add or remove content level', AEMI_TEXT_DOMAIN),
        'choose_from_most_used'      => __('Choose from the most used', AEMI_TEXT_DOMAIN),
        'popular_items'              => __('Popular content levels', AEMI_TEXT_DOMAIN),
        'search_items'               => __('Search content levels', AEMI_TEXT_DOMAIN),
        'not_found'                  => __('Not Found', AEMI_TEXT_DOMAIN),
        'no_terms'                   => __('No content levels', AEMI_TEXT_DOMAIN),
        'items_list'                 => __('Content levels list', AEMI_TEXT_DOMAIN),
        'items_list_navigation'      => __('Content levels list navigation', AEMI_TEXT_DOMAIN),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => false,
        'show_in_rest'               => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy('content_level', array('post'), $args);

}

add_action('init', 'aemi_register_content_level_taxonomy');