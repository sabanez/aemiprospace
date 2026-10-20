<?php

class AEMIcustomUpdatesInfoMyAccountPage
{
    public static $nonce_action = 'sdDLzr5UjMy-c48evcPL7X-PNDYp';
    public static $nonce_name = 'lNCCjGT8JdI5GE3xBVrH48nA';
    public static $nonce_account_data_action = 'LmIXS;!TM,ZywY.0l6,9sWVukrZ@BW';
    public static $nonce_account_data_name = 'kh2NqWl4fHoybg0qJd0smKJMwv8Gc';
    public static $nonce_shipping_billing_action = 'dtlzdlI_R6HSae7wwZoizxBNnCVCQu';
    public static $nonce_shipping_billing_name = 'wenrE1jAagSV65t_eBlmBg20kUDodN';

    public function __construct()
    {
        add_action('init', [$this, 'init']);
    }

    public function init()
    {
        add_shortcode('render_nonce_field_password_reset_form', [$this, 'render_nonce_field_password_reset_form']);
        add_shortcode('render_nonce_field_update_data_reset_form', [$this, 'render_nonce_field_update_data_reset_form']);

        /*
         * =============================================================================================
         *                                        PASSWORD
         * =============================================================================================
         */
        add_action('wp_ajax_aemi_my_account_reset_password', [$this, 'aemi_my_account_reset_password']);
        add_action('wp_ajax_nopriv_aemi_my_account_reset_password', [$this, 'aemi_my_account_reset_password']);

        add_action('wp_ajax_aemi_my_account_reset_password_nonce', [$this, 'aemi_my_account_reset_password_nonce']);
        add_action('wp_ajax_nopriv_aemi_my_account_reset_password_nonce', [$this, 'aemi_my_account_reset_password_nonce']);

        /*
         * =============================================================================================
         *                                        Personal data
         * =============================================================================================
         */
        add_action('wp_ajax_aemi_my_account_update_data', [$this, 'aemi_my_account_update_data']);
        add_action('wp_ajax_nopriv_aemi_my_account_update_data', [$this, 'aemi_my_account_update_data']);

        add_action('wp_ajax_aemi_my_account_update_data_nonce', [$this, 'aemi_my_account_update_data_nonce']);
        add_action('wp_ajax_nopriv_aemi_my_account_update_data_nonce', [$this, 'aemi_my_account_update_data_nonce']);

        /*
         * =============================================================================================
         *                                        Personal data
         * =============================================================================================
         */
        add_action('wp_ajax_aemi_my_account_update_edit_billing_data', [$this, 'aemi_my_account_update_edit_billing_data']);
        add_action('wp_ajax_nopriv_aemi_my_account_update_edit_billing_data', [$this, 'aemi_my_account_update_edit_billing_data']);
    }

    public function render_nonce_field_password_reset_form()
    {
        if (is_user_logged_in()) {
            $html = wp_nonce_field(self::$nonce_action, self::$nonce_name, true, false);

            return $html;
        }

        return '';
    }

    public function aemi_my_account_reset_password_nonce()
    {
        return wp_send_json_success(['t' => $this->render_nonce_field_password_reset_form()]);
    }

    public function aemi_my_account_reset_password()
    {
        if (is_user_logged_in()) {
            check_ajax_referer(self::$nonce_action, self::$nonce_name);

            $new_password = $_POST['new-password'];
            $user_id = get_current_user_id();
            $data = [
                'message' => 'Usted no se ha autentificado.',
                'success' => false,
            ];

            if (preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^&*()_+{}[\]:;<>,.?~\\-]).{5,}$/', $new_password)) {
                $user = wp_get_current_user();

                wp_set_password($new_password, $user_id);
                wp_set_auth_cookie($user_id);

                $data['message'] = '¡Contraseña actualizada exitosamente!';
                $data['success'] = true;

                wp_send_json_success($data);
            } else {
                if (strlen($new_password) < 5) {
                    $error_messages = 'La contraseña debe tener al menos 5 caracteres.';
                }

                if (!preg_match('/[A-Z]/', $new_password)) {
                    $error_messages = 'La contraseña debe contener al menos 1 carácter en mayúscula.';
                }

                if (!preg_match('/[a-z]/', $new_password)) {
                    $error_messages = 'La contraseña debe contener al menos una letra minúscula.';
                }

                if (!preg_match('/[0-9]/', $new_password)) {
                    $error_messages = 'La contraseña debe contener al menos un número.';
                }

                if (!preg_match('/[!@#$%^&*()_+{}[\]:;<>,.?~\\-]/', $new_password)) {
                    $error_messages = 'La contraseña debe contener al menos un carácter especial.';
                }

                $data['message'] = $error_messages;
                wp_send_json_success($data);
            }
        }

        $data['message'] = 'Usted no se ha autentificado.';
        wp_send_json_error($data);
    }

    public function render_nonce_field_update_data_reset_form()
    {
        if (is_user_logged_in()) {
            $html = wp_nonce_field(self::$nonce_account_data_action, self::$nonce_account_data_name, true, false);

            return $html;
        }

        return '';
    }

    public function aemi_my_account_update_data_nonce()
    {
        return wp_send_json_success(['t' => $this->render_nonce_field_update_data_reset_form()]);
    }

    public function aemi_my_account_update_data()
    {
        $data = [
            'message' => 'Usted no se ha autentificado.',
            'success' => false,
        ];

        if (is_user_logged_in()) {
            check_ajax_referer(self::$nonce_account_data_action, self::$nonce_account_data_name);

            $user_id = get_current_user_id();
            $current_user = wp_get_current_user();

            $first_name = (isset($_POST['fname']) && $_POST['fname'] ? sanitize_text_field($_POST['fname']) : '');
            $last_name = (isset($_POST['lname']) && $_POST['lname'] ? sanitize_text_field($_POST['lname']) : '');
            $user_email = (isset($_POST['email']) && $_POST['email'] ? sanitize_email($_POST['email']) : '');

            $error = '';

            if (!$first_name) {
                $error = '¡Por favor complete el campo de nombre correctamente!';
            }
            if (!$last_name) {
                $error .= ($error ? "\n" : '').'¡Por favor complete el campo de apellido correctamente!';
            }

            if (!$user_email) {
                $error .= ($error ? "\n" : '').'¡Por favor complete el campo de correo electrónico correctamente!';
            } elseif ($user_email !== $current_user->user_email && email_exists($user_email)) {
                $error .= ($error ? "\n" : '').'¡El correo electrónico ya está en uso por otro usuario!';
            }

            if (empty($error)) {
                // Atualize os dados do usuário
                wp_update_user([
                    'ID' => $user_id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'user_email' => $user_email,
                ]);

                $data['message'] = 'Datos actualizados exitosamente.';
                $data['success'] = true;
            } else {
                $data['message'] = $error;
            }
        }

        wp_send_json($data);
    }

    /**
     * This code is a copy of the original, with a small difference at the end.
     * It was necessary to remove the redirect.
     *
     * To see the original code access the file plugins/woocommerce/includes/class-wc-form-handler.php
     *
     * Save and and update a billing or shipping address if the
     * form was submitted through the user account page.
     */
    public static function save_address($is_shipping = false)
    {
        global $wp;

        $nonce_value = wc_get_var($_REQUEST['woocommerce-edit-address-nonce'], wc_get_var($_REQUEST['_wpnonce'], '')); // @codingStandardsIgnoreLine.
        if (!wp_verify_nonce($nonce_value, 'woocommerce-edit_address')) {
            return;
        }

        if (empty($_POST['action']) || 'edit_address' !== $_POST['action']) {
            return;
        }

        wc_nocache_headers();

        $user_id = get_current_user_id();

        if ($user_id <= 0) {
            return;
        }

        $customer = new WC_Customer($user_id);

        if (!$customer) {
            return;
        }

        // I needed to modify this line too
        // $load_address = isset($wp->query_vars['edit-address']) ? wc_edit_address_i18n(sanitize_title($wp->query_vars['edit-address']), true) : 'billing';
        $load_address = $is_shipping ? wc_edit_address_i18n(sanitize_title('shipping'), true) : 'billing';

        if (!isset($_POST[$load_address.'_country'])) {
            // wc_add_notice('teste', 'teste', 'error');
            return;
        }

        $address = WC()->countries->get_address_fields(wc_clean(wp_unslash($_POST[$load_address.'_country'])), $load_address.'_');

        foreach ($address as $key => $field) {
            if (!isset($field['type'])) {
                $field['type'] = 'text';
            }

            // Get Value.
            if ('checkbox' === $field['type']) {
                $value = (int) isset($_POST[$key]);
            } else {
                $value = isset($_POST[$key]) ? wc_clean(wp_unslash($_POST[$key])) : '';
            }

            // Hook to allow modification of value.
            $value = apply_filters('woocommerce_process_myaccount_field_'.$key, $value);

            // Validation: Required fields.
            if (!empty($field['required']) && empty($value)) {
                /* translators: %s: Field name. */
                wc_add_notice(sprintf(__('%s is a required field.', 'woocommerce'), $field['label']), 'error', ['id' => $key]);
            }

            if (!empty($value)) {
                // Validation and formatting rules.
                if (!empty($field['validate']) && is_array($field['validate'])) {
                    foreach ($field['validate'] as $rule) {
                        switch ($rule) {
                            case 'postcode':
                                $country = wc_clean(wp_unslash($_POST[$load_address.'_country']));
                                $value = wc_format_postcode($value, $country);

                                if ('' !== $value && !WC_Validation::is_postcode($value, $country)) {
                                    switch ($country) {
                                        case 'IE':
                                            $postcode_validation_notice = __('Please enter a valid Eircode.', 'woocommerce');
                                            break;
                                        default:
                                            $postcode_validation_notice = __('Please enter a valid postcode / ZIP.', 'woocommerce');
                                    }
                                    wc_add_notice($postcode_validation_notice, 'error');
                                }
                                break;
                            case 'phone':
                                if ('' !== $value && !WC_Validation::is_phone($value)) {
                                    /* translators: %s: Phone number. */
                                    wc_add_notice(sprintf(__('%s is not a valid phone number.', 'woocommerce'), '<strong>'.$field['label'].'</strong>'), 'error');
                                }
                                break;
                            case 'email':
                                $value = strtolower($value);

                                if (!is_email($value)) {
                                    /* translators: %s: Email address. */
                                    wc_add_notice(sprintf(__('%s is not a valid email address.', 'woocommerce'), '<strong>'.$field['label'].'</strong>'), 'error');
                                }
                                break;
                        }
                    }
                }
            }

            try {
                // Set prop in customer object.
                if (is_callable([$customer, "set_$key"])) {
                    $customer->{"set_$key"}($value);
                } else {
                    $customer->update_meta_data($key, $value);
                }
            } catch (WC_Data_Exception $e) {
                // Set notices. Ignore invalid billing email, since is already validated.
                if ('customer_invalid_billing_email' !== $e->getErrorCode()) {
                    wc_add_notice($e->getMessage(), 'error');
                }
            }
        }

        /*
         * Hook: woocommerce_after_save_address_validation.
         *
         * Allow developers to add custom validation logic and throw an error to prevent save.
         *
         * @param int         $user_id User ID being saved.
         * @param string      $load_address Type of address e.g. billing or shipping.
         * @param array       $address The address fields.
         * @param WC_Customer $customer The customer object being saved. @since 3.6.0
         */
        do_action('woocommerce_after_save_address_validation', $user_id, $load_address, $address, $customer);

        if (0 < wc_notice_count('error')) {
            return;
        }

        $customer->save();

        wc_add_notice(__('Address changed successfully.', 'woocommerce'));

        do_action('woocommerce_customer_save_address', $user_id, $load_address);

        // Here it was necessary to comment
        // wp_safe_redirect(wc_get_endpoint_url('edit-address', '', wc_get_page_permalink('myaccount')));
        // exit;
    }

    public function aemi_my_account_update_edit_billing_data()
    {
        $data = [
            'message' => 'Usted no se ha autentificado.',
            'success' => false,
        ];

        if (is_user_logged_in()) {
            check_ajax_referer(self::$nonce_shipping_billing_action, self::$nonce_shipping_billing_name);

            $_POST['action'] = 'edit_address';
            self::save_address(isset($_POST['shipping_first_name']) || isset($_POST['shipping_country']));

            $notices = wc_get_notices();

            $data['success'] = (isset($notices['success']) ? true : false);

            ob_start();
            woocommerce_output_all_notices();
            $getHTMLWoocommerceNotices = ob_get_contents();
            ob_end_clean();

            $data['message'] = $getHTMLWoocommerceNotices;
        }

        wp_send_json($data);
        exit;
    }

    public static function nonce_shipping_billing_action()
    {
        return wp_create_nonce(self::$nonce_shipping_billing_action);
    }

    public static function nonce_shipping_billing_name()
    {
        return self::$nonce_shipping_billing_name;
    }
}

new AEMIcustomUpdatesInfoMyAccountPage();
