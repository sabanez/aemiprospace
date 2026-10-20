<?php

function aemi_set_favorite_post()
{
    $post_id = sanitize_text_field($_POST['post_id']);
    $user_id = get_current_user_id();
    $meta_key = '_aemi_favorite_pages';
    $favorites = get_user_meta($user_id, $meta_key);

    if (is_countable($favorites) && count($favorites) < 1) {
        update_user_meta($user_id, $meta_key, [$post_id]);
    } else {
        $meta = get_user_meta($user_id, '_aemi_favorite_pages');

        if ( in_array($post_id, $meta[0]) ) {
            $search = array_search($post_id, $meta[0]);
            unset($meta[0][$search]);

            update_user_meta($user_id, $meta_key, $meta[0]);
            wp_send_json(100);
        }

        array_push($meta[0], $post_id);
        update_user_meta($user_id, $meta_key, $meta[0]);

        wp_send_json(200);
    }
}
add_action('wp_ajax_aemi_set_favorite_post', 'aemi_set_favorite_post');
add_action('wp_ajax_nopriv_aemi_set_favorite_post', 'aemi_set_favorite_post');