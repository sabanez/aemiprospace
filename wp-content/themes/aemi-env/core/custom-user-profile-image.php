<?php

if (!class_exists('EnvixoCustomUserProfileImage')) {
    class EnvixoCustomUserProfileImage
    {
        public $meta_name = '_custom_user_thumbnail_id';
        public $meta_name_new_image = '_custom_user_thumbnail_new_image';
        public $nonce_action = 'custom_user_profile_thumbnail';
        public $nonce_name = '_user_profile_photo';

        /**
         * Set actions and filters User profile custom thumbnail.
         */
        public function __construct()
        {
            add_action('show_user_profile', [$this, 'custom_fields_user_admin']);
            add_action('edit_user_profile', [$this, 'custom_fields_user_admin']);
            add_action('admin_head', [$this, '_custom_head_style_admin']);
            add_action('admin_enqueue_scripts', [$this, '_register_scripts']);

            add_action('personal_options_update', [$this, 'save_admin_user_thumbnail']);
            add_action('edit_user_profile_update', [$this, 'save_admin_user_thumbnail']);

            add_filter('pre_get_avatar_data', [$this, 'replace_default_url_profile_image_url'], 100, 2);

            // add compatibility Woocommerce
            add_action('woocommerce_edit_account_form_start', [$this, 'add_woo_change_profile_thumbnail']);
            // add_action( 'woocommerce_edit_account_form_tag', array( $this, 'customize_form_edit_account_type' ) );
            add_action('wp_enqueue_scripts', [$this, 'add_woo_custom_scripts']);
            // add_action( 'woocommerce_save_account_details', array( $this, 'save_woo_change_profile_thumbnail' ) );

            add_action('wp_ajax_custom_change_profile_image', [$this, 'ajaxProfileCustomChangeProfileImage']);
        }

        /**
         * Delete user profile image and delete attachment from WP database.
         *
         * @param int $user_id
         */
        private function delete_current_user_image($user_id)
        {
            // Verify has user ID
            if ($user_id) {
                // get current user image set to delete, because user removed user
                $custom_user_thumbnail_id = get_user_meta($user_id, $this->meta_name, true);
                // verify thumbnail selected
                if ($custom_user_thumbnail_id != '') {
                    // validate post attachment exist
                    if (get_post($custom_user_thumbnail_id)) {
                        // delete thumbnail image from WP
                        wp_delete_attachment($custom_user_thumbnail_id, true);
                    }

                    // set removed profile thumbnail
                    update_user_meta($user_id, $this->meta_name, '');
                }
            }
        }

        /**
         * =================================================================
         *                  CUSTOM FRONTEND SETTINGS
         * =================================================================.
         */

        /**
         * Insert in default form to edit account enctype attribute for default woocommerce form.
         */
        public function customize_form_edit_account_type()
        {
            echo ' enctype="multipart/form-data" ';
        }

        /**
         * Custom action to save custom profile image from user.
         *
         * @param int $user_id
         */
        public function save_woo_change_profile_thumbnail($user_id)
        {
            if (isset($_POST[$this->meta_name]) && isset($_POST[$this->nonce_name]) && wp_verify_nonce($_POST[$this->nonce_name], $this->nonce_action)) {
                // Thumnail id
                $user_thumbnail_id = (isset($_POST[$this->meta_name]) && $_POST[$this->meta_name] ? esc_attr($_POST[$this->meta_name]) : '');

                // User removed default thumbnail
                if ($user_thumbnail_id == '') {
                    $this->delete_current_user_image($user_id);
                }

                // Validate use send new message
                $user_new_image = (isset($_FILES[$this->meta_name_new_image]) && $_FILES[$this->meta_name_new_image] ? $_FILES[$this->meta_name_new_image] : '');

                if ($user_new_image) {
                    $returnValidation = wp_check_filetype_and_ext($user_new_image['tmp_name'], $user_new_image['name']);

                    // verify is a valid type image
                    if (wp_match_mime_types('image', $returnValidation['type'])) {
                        if ($returnValidation['ext']) {
                            $_FILES[$this->meta_name_new_image]['name'] = substr(str_shuffle('q1w2e3r4t5y68u9ipalkdjfhgzmxncbv'.rand(0, 1000).'4qw5as534d83q41a3s8asdas5d41a4das6d'), 0, 12).date('mdis').substr(str_shuffle('q1w2e3r4t5y68u9ipalkdjfhgzmxncbv'.rand(0, 1000).'4qw5as534d83q41a3s8asdas5d41a4das6d'), 0, 12).'.'.$returnValidation['ext'];
                        }

                        require_once ABSPATH.'wp-admin/includes/image.php';
                        require_once ABSPATH.'wp-admin/includes/file.php';
                        require_once ABSPATH.'wp-admin/includes/media.php';

                        // return current attachment id case success or error msg
                        $attachment_id = media_handle_upload($this->meta_name_new_image, 0, [], ['test_form' => false]);

                        if (is_wp_error($attachment_id)) {
                            // error occurred
                        } else {
                            // remove old thumbnail related a profile user
                            $this->delete_current_user_image($user_id);
                            // set new upload image profile user
                            update_user_meta($user_id, $this->meta_name, $attachment_id);
                        }
                    }
                }

                return true;
            }

            return false;
        }

        /**
         * Add custom fields in form to thumbnail profile user.
         */
        public function add_woo_change_profile_thumbnail()
        {
            $custom_user_thumbnail_id = get_user_meta(get_current_user_id(), $this->meta_name, true);
            $url = '';

            if ($custom_user_thumbnail_id) {
                $url = wp_get_attachment_url($custom_user_thumbnail_id);
            }

            ?>
      <form class="my-account-form-custom-user-profile" enctype="multipart/form-data">
        <div class="custom-user-profile">
          <div class="custom-user-profile-thumbnail-container">
            <?php echo wp_nonce_field($this->nonce_action, $this->nonce_name); ?>
            <input type="text" name="<?php echo $this->meta_name; ?>" class="custom-user-profile-current-image-input" value="<?php echo $custom_user_thumbnail_id; ?>" />
            <div class="custom-user-profile-thumbnail-img <?php echo  $url ? 'custom-user-profile-thumbnail-has-image' : ''; ?>" 
              <?php echo  $url ? 'style="background-image: url('.$url.');"' : ''; ?>
            >
              <input type="file" accept=".jpg, .jpeg, .png" name="<?php echo $this->meta_name_new_image; ?>" id="custom-user-profile-thumbnail-input"/>
              <label for="custom-user-profile-thumbnail-input">
                <span>
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M490.3 40.4C512.2 62.27 512.2 97.73 490.3 119.6L460.3 149.7L362.3 51.72L392.4 21.66C414.3-.2135 449.7-.2135 471.6 21.66L490.3 40.4zM172.4 241.7L339.7 74.34L437.7 172.3L270.3 339.6C264.2 345.8 256.7 350.4 248.4 353.2L159.6 382.8C150.1 385.6 141.5 383.4 135 376.1C128.6 370.5 126.4 361 129.2 352.4L158.8 263.6C161.6 255.3 166.2 247.8 172.4 241.7V241.7zM192 63.1C209.7 63.1 224 78.33 224 95.1C224 113.7 209.7 127.1 192 127.1H96C78.33 127.1 64 142.3 64 159.1V416C64 433.7 78.33 448 96 448H352C369.7 448 384 433.7 384 416V319.1C384 302.3 398.3 287.1 416 287.1C433.7 287.1 448 302.3 448 319.1V416C448 469 405 512 352 512H96C42.98 512 0 469 0 416V159.1C0 106.1 42.98 63.1 96 63.1H192z"/></svg>
                </span>
              </label>
              <button type="button" class="custom-user-profile-thumbnail-remove">x</button>

              <div class="custom-user-profile-thumbnail__loading">
                <div><span></span></div>
              </div>

              <div class="custom-user-profile-thumbnail__confirm-change-container" data-urldefaultthumbnail="<?php echo $url; ?>">
                <div class="custom-user-profile-thumbnail__confirm-change-container-inner">
                  <p><?php echo __('Check profile photo preview, want to make a change?', AEMI_TEXT_DOMAIN); ?></p>
                  <button type="button" class="custom-user-profile-thumbnail__confirm-change-cancel">
                    <?php echo __('Cancel', AEMI_TEXT_DOMAIN); ?>
                  </button>
                  <button type="button" class="custom-user-profile-thumbnail__confirm-change-confirm">
                    <?php echo __('Confirm', AEMI_TEXT_DOMAIN); ?>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
      <?php
        }

        /**
         * Register scripts on front page.
         */
        public function add_woo_custom_scripts($hook)
        {
            if (function_exists('is_account_page') && is_account_page()) {
                wp_enqueue_script('custom-profile-script', get_aemi_url_assets_js('core/custom-user-profile-image.js'), ['jquery']);
                wp_localize_script('custom-profile-script', 'custom_profile_script_vars', [
                  'admin_ajax' => get_admin_url().'admin-ajax.php',
                ]);
                wp_enqueue_style('custom-profile-style', get_aemi_url_assets_css('core/custom-user-profile-image.css'));
            }
        }

        /**
         * code from https://stackoverflow.com/questions/2510434/format-bytes-to-kilobytes-megabytes-gigabytes.
         *
         * @see https://stackoverflow.com/questions/2510434/format-bytes-to-kilobytes-megabytes-gigabytes
         *
         * @author John Himmelman's <https://stackoverflow.com/users/194676/john-himmelman>
         */
        public function formatBytes($size, $precision = 2)
        {
            $base = log($size, 1024);
            $suffixes = ['', 'Kb', 'Mb', 'Gb', 'Tb'];

            return round(pow(1024, $base - floor($base)), $precision).' '.$suffixes[floor($base)];
        }

        /**
         * Action to change custom change profile image.
         */
        public function ajaxProfileCustomChangeProfileImage()
        {
            $user_id = get_current_user_id();

            $return_data = [
              'success' => false,
              'msg' => __('Oops, could not change the image. Try again later!', AEMI_TEXT_DOMAIN),
            ];

            if ($user_id) {
                if (isset($_FILES[$this->meta_name_new_image]) && isset($_FILES[$this->meta_name_new_image]['size'])) {
                    if ($_FILES[$this->meta_name_new_image]['size'] <= wp_max_upload_size() && (!$_FILES[$this->meta_name_new_image]['error'] || $_FILES[$this->meta_name_new_image]['error'] == 4)) {
                        $return_info = $this->save_woo_change_profile_thumbnail($user_id);

                        if ($return_info) {
                            $return_data['success'] = true;
                            $return_data['msg'] = __('Profile photo changed.', AEMI_TEXT_DOMAIN);
                        }
                    } else {
                        $return_data['msg'] = sprintf(__('Unable to upload your image, please upload a file up to %s.', AEMI_TEXT_DOMAIN), $this->formatBytes(wp_max_upload_size()));
                    }
                } else {
                    $return_data['msg'] = __('Please upload an image file in the correct format.', AEMI_TEXT_DOMAIN);
                }
            }

            echo json_encode($return_data);
            exit;
        }

        /**
         * =================================================================
         *                  CUSTOM ADMIN SETTINGS
         * =================================================================.
         */
        /**
         * Register custom scripts.
         */
        public function _register_scripts($hook)
        {
            if ($hook == 'profile.php') {
                wp_enqueue_media();
                wp_enqueue_script('admin-custom-profile-script', get_aemi_url_assets_js('core/admin/custom-user-profile-image.js'), ['jquery']);

                wp_localize_script('admin-custom-profile-script', 'admin_custom_profile_script_vars', [
                  'titleUploadImage' => __('Change thumbnail', AEMI_TEXT_DOMAIN),
                  'btnTextUploadImage' => __('Use this image', AEMI_TEXT_DOMAIN),
                ]);
            }
        }

        /**
         * Custom style in admin header.
         */
        public function _custom_head_style_admin()
        {
            global $hook_suffix;
            if ($hook_suffix == 'profile.php') {
                ?><style>#profile-page .user-profile-picture{ display: none; }</style><?php
            }
        }

        /**
         * HTML Custom fields.
         *
         * @param WP_User $user
         */
        public function custom_fields_user_admin($user)
        {
            $userID = false;

            if ($user) {
                $userArr = (array) $user;

                if ($userArr && isset($userArr['ID'])) {
                    $userID = $userArr['ID'];
                }
            }

            $custom_user_thumbnail_id = false;
            $url = '';

            if ($userID) {
                $custom_user_thumbnail_id = get_user_meta($userID, $this->meta_name, true);

                if ($custom_user_thumbnail_id) {
                    $url = wp_get_attachment_url($custom_user_thumbnail_id);
                }
            }

            ?>
      <table class="form-table">
        <tr>
            <th>
              <label><?php _e('Profile Picture'); ?></label>
            </th>
            <td>
              <?php echo wp_nonce_field($this->nonce_action, $this->nonce_name); ?>
              <div style="height: 150px; width: 150px; border-radius: 5px; border: 4px solid #fff; margin-bottom: 25px; background-color: #ccc; box-shadow: 0 0 11px -4px rgb(0,0,0,0.30); background-size: cover; background-position: center;<?php echo  $url ? 'background-image: url('.$url.');' : ''; ?>" id="attachment-user-url-thumbnail">
                <button type="button" id="attachment-user-remove-thumbnail" style="background: #e73535;color: #fff;border: none;border-radius: 70%;line-height: 25px;padding-top: 6px;float: right;margin: -17px -18px 0 0; cursor:pointer;<?php echo  $url ? 'display: block;' : 'display: none;'; ?>"><span class="dashicons dashicons-no"></span></button>
              </div>
              <input type="hidden" id="attachment-user-id-thumbnail" name="<?php echo $this->meta_name; ?>" value="<?php echo $custom_user_thumbnail_id; ?>" />
              <button type="button" class="button button-primary" id="attachment-user-id-btn"><?php echo __('Select thumbnail', AEMI_TEXT_DOMAIN); ?></button>
            </td>
        </tr>
      </table>
      <?php
        }

        /**
         * Detect and save custom field user admin.
         *
         * @param int $user_id
         */
        public function save_admin_user_thumbnail($user_id)
        {
            if (isset($_POST[$this->meta_name]) && isset($_POST[$this->nonce_name]) && wp_verify_nonce($_POST[$this->nonce_name], $this->nonce_action)) {
                // thumnail id
                $user_thumbnail_id = (isset($_POST[$this->meta_name]) && $_POST[$this->meta_name] ? esc_attr($_POST[$this->meta_name]) : '');

                if ($user_thumbnail_id) {
                    if (!get_post($user_thumbnail_id)) {
                        $user_thumbnail_id = '';
                    }
                }

                // save data thumbnail
                update_user_meta($user_id, $this->meta_name, $user_thumbnail_id);
            }
        }

        /**
         *  Replace default gravatar URL to customize user thumbnail.
         */
        public function replace_default_url_profile_image_url($args, $id_or_email)
        {
            $user = false;
            $args['url'] = '';

            if (is_int($id_or_email)) {
                $user = get_user_by('id', $id_or_email);
            } elseif (is_string($id_or_email)) {
                $user = get_user_by('email', $id_or_email);
            }

            if ($user) {
                $custom_user_thumbnail_id = get_user_meta($user->ID, $this->meta_name, true);

                if ($custom_user_thumbnail_id) {
                    $img_src = wp_get_attachment_image_src($custom_user_thumbnail_id, [$args['width'], $args['height']]);

                    if (isset($img_src[0])) {
                        $args['url'] = $img_src[0];
                    }
                }
            }

            return $args;
        }
    }

    new EnvixoCustomUserProfileImage();
}
