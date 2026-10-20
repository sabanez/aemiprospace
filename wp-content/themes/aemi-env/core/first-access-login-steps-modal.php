<?php

if (!class_exists('AEMIFirstAccessLoginStepsModal')) {
    class AEMIFirstAccessLoginStepsModal
    {
        public static $meta_field_name = '_first_access_login_steps_modal';
        public static $nonce_action = 'Aervd9q9qwe87w56qegs';
        public static $nonce_name = '_first_access_login_steps_modal';
        public static $option_meta_name = '_first_acccess_login_steps_modal';

        public static $frontend_modal_meta_field_name = '_steps_modal';
        public static $frontend_modal_nonce_action = 'qw4e6851a35ASASDEQPOIU';

        public static $user_meta_current_step = '_current_user_step';
        public static $user_meta_user_interest_type = '_current_user_step_insterest_type';
        public static $user_meta_user_interest = '_current_user_step_insterest';

        public function __construct()
        {
            if (is_admin()) {
                add_action('admin_menu', [$this, 'register_page']);
                add_action('admin_enqueue_scripts', [$this, 'register_scripts']);

                // user profile admin
                add_action('show_user_profile', [$this, 'custom_fields_user_admin']);
                add_action('edit_user_profile', [$this, 'custom_fields_user_admin']);
                add_action('personal_options_update', [$this, 'save_admin_user_thumbnail']);
                add_action('edit_user_profile_update', [$this, 'save_admin_user_thumbnail']);
            } else {
                add_action('init', [$this, 'save_first_steps_information']);
                add_action('init', [$this, 'detect_and_show_first_step_login']);
            }
        }

        /**
         * Return options for the first access login steps modal.
         */
        public static function get_options()
        {
            $options = get_option(self::$option_meta_name, false);
            if ($options) {
                $options = wporg_recursive_sanitize_text_field($options, true);
            }

            return $options;
        }

        /**
         * Return current step user.
         *
         * @param int $userID
         */
        public static function get_user_current_step($userID = false)
        {
            $userID = (isset($userID) && $userID ? $userID : get_current_user_id());

            if ($userID) {
                $user_meta_current_step = get_user_meta($userID, self::$user_meta_current_step, true);

                if ($user_meta_current_step) {
                    return $user_meta_current_step;
                }
            }

            return 1;
        }

        /**
         * ===========================================================================================
         *                                  BACKEND-END (ADMIN)
         * ===========================================================================================.
         */

        /**
         * Register script and style.
         */
        public function register_scripts($hook)
        {
            if ($hook == 'settings_page_first_access_login_steps_modal') {
                AEMIGenerateFieldsRepeat::load_script_thumbnail();
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

            $user_meta_current_step = false;
            $user_meta_user_interest_type = '';
            $user_meta_user_interest = [];
            $categories_interest = get_interest_list_for_users();

            if ($userID) {
                $user_meta_current_step = get_user_meta($userID, self::$user_meta_current_step, true);
                $user_meta_user_interest_type = get_user_meta($userID, self::$user_meta_user_interest_type, true);
                $user_meta_user_interest = get_user_meta($userID, self::$user_meta_user_interest, true);
            }

            wp_nonce_field(self::$nonce_action, self::$nonce_name);
            ?>
      <table class="form-table">
        <tr>
            <th>
              <label><?php _e('Current stage of first access', AEMI_TEXT_DOMAIN); ?>:</label>
            </th>
            <td>
              <select name="<?php echo self::$user_meta_current_step; ?>" class="regular-text">
                <option value="1" <?php echo (int) $user_meta_current_step == 1 ? 'selected="selected"' : ''; ?>>1</option>
                <option value="2" <?php echo (int) $user_meta_current_step == 2 ? 'selected="selected"' : ''; ?>>2</option>
                <option value="3" <?php echo (int) $user_meta_current_step == 3 ? 'selected="selected"' : ''; ?>>3</option>
                <option value="4" <?php echo (int) $user_meta_current_step == 4 ? 'selected="selected"' : ''; ?>>4 - Vídeo de bienvenida</option>
                <option value="5" <?php echo (int) $user_meta_current_step == 5 ? 'selected="selected"' : ''; ?>><?php echo __('Concluded', AEMI_TEXT_DOMAIN); ?></option>
              </select>
            </td>
        </tr>
        
        <tr>
            <th>
              <label><?php _e('Type of interest', AEMI_TEXT_DOMAIN); ?>:</label>
            </th>
            <td>
              <ul>
                <?php $loopItem = 0;
            foreach (AEMIFirstAccessLoginStepsModal::get_interests_type() as $key_interests => $value_interests) {
                ++$loopItem; ?>
                <li>
                  <label for="interest-<?php echo $key_interests; ?>" class="aemi-first-acess-interest-input-<?php echo $loopItem; ?>">
                    <input class="aemi-first-acess-interest-input aemi-first-acess-interest-input-<?php echo $loopItem; ?>" type="radio" name="first_step_interest" value="<?php echo $key_interests; ?>"
                    <?php echo $user_meta_user_interest_type == $key_interests ? 'checked=""' : ''; ?>
                    id="interest-<?php echo $key_interests; ?>" />
                    <span><?php echo $value_interests; ?></span>
                  </label>
                </li>
                <?php } ?>
              </ul>
            </td>
        </tr>
        
        <tr>
            <th>
              <label><?php _e('Interest', AEMI_TEXT_DOMAIN); ?>:</label>
            </th>
            <td>
              <?php foreach ($categories_interest as $interest) { ?>
                <div class="col-lg-6">
                  <div style="margin-bottom: 6px;">
                    <label class="aemi-form-group-label-checkbox">
                      <input
                        <?php echo is_array($user_meta_user_interest) && in_array($interest['id'], $user_meta_user_interest) ? 'checked=""' : ''; ?>
                      class="aemi-form-group-label-checkbox__input" name="interest_user[]" type="checkbox" value="<?php echo $interest['id']; ?>" /> 
                      <span class="aemi-form-group-label-checkbox__label"><?php echo $interest['title']; ?></span>
                    </label>
                  </div>
                </div>
              <?php } ?>
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
            if (isset($_POST[self::$nonce_name]) && wp_verify_nonce($_POST[self::$nonce_name], self::$nonce_action)) {
                $interests_type = self::get_interests_type();
                $insterestPost = (isset($_POST['first_step_interest']) && $_POST['first_step_interest'] ? sanitize_text_field($_POST['first_step_interest']) : '');

                if ($insterestPost && isset($interests_type[$insterestPost])) {
                    update_user_meta($user_id, self::$user_meta_user_interest_type, $insterestPost);
                } else {
                    update_user_meta($user_id, self::$user_meta_user_interest_type, '');
                }

                $interests = [];
                if (isset($_POST['interest_user']) && $_POST['interest_user']) {
                    foreach ($_POST['interest_user'] as $interest) {
                        if (is_numeric(sanitize_text_field($interest))) {
                            $interests[] = sanitize_text_field($interest);
                        }
                    }
                }

                update_user_meta($user_id, self::$user_meta_user_interest, $interests);

                if (isset($_POST[self::$user_meta_current_step]) && $_POST[self::$user_meta_current_step]) {
                    update_user_meta($user_id, self::$user_meta_current_step, sanitize_text_field($_POST[self::$user_meta_current_step]));
                }

                do_action('activecampaign_update_insterest', $user_id);
            }
        }

        /**
         * Register new admin sub page in setting menu.
         *
         * @see https://codex.wordpress.org/Administration_Menus (Reference)
         */
        public function register_page()
        {
            $hook_suffix = add_submenu_page(
                'options-general.php',
                __('First access messages', AEMI_TEXT_DOMAIN),
                __('First access messages', AEMI_TEXT_DOMAIN),
                'manage_options',
                'first_access_login_steps_modal',
                [$this, 'submenu_page']
            );

            add_action('load-'.$hook_suffix, [$this, 'save_settings']);
        }

        public function generateFieldsStepOne($args)
        {
            $data = $args['data'];
            $title = (isset($data['title']) && $data['title'] ? esc_attr($data['title']) : '');
            $description = (isset($data['description']) && $data['description'] ? $data['description'] : '');
            $name = (isset($data['name']) && $data['name'] ? esc_attr($data['name']) : '');
            $subtitle = (isset($data['subtitle']) && $data['subtitle'] ? esc_attr($data['subtitle']) : '');
            $thumbnail_id = (isset($data['thumbnail_id']) && $data['thumbnail_id'] ? esc_attr($data['thumbnail_id']) : '');

            $htmlReturn = '';

            // Title
            $htmlReturn .= '<div class="aemi-custom-fields__box">';
            $htmlReturn .= '<div class="aemi-custom-fields__box__title">';
            $htmlReturn .= '<label>'.__('Title', AEMI_TEXT_DOMAIN).':</label>';
            $htmlReturn .= '</div>';
            $htmlReturn .= '<div class="aemi-custom-fields__box__input-container">';
            $htmlReturn .= '<input type="text" name="'.$args['prefix'].'[title]" class="aemi-custom-fields__box__input" value="'.$title.'">';
            $htmlReturn .= '</div>';
            $htmlReturn .= '</div>';

            // Description
            $htmlReturn .= '<div class="aemi-custom-fields__box align-to-top">';
            $htmlReturn .= '<div class="aemi-custom-fields__box__title">';
            $htmlReturn .= '<label>'.__('Content', AEMI_TEXT_DOMAIN).':</label>';
            $htmlReturn .= '</div>';
            $htmlReturn .= '<div class="aemi-custom-fields__box__input-container">';
            $htmlReturn .= '<textarea name="'.$args['prefix'].'[description]" class="aemi-custom-fields__box__input" rows="4">'.$description.'</textarea>';
            $htmlReturn .= '</div>';
            $htmlReturn .= '</div>';

            $htmlReturn .= '<div>';
            $htmlReturn .= '<hr />';
            $htmlReturn .= '<h1 style="color: var( --default-blue-color );">'.__('Profile', AEMI_TEXT_DOMAIN).':</h1>';
            $htmlReturn .= '<hr />';
            $htmlReturn .= '</div>';

            // Get rendered element to working WordPress midia gallery
            $htmlReturn .= AEMIGenerateFieldsRepeat::generate_fields_image([
              'input_name' => $args['prefix'].'[thumbnail_id]',
              'input_value' => $thumbnail_id,
            ]);

            // Name
            $htmlReturn .= '<div class="aemi-custom-fields__box">';
            $htmlReturn .= '<div class="aemi-custom-fields__box__title">';
            $htmlReturn .= '<label>'.__('Name', AEMI_TEXT_DOMAIN).':</label>';
            $htmlReturn .= '</div>';
            $htmlReturn .= '<div class="aemi-custom-fields__box__input-container">';
            $htmlReturn .= '<input type="text" name="'.$args['prefix'].'[name]" class="aemi-custom-fields__box__input" value="'.$name.'">';
            $htmlReturn .= '</div>';
            $htmlReturn .= '</div>';

            // Subtitle
            $htmlReturn .= '<div class="aemi-custom-fields__box">';
            $htmlReturn .= '<div class="aemi-custom-fields__box__title">';
            $htmlReturn .= '<label>'.__('Subtitle', AEMI_TEXT_DOMAIN).':</label>';
            $htmlReturn .= '</div>';
            $htmlReturn .= '<div class="aemi-custom-fields__box__input-container">';
            $htmlReturn .= '<input type="text" name="'.$args['prefix'].'[subtitle]" class="aemi-custom-fields__box__input" value="'.$subtitle.'">';
            $htmlReturn .= '</div>';
            $htmlReturn .= '</div>';

            return $htmlReturn;
        }

        public function generateFieldsStepTwo($args)
        {
            $data = $args['data'];
            $title = (isset($data['title']) && $data['title'] ? esc_attr($data['title']) : '');
            $description = (isset($data['description']) && $data['description'] ? $data['description'] : '');
            $name = (isset($data['name']) && $data['name'] ? esc_attr($data['name']) : '');
            $subtitle = (isset($data['subtitle']) && $data['subtitle'] ? esc_attr($data['subtitle']) : '');
            $interests_title = (isset($data['interests_title']) && $data['interests_title'] ? esc_attr($data['interests_title']) : '');
            $thumbnail_id = (isset($data['thumbnail_id']) && $data['thumbnail_id'] ? esc_attr($data['thumbnail_id']) : '');

            $htmlReturn = '';

            // Title
            $htmlReturn .= '<div class="aemi-custom-fields__box">';
            $htmlReturn .= '<div class="aemi-custom-fields__box__title">';
            $htmlReturn .= '<label>'.__('Title', AEMI_TEXT_DOMAIN).':</label>';
            $htmlReturn .= '</div>';
            $htmlReturn .= '<div class="aemi-custom-fields__box__input-container">';
            $htmlReturn .= '<input type="text" name="'.$args['prefix'].'[title]" class="aemi-custom-fields__box__input" value="'.$title.'">';
            $htmlReturn .= '</div>';
            $htmlReturn .= '</div>';

            // Description
            $htmlReturn .= '<div class="aemi-custom-fields__box align-to-top">';
            $htmlReturn .= '<div class="aemi-custom-fields__box__title">';
            $htmlReturn .= '<label>'.__('Content', AEMI_TEXT_DOMAIN).':</label>';
            $htmlReturn .= '</div>';
            $htmlReturn .= '<div class="aemi-custom-fields__box__input-container">';
            $htmlReturn .= '<textarea name="'.$args['prefix'].'[description]" class="aemi-custom-fields__box__input" rows="4">'.$description.'</textarea>';
            $htmlReturn .= '</div>';
            $htmlReturn .= '</div>';

            // interests
            $htmlReturn .= '<div class="aemi-custom-fields__box">';
            $htmlReturn .= '<div class="aemi-custom-fields__box__title">';
            $htmlReturn .= '<label>'.__('Custom title about interests', AEMI_TEXT_DOMAIN).':</label>';
            $htmlReturn .= '</div>';
            $htmlReturn .= '<div class="aemi-custom-fields__box__input-container">';
            $htmlReturn .= '<input type="text" name="'.$args['prefix'].'[interests_title]" class="aemi-custom-fields__box__input" value="'.$interests_title.'">';
            $htmlReturn .= '</div>';
            $htmlReturn .= '</div>';

            $htmlReturn .= '<div>';
            $htmlReturn .= '<hr style="margin-top: 30px;" />';
            $htmlReturn .= '<h1 style="color: var( --default-blue-color );">'.__('Profile', AEMI_TEXT_DOMAIN).':</h1>';
            $htmlReturn .= '<hr />';
            $htmlReturn .= '</div>';

            // Get rendered element to working WordPress midia gallery
            $htmlReturn .= AEMIGenerateFieldsRepeat::generate_fields_image([
              'input_name' => $args['prefix'].'[thumbnail_id]',
              'input_value' => $thumbnail_id,
            ]);

            // Name
            $htmlReturn .= '<div class="aemi-custom-fields__box">';
            $htmlReturn .= '<div class="aemi-custom-fields__box__title">';
            $htmlReturn .= '<label>'.__('Name', AEMI_TEXT_DOMAIN).':</label>';
            $htmlReturn .= '</div>';
            $htmlReturn .= '<div class="aemi-custom-fields__box__input-container">';
            $htmlReturn .= '<input type="text" name="'.$args['prefix'].'[name]" class="aemi-custom-fields__box__input" value="'.$name.'">';
            $htmlReturn .= '</div>';
            $htmlReturn .= '</div>';

            // Subtitle
            $htmlReturn .= '<div class="aemi-custom-fields__box">';
            $htmlReturn .= '<div class="aemi-custom-fields__box__title">';
            $htmlReturn .= '<label>'.__('Subtitle', AEMI_TEXT_DOMAIN).':</label>';
            $htmlReturn .= '</div>';
            $htmlReturn .= '<div class="aemi-custom-fields__box__input-container">';
            $htmlReturn .= '<input type="text" name="'.$args['prefix'].'[subtitle]" class="aemi-custom-fields__box__input" value="'.$subtitle.'">';
            $htmlReturn .= '</div>';
            $htmlReturn .= '</div>';

            return $htmlReturn;
        }

        public function generateFieldsStepThree($args)
        {
            $data = $args['data'];
            $title = (isset($data['title']) && $data['title'] ? esc_attr($data['title']) : '');
            $description = (isset($data['description']) && $data['description'] ? $data['description'] : '');
            $name = (isset($data['name']) && $data['name'] ? esc_attr($data['name']) : '');
            $subtitle = (isset($data['subtitle']) && $data['subtitle'] ? esc_attr($data['subtitle']) : '');
            $thumbnail_id = (isset($data['thumbnail_id']) && $data['thumbnail_id'] ? esc_attr($data['thumbnail_id']) : '');

            $htmlReturn = '';

            // Title
            $htmlReturn .= '<div class="aemi-custom-fields__box">';
            $htmlReturn .= '<div class="aemi-custom-fields__box__title">';
            $htmlReturn .= '<label>'.__('Title', AEMI_TEXT_DOMAIN).':</label>';
            $htmlReturn .= '</div>';
            $htmlReturn .= '<div class="aemi-custom-fields__box__input-container">';
            $htmlReturn .= '<input type="text" name="'.$args['prefix'].'[title]" class="aemi-custom-fields__box__input" value="'.$title.'">';
            $htmlReturn .= '</div>';
            $htmlReturn .= '</div>';

            // Description
            $htmlReturn .= '<div class="aemi-custom-fields__box align-to-top">';
            $htmlReturn .= '<div class="aemi-custom-fields__box__title">';
            $htmlReturn .= '<label>'.__('Content', AEMI_TEXT_DOMAIN).':</label>';
            $htmlReturn .= '</div>';
            $htmlReturn .= '<div class="aemi-custom-fields__box__input-container">';
            $htmlReturn .= '<textarea name="'.$args['prefix'].'[description]" class="aemi-custom-fields__box__input" rows="4">'.$description.'</textarea>';
            $htmlReturn .= '</div>';
            $htmlReturn .= '</div>';

            $htmlReturn .= '<div>';
            $htmlReturn .= '<hr />';
            $htmlReturn .= '<h1 style="color: var( --default-blue-color );">'.__('Profile', AEMI_TEXT_DOMAIN).':</h1>';
            $htmlReturn .= '<hr />';
            $htmlReturn .= '</div>';

            // Get rendered element to working WordPress midia gallery
            $htmlReturn .= AEMIGenerateFieldsRepeat::generate_fields_image([
              'input_name' => $args['prefix'].'[thumbnail_id]',
              'input_value' => $thumbnail_id,
            ]);

            // Name
            $htmlReturn .= '<div class="aemi-custom-fields__box">';
            $htmlReturn .= '<div class="aemi-custom-fields__box__title">';
            $htmlReturn .= '<label>'.__('Name', AEMI_TEXT_DOMAIN).':</label>';
            $htmlReturn .= '</div>';
            $htmlReturn .= '<div class="aemi-custom-fields__box__input-container">';
            $htmlReturn .= '<input type="text" name="'.$args['prefix'].'[name]" class="aemi-custom-fields__box__input" value="'.$name.'">';
            $htmlReturn .= '</div>';
            $htmlReturn .= '</div>';

            // Subtitle
            $htmlReturn .= '<div class="aemi-custom-fields__box">';
            $htmlReturn .= '<div class="aemi-custom-fields__box__title">';
            $htmlReturn .= '<label>'.__('Subtitle', AEMI_TEXT_DOMAIN).':</label>';
            $htmlReturn .= '</div>';
            $htmlReturn .= '<div class="aemi-custom-fields__box__input-container">';
            $htmlReturn .= '<input type="text" name="'.$args['prefix'].'[subtitle]" class="aemi-custom-fields__box__input" value="'.$subtitle.'">';
            $htmlReturn .= '</div>';
            $htmlReturn .= '</div>';

            return $htmlReturn;
        }

        public function generateAdminStepContentLang($args)
        {
            $htmlReturn = '';

            switch ($args['step']) {
                case 1:
                    $htmlReturn .= $this->generateFieldsStepOne($args);
                    break;

                case 2:
                    $htmlReturn .= $this->generateFieldsStepTwo($args);
                    break;

                case 3:
                    $htmlReturn .= $this->generateFieldsStepThree($args);
                    break;
            }

            return $htmlReturn;
        }

        public function generateAdminStepContent($args)
        {
            $langs = $args['langs'];

            foreach ($langs as $lang_key => $lang) {
                if ($lang['language'] == 'en_US') {
                    continue;
                }

                $langs[$lang_key]['tab_name'] = __($lang['native_name'], AEMI_TEXT_DOMAIN);
                $langs[$lang_key]['tab_content'] = $this->generateAdminStepContentLang(
                    [
                      'prefix' => $args['prefix'].'['.$lang['language'].']',
                      'step' => $args['step'],
                      'data' => ($args['data'] && isset($args['data'][$lang['language']]) && $args['data'][$lang['language']] ? $args['data'][$lang['language']] : []),
                    ]
                );
            }

            $tabs = new AEMIAutoGenerateAdminTabs($langs);

            return $tabs->run();
        }

        /**
         * Save data after submit.
         */
        public function save_settings()
        {
            if (
                isset($_POST) && isset($_POST[self::$nonce_name]) && wp_verify_nonce($_POST[self::$nonce_name], self::$nonce_action)
                && isset($_POST['step'])
            ) {
                $step = wporg_recursive_sanitize_text_field($_POST['step']);
                update_option(self::$option_meta_name, $step);
            }
        }

        public function submenu_page()
        {
            $langs = get_aemi_wp_get_available_translations();
            $options = self::get_options();

            $steps = [
              [
                'tab_name' => __('Step 1', AEMI_TEXT_DOMAIN),
                'tab_content' => $this->generateAdminStepContent(
                    [
                      'prefix' => 'step[1]',
                      'langs' => $langs,
                      'step' => 1,
                      'data' => ($options && isset($options[1]) && $options[1] ? $options[1] : []),
                    ]
                ),
              ],
              [
                'tab_name' => __('Step 2', AEMI_TEXT_DOMAIN),
                'tab_content' => $this->generateAdminStepContent(
                    [
                      'prefix' => 'step[2]',
                      'langs' => $langs,
                      'step' => 2,
                      'data' => ($options && isset($options[2]) && $options[2] ? $options[2] : []),
                    ]
                ),
              ],
              [
                'tab_name' => __('Step 3', AEMI_TEXT_DOMAIN),
                'tab_content' => $this->generateAdminStepContent(
                    [
                      'prefix' => 'step[3]',
                      'langs' => $langs,
                      'step' => 3,
                      'data' => ($options && isset($options[3]) && $options[3] ? $options[3] : []),
                    ]
                ),
              ],
            ];
            $generateTabs = new AEMIAutoGenerateAdminTabs($steps);
            $tabs = $generateTabs->run();
            ?>
      <div class="wrap">
        <h2><?php echo __('First access messages', AEMI_TEXT_DOMAIN); ?></h2>
        
        <form action="" method="POST" style="background: #fff; padding: 30px; border-radius: 10px; border: 1px solid #ccc;">
          <?php
                  wp_nonce_field(self::$nonce_action, self::$nonce_name);
            echo $tabs;
            ?>

          <br />
          <br />
          <hr />
          <br />

          <div class="">
            <h3 style="margin-bottom: -8px;"><?php echo __('Short codes'); ?></h3>
            <p style="margin-bottom: 30px;"><?php echo __('Use these small code snippets to return information', AEMI_TEXT_DOMAIN); ?></p>

            <div class="aemi-custom-box-to-list-shortcodes">
              <ul>
                <li>
                  <strong>[aemi_current_user_name]</strong> - <?php echo __('Returns the username', AEMI_TEXT_DOMAIN); ?>
                </li>
              </ul>
            </div>
          </div>

          <br />
          <hr />
          <br />
          <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />

        </form>

      </div>
      <?php
        }

        /**
         * ===========================================================================================
         *                                  FRONT-END
         * ===========================================================================================.
         */

        /**
         * Return default interests options user.
         */
        public static function get_interests_type()
        {
            return [
              'concrete_case' => __('Concrete case', AEMI_TEXT_DOMAIN),
              'general_interest' => __('General interest', AEMI_TEXT_DOMAIN),
              'both' => __('Both', AEMI_TEXT_DOMAIN),
            ];
        }

        /**
         * Control show or not show first step login users.
         */
        public function detect_and_show_first_step_login()
        {
            // user logged
            if (is_user_logged_in()) {
                $user_viewed_presentation = AEMIFirstAccessLoginStepsModal::user_viewed_presentation();

                // case user not viewed modal of first step login, show to user
                if ($user_viewed_presentation == false) {
                    add_filter('body_class', [$this, 'add_class_in_body']);
                    add_action('wp_footer', [$this, 'load_modal_template']);
                }
            }
        }

        /**
         * Add class in body html.
         */
        public function add_class_in_body($class)
        {
            $currentStep = self::get_user_current_step(get_current_user_id());

            if ($currentStep < 4) {
                $class[] = 'aemi-first-access-login-steps-modal-block-body';
            }

            return $class;
        }

        /**
         * Detect and save interests.
         */
        public function save_first_steps_information()
        {
            if (
                isset($_POST['aemi-form-steps'])
                && $_POST['aemi-form-steps'] == self::get_user_current_step()
                && isset($_POST[self::$frontend_modal_meta_field_name])
                && $_POST[self::$frontend_modal_meta_field_name]
                && wp_verify_nonce($_POST[self::$frontend_modal_meta_field_name], self::$frontend_modal_nonce_action)
            ) {
                $userID = get_current_user_id();

                switch ($_POST['aemi-form-steps']) {
                    case 1:
                        $interests = self::get_interests_type();
                        $insterestPost = (isset($_POST['first_step_interest']) && $_POST['first_step_interest'] ? sanitize_text_field($_POST['first_step_interest']) : false);

                        if ($insterestPost && isset($interests[$insterestPost])) {
                            update_user_meta($userID, self::$user_meta_current_step, 2);
                            update_user_meta($userID, self::$user_meta_user_interest_type, $insterestPost);

                            do_action('activecampaign_update_insterest_type', $userID);
                        }
                        break;

                    case 2:
                        $interests = [];
                        if (isset($_POST['interest_user']) && $_POST['interest_user']) {
                            foreach ($_POST['interest_user'] as $interest) {
                                if (is_numeric(sanitize_text_field($interest))) {
                                    $interests[] = sanitize_text_field($interest);
                                }
                            }

                            if (count($interests) >= 0) {
                                update_user_meta($userID, self::$user_meta_current_step, 3);
                                update_user_meta($userID, self::$user_meta_user_interest, $interests);

                                do_action('activecampaign_update_insterest', $userID);
                            }
                        }

                        break;

                    case 3:
                        update_user_meta($userID, self::$user_meta_current_step, 4);
                        break;

                    case 4:
                        update_user_meta($userID, self::$user_meta_current_step, 5);
                        break;
                }
            }
        }

        /**
         * Load template modal.
         */
        public function load_modal_template()
        {
            $currentStep = self::get_user_current_step(get_current_user_id());

            if ($currentStep < 5) {
                $nonce = wp_nonce_field(self::$frontend_modal_nonce_action, self::$frontend_modal_meta_field_name, true, false);

                echo get_template_part('includes/first-access-login-steps-modal/index', null, [
                  'aemiStep' => $currentStep,
                  'nonce' => $nonce,
                ]);
            }
        }

        /**
         * Return information user has viewed the presentation modal.
         *
         * @param int $userID - Optional information user id, default use current user id
         *
         * @return bool
         */
        public static function user_viewed_presentation($userID = false)
        {
            $userID = ($userID ? $userID : (is_user_logged_in() && get_current_user_id() ? get_current_user_id() : false));

            if ($userID) {
                $meta = get_user_meta($userID, self::$meta_field_name, true);

                return $meta ? true : false;
            }

            return false;
        }
    }

    new AEMIFirstAccessLoginStepsModal();
}
