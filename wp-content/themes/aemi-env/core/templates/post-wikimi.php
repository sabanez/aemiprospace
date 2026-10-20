<?php

if (!function_exists('get_aemi_template_wikimi_posts')) {
    /**
     * Return rendered html list of posts WikiMi.
     *
     * @param array $args - This array contais args to return categories
     *
     * @see Reference accept $args [https://developer.wordpress.org/reference/functions/get_posts/]
     *
     * @return string
     */
    function get_aemi_template_wikimi_posts($args = [])
    {
        // default config to get posts testimonial
        $args = array_merge(
            [
              'posts_per_page' => 4,
            ],
            $args
        );

        // get posts
        $posts = get_posts($args);

        $htmlRender = '';

        if ($posts) {
            foreach ($posts as $post) {
                $htmlRender .= get_aemi_box_wikimi_post($post, $args);
            }
        }

        return $htmlRender;
    }
}

if (!function_exists('get_aemi_box_wikimi_post')) {
    /**
     * Return HTML box rendered.
     *
     * @param int|object $post
     *
     * @return string
     */
    function get_aemi_box_wikimi_post($post, $args = [])
    {
        $post = get_post($post);

        $html = '';

        if ($post) {
            $thumbnail = get_the_post_thumbnail($post->ID, 'wikimi-thumbnail');
            $thumbnail = $thumbnail ? $thumbnail : '<img width="305" height="205" src="'.get_template_directory_uri().'/assets/images/box-posts-default.jpg" class="attachment-wikimi-thumbnail size-wikimi-thumbnail wp-post-image" />';
            $content_type = get_aemi_content_types_post($post->ID, 'content_type', false);
            $excerpt_content = get_aemi_text_excerpt($post);
            $content_for_subscribers = get_post_meta($post->ID, '_content_for_subscribers', true);
            $verify_user_viewed_post = verify_user_viewed_post($post->ID, get_current_user_id());
            $level_relation = get_posts_aemi_content_level_names($post->ID, 0, true);

            if (isset($args['container_class'])) {
                $html .= '<div class="'.esc_attr($args['container_class']).'">';
            }

            $html .= '<div class="box-wikimi-post '.($content_for_subscribers ? 'box-wikimi-post__pro-content' : '').'">';

            if ($thumbnail) {
                $html .= '<div class="box-wikimi-post__thumbnail">';
                $html .= '<a href="'.get_permalink($post->ID).'" title="'.__('Go to', AEMI_TEXT_DOMAIN).' '.esc_attr($post->post_title).'">';
                $html .= $thumbnail;

                if ($content_for_subscribers) {
                    $html .= '<div class="box-wikimi-post__info-thumbnail-pro">'.__('PRO', AEMI_TEXT_DOMAIN).'</div>';
                }

                if ($verify_user_viewed_post) {
                    $html .= '<div class="box-wikimi-post__content-box-visualized-element">'.__('Visualized', AEMI_TEXT_DOMAIN).'</div>';
                }

                if ($level_relation) {
                    $html .= '<div class="box-wikimi-post__content-level-post-wikimi">'.implode(', ', $level_relation).'</div>';
                }

                $html .= '</a>';
                $html .= '</div>';
            }

            $html .= '<div class="box-wikimi-post__info">';

            if ($content_type) {
                $html .= '<div class="box-wikimi-post__content-type">';
                $html .= $content_type;
                $html .= '</div>';
            }

            $html .= '<h3 class="box-wikimi-post__title">';
            $html .= '<a href="'.get_permalink($post->ID).'" title="'.__('Go to', AEMI_TEXT_DOMAIN).' '.esc_attr($post->post_title).'">';
            $html .= $post->post_title;
            $html .= '</a>';
            $html .= '</h3>';

            $html .= '<div class="box-wikimi-post__excerpt">';
            $html .= $excerpt_content;
            $html .= '</div>';

            $html .= '<div class="box-wikimi-post__link">';

            $html .= '<a href="'.get_permalink($post->ID).'" title="'.__('Go to', AEMI_TEXT_DOMAIN).' '.esc_attr($post->post_title).'" class="btn btn-secondary">'.__('See publications', AEMI_TEXT_DOMAIN).'</a>';

            $html .= favorite_button($post->ID);

            $html .= '</div>';

            $html .= '</div>';

            $html .= '</div>';

            if (isset($args['container_class'])) {
                $html .= '</div>';
            }
        }

        return $html;
    }
}
