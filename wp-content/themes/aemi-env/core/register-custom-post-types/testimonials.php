<?php

class AemiCPTTestimonials
{
    private $post_type_slug = 'testimonials';
    public static $nonce_action = 'sdDLzr5UjMy-c48evcPL7X-PNDYp';
    public static $nonce_name = 'lNCCjGT8JdI5GE3xBVrH48nA';

    public function __construct()
    {
        // insert in init new custom post type
        add_action('init', [$this, 'testimonials'], 0);

        // Register metabox
        add_action('add_meta_boxes', [$this, 'meta_box']);
        add_action('save_post', [$this, 'save_meta_box']);

        /*
         * =============================================================================================
         *                                        Personal data
         * =============================================================================================
         */
        add_action('wp_ajax_aemi_insert_new_testimonial', [$this, 'aemi_insert_new_testimonial']);
        add_action('wp_ajax_nopriv_aemi_insert_new_testimonial', [$this, 'aemi_insert_new_testimonial']);
    }

    public static function render_nonce_aemi_insert_new_testimonial()
    {
        if (is_user_logged_in()) {
            $html = wp_nonce_field(self::$nonce_action, self::$nonce_name, true, false);

            return $html;
        }

        return '';
    }

    /**
     * Save data os post metabox.
     *
     * @param WP_Post $postID Return post object
     */
    public function save_meta_box($postID)
    {
        // Check nonce exist
        if (!isset($_POST[$this->post_type_slug.'_meta_nonce'])) {
            return $postID;
        }

        $get_nonce_value = $_POST[$this->post_type_slug.'_meta_nonce'];

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($get_nonce_value, $this->post_type_slug)) {
            return $postID;
        }

        // Case is auto save, not process saved parameter
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $postID;
        }

        // VERIFY is post submit is post_type equals a testimonials
        if (!isset($_POST['post_type']) && isset($_POST['post_type']) && $_POST['post_type'] != $this->post_type_slug) {
            return $postID;
        }

        // verify user contains permission of edit page or edit post
        if (!current_user_can('edit_page', $postID) && !current_user_can('edit_post', $postID)) {
            return $postID;
        }

        // Get data to save
        // $data = (isset($_POST['text_below_name']) && $_POST['text_below_name'] ? sanitize_text_field($_POST['text_below_name']) : '');

        // Save/Update post meta
        // update_post_meta($postID, '_text_below_name', $data);
    }

    /**
     * Create/Register custom meta box of testimonials.
     */
    public function meta_box($post_type)
    {
        if ($post_type == $this->post_type_slug) {
            add_meta_box(
                $this->post_type_slug.'_extra_options',
                __('Extra options', AEMI_TEXT_DOMAIN),
                [$this, 'meta_box_content'],
                $post_type,
                'advanced',
                'high'
            );
        }
    }

    public function meta_box_content($post)
    {
        wp_nonce_field($this->post_type_slug, $this->post_type_slug.'_meta_nonce');

        // Get in database
        $savedData = get_post_meta($post->ID, '_text_below_name', true);
        $userDataRelatedID = get_post_meta($post->ID, '_related_user_id', true);
        $user_data = get_userdata($userDataRelatedID);

        // echo '<div style="padding-top: 15px; display: flex; align-items: center;">';
        // echo '<label for="text-below-name"><strong>'.__('Text below name:', AEMI_TEXT_DOMAIN).'</strong></label>';
        // echo '<input id="text-below-name" name="text_below_name" value="'.esc_attr($savedData).'" placeholder="'.__('Ex: CEO', AEMI_TEXT_DOMAIN).'" style="margin-left: 15px; border-radius: 10px; border: 1px solid #ccc; height: 45px; flex: 1; padding-left: 15px;"/>';
        // echo '</div>';

        if ($user_data) {
            $user_name = $user_data->display_name;

            echo '<div style="padding-top: 15px;">';
            echo '<label for="text-below-name" style="margin-right: 10px;"><strong>Usuario relacionado:</strong></label>';
            echo '<br /><br /><strong>ID:</strong> '.$userDataRelatedID.'&nbsp;&nbsp;&nbsp;&nbsp;
            <br /><strong>Nombre:</strong> <a href="'.admin_url('user-edit.php?user_id='.$userDataRelatedID).'">'.esc_html($user_name).'</a>';
            echo '</div>';
        }
    }

    // Register Custom Post Type
    public function testimonials()
    {
        $labels = [
          'name' => __('Testimonials', AEMI_TEXT_DOMAIN),
          'singular_name' => __('Testimonial', AEMI_TEXT_DOMAIN),
          'menu_name' => __('Testimonials', AEMI_TEXT_DOMAIN),
          'name_admin_bar' => __('Testimonials', AEMI_TEXT_DOMAIN),
          'archives' => __('Testimonial archives', AEMI_TEXT_DOMAIN),
          'attributes' => __('Testimonial attributes', AEMI_TEXT_DOMAIN),
          'parent_item_colon' => __('Parent testimonial:', AEMI_TEXT_DOMAIN),
          'all_items' => __('All testimonials', AEMI_TEXT_DOMAIN),
          'add_new_item' => __('Add New testimonial', AEMI_TEXT_DOMAIN),
          'add_new' => __('Add New', AEMI_TEXT_DOMAIN),
          'new_item' => __('New testimonial', AEMI_TEXT_DOMAIN),
          'edit_item' => __('Edit testimonial', AEMI_TEXT_DOMAIN),
          'update_item' => __('Update testimonial', AEMI_TEXT_DOMAIN),
          'view_item' => __('View testimonial', AEMI_TEXT_DOMAIN),
          'view_items' => __('View testimonials', AEMI_TEXT_DOMAIN),
          'search_items' => __('Search testimonial', AEMI_TEXT_DOMAIN),
          'not_found' => __('Not found', AEMI_TEXT_DOMAIN),
          'not_found_in_trash' => __('Not found in Trash', AEMI_TEXT_DOMAIN),
          'featured_image' => __('Author\'s image', AEMI_TEXT_DOMAIN),
          'set_featured_image' => __('Set Author\'s image', AEMI_TEXT_DOMAIN),
          'remove_featured_image' => __('Remove Author\'s image', AEMI_TEXT_DOMAIN),
          'use_featured_image' => __('Use as Author\'s image', AEMI_TEXT_DOMAIN),
          'insert_into_item' => __('Insert into testimonial', AEMI_TEXT_DOMAIN),
          'uploaded_to_this_item' => __('Uploaded to this testimonial', AEMI_TEXT_DOMAIN),
          'items_list' => __('Testimonials list', AEMI_TEXT_DOMAIN),
          'items_list_navigation' => __('Testimonials list navigation', AEMI_TEXT_DOMAIN),
          'filter_items_list' => __('Filter testimonials list', AEMI_TEXT_DOMAIN),
        ];

        $args = [
          'label' => __('Testimonial', AEMI_TEXT_DOMAIN),
          'labels' => $labels,
          'supports' => ['title', 'editor'],
          'hierarchical' => false,
          'public' => false,
          'show_ui' => true,
          'show_in_menu' => true,
          // 'menu_position'         => 5,
          'show_in_admin_bar' => true,
          'show_in_nav_menus' => true,
          'can_export' => true,
          'has_archive' => false,
          'exclude_from_search' => true,
          'publicly_queryable' => false,
          'capability_type' => 'page',
        ];

        register_post_type($this->post_type_slug, $args);
    }

    public function aemi_insert_new_testimonial()
    {
        $data['message'] = 'Vaya, no fue posible grabar su testimonio.';
        $data['success'] = false;

        if (is_user_logged_in()) {
            check_ajax_referer(self::$nonce_action, self::$nonce_name);

            if (isset($_POST['mensagem']) && $_POST['mensagem']) {
                $textarea = sanitize_textarea_field($_POST['mensagem']);
//                $current_user = wp_get_current_user();

                $old_posted_data = get_posts([
                    'post_type' => $this->post_type_slug,
                    'meta_key' => '_related_user_id',
                    'meta_value' => get_current_user_id(),
                    'post_status' => ['publish', 'draft'],
                ]);

                if ($old_posted_data) {
                    $post_date = get_the_date('Y-m-d H:i:s', $old_posted_data[0]->ID);
                    $post_timestamp = strtotime($post_date);
                    $current_timestamp = current_time('timestamp');
                    $difference_in_seconds = $current_timestamp - $post_timestamp;

                    if ($difference_in_seconds <= 300) {
                        $data['message'] = 'Inténtelo de nuevo más tarde, ¡acaba de enviar un testimonio!';
                        $data['success'] = true;
                        wp_send_json_success($data);
                        exit;
                    }
                }

                $post_id = wp_insert_post([
                  'post_title' => esc_html(get_aemi_current_user_name()),
                  'post_content' => $textarea,
                  'post_status' => 'draft',
                  'post_type' => $this->post_type_slug,
                ]);

                if (!is_wp_error($post_id)) {
                    $data['message'] = '¡Testimonio enviado exitosamente!';
                    $data['success'] = true;

                    update_post_meta($post_id, '_related_user_id', get_current_user_id());
                }
            }
        }

        wp_send_json_success($data);
    }
}

// Disabled temporary
new AemiCPTTestimonials();
