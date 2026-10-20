<?php

if (!class_exists('AEMICustomizeSimpleMembership__CustomPageInWoo')) {
    class AEMICustomizeSimpleMembership__CustomPageInWoo
    {
        public $nonce_name = 'aemi_membership_inoice_pdf';
        public $nonce_action = 'aemi_membership_inoice_pdf_action';

        public static $metaPostSwpmInvoice = 'aemi_swpm_invoice';
        public static $metaPostPaymentTransactionRelatedUser = 'aemi_swpm_user_related';
        public static $simpleMembershipWooSlug = 'subscription-payments';
        public static $metaPostStatusSubscriptionOnGateway = 'status_subscription_on_gateway_canceled_or_refund';
        public static $metaPostStatusSubscriptionOnGatewayDate = 'status_subscription_on_gateway_canceled_or_refund_date';

        public function __construct()
        {
            add_action('init', [$this, 'aemi_add_my_subscription_payments_endpoint']);
            add_filter('query_vars', [$this, 'aemi_my_subscription_payments_query_vars'], 0);
            add_action('woocommerce_account_'.self::$simpleMembershipWooSlug.'_endpoint', [$this, 'aemi_subscription_payments_content']);
            add_action('swpm_txn_record_saved', [$this, 'swpm_txn_record_saved'], 99, 3);
            add_action('swpm_subscription_payment_cancelled', [$this, 'swpm_subscription_payment_cancelled'], 99, 1);

            add_action('wp_ajax_aemi_download_invoice_membership', [$this, 'aemiDownloadInvoiceMembership']);
            add_action('wp_ajax_aemi_cancel_subscription_membership', [$this, 'aemiCancelSubscriptionMembership']);

            add_action('admin_menu', [$this, 'wpdocs_remove_post_custom_fields'], 9999);

            add_filter('woocommerce_registration_auth_new_customer', [$this, 'woocommerce_registration_auth_new_customer'], 999, 2);
        }

        public function wpdocs_remove_post_custom_fields()
        {
            if (function_exists('add_meta_box')) {
                $post_types = get_post_types();
                foreach ($post_types as $post_type => $post_type) {
                    remove_meta_box('swpm_sectionid', $post_type, 'advanced');
                }
            }
        }

        /**
         * Modifica o login automatico do woocommerce, desabilitando o login automatico
         * e força um login pela função padrão do WordPress, isso vai acontecer para adicionar
         * compatibilidade com o plugin wp simple membership.
         */
        public function woocommerce_registration_auth_new_customer($auth, $new_customer)
        {
            $creds = [
                'user_login' => trim(wp_unslash($_POST['email'])), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                'user_password' => $_POST['password'], // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
            ];

            $user = wp_signon(apply_filters('woocommerce_login_credentials', $creds), is_ssl());

            if (is_wp_error($user)) {
                throw new Exception($user->get_error_message());
            } else {
                if (!empty($_POST['redirect'])) {
                    $redirect = wp_sanitize_redirect(wp_unslash($_POST['redirect'])); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                } elseif (wc_get_raw_referer()) {
                    $redirect = wc_get_raw_referer();
                } else {
                    $redirect = wc_get_page_permalink('myaccount');
                }

                wp_redirect(wp_validate_redirect(apply_filters('woocommerce_registration_redirect', $redirect), wc_get_page_permalink('myaccount')));

                exit;
            }
        }

        public static function get_current_membership_plan_info()
        {
            $user_id = get_current_user_id();

            $auth = SwpmAuth::get_instance();
            $user_data = (array) $auth->userData;
            $user_data['membership_level_alias'] = $auth->get('alias');
            $user_data['membership_level'] = $auth->get('membership_level');
            $user_data['subscription_period'] = $auth->get('subscription_period');

            $expiry_timestamp = (isset($user_data['member_id']) ? SwpmMemberUtils::get_expiry_date_timestamp_by_user_id($user_data['member_id']) : 0);
            $user_data['expiry_timestamp'] = $expiry_timestamp;

            // id dois significa que é usuário gratuito
            if ($user_data['membership_level'] != 2 && $user_data['account_state'] != 'active') {
                $opts = get_option('swpm-settings');
                $membership_info = SwpmPermission::get_instance($opts['free-membership-id']);

                if ($membership_info) {
                    $user_data['membership_level'] = $membership_info->get('id');
                    $user_data['membership_level_alias'] = $membership_info->get('alias');
                    $user_data['subscription_duration_type'] = $membership_info->get('subscription_duration_type');
                    $user_data['subscription_period'] = $membership_info->get('subscription_period');

                    if (!$user_data['subscription_duration_type'] || $user_data['subscription_duration_type'] == false || $user_data['subscription_duration_type'] == 0) {
                        $user_data['expiry_timestamp'] = PHP_INT_MAX;
                    }
                }
            }

            $transactions = get_posts([
                'posts_per_page' => 9999,
                'post_type' => 'swpm_transactions',
                'meta_query' => [
                    'relation' => 'and',
                    [
                        'key' => self::$metaPostPaymentTransactionRelatedUser,
                        'value' => get_current_user_id(),
                        'compare' => '=',
                    ],
                    [
                        'key' => 'subscr_id',
                        'value' => $user_data['subscr_id'],
                        'compare' => '=',
                    ],
                    // array(
                    //     'key' => 'txn_date',
                    //     'value' => $user_data['subscription_starts'],
                    //     'compare' => '='
                    // ),
                    [
                        'key' => 'member_id',
                        'value' => $user_data['member_id'],
                        'compare' => '=',
                    ],
                    [
                        'key' => 'email',
                        'value' => $user_data['email'],
                        'compare' => '=',
                    ],
                ],
            ]);

            // validou uma transação e o usuário atual
            if ($transactions) {
                $transactions[0]->meta = get_post_meta($transactions[0]->ID);
                $user_data['transaction'] = $transactions[0];
            }

            return $user_data;
        }

        public function aemi_add_my_subscription_payments_endpoint()
        {
            add_rewrite_endpoint(self::$simpleMembershipWooSlug, EP_ROOT | EP_PAGES);
        }

        public function aemi_my_subscription_payments_query_vars($vars)
        {
            $vars[] = self::$simpleMembershipWooSlug;

            return $vars;
        }

        /**
         * Atualiza o status da transação atual com o status no gateway de pagamento.
         */
        public function swpm_subscription_payment_cancelled($ipn_data)
        {
            if (
                isset($ipn_data['subscr_id']) && $ipn_data['subscr_id'] &&
                isset($ipn_data['parent_txn_id']) && $ipn_data['parent_txn_id']
            ) {
                $input = @file_get_contents('php://input');
                if (!empty($input)) {
                    $event_json = json_decode($input);
                    $type = $event_json->type;

                    if ('customer.subscription.deleted' === $type || 'charge.refunded' === $type) {
                        /**
                         * Retorna a transação orignal que deu inicio a inscrição.
                         */
                        $transactions = get_posts([
                            'posts_per_page' => 9999,
                            'post_type' => 'swpm_transactions',
                            'meta_query' => [
                                'relation' => 'and',
                                [
                                    'key' => 'subscr_id',
                                    'value' => $ipn_data['subscr_id'],
                                    'compare' => '=',
                                ],
                                [
                                    'key' => 'txn_id',
                                    'value' => $ipn_data['parent_txn_id'],
                                    'compare' => '=',
                                ],
                            ],
                        ]);

                        // validou uma transação e o usuário atual
                        if ($transactions) {
                            foreach ($transactions as $transaction) {
                                update_post_meta($transaction->ID, self::$metaPostStatusSubscriptionOnGateway.'_original', $type);
                                update_post_meta($transaction->ID, self::$metaPostStatusSubscriptionOnGatewayDate.'_original', date('Y-m-d H:i:s'));
                            }
                        }

                        /**
                         * Retorna todas as transações da inscrição.
                         */
                        $transactions = get_posts([
                            'posts_per_page' => 9999,
                            'post_type' => 'swpm_transactions',
                            'meta_query' => [
                                'relation' => 'and',
                                [
                                    'key' => 'subscr_id',
                                    'value' => $ipn_data['subscr_id'],
                                    'compare' => '=',
                                ],
                            ],
                        ]);

                        // validou uma transação e o usuário atual
                        if ($transactions) {
                            foreach ($transactions as $keyL => $transaction) {
                                if ($keyL == 0) {
                                    update_post_meta($transaction->ID, self::$metaPostStatusSubscriptionOnGateway.'__canceled_on_this_renewal', true);
                                }
                                update_post_meta($transaction->ID, self::$metaPostStatusSubscriptionOnGateway, $type);
                                update_post_meta($transaction->ID, self::$metaPostStatusSubscriptionOnGatewayDate, date('Y-m-d H:i:s'));
                            }
                        }
                    }
                }
            }
        }

        /**
         * Adding transaction related user ID.
         */
        public function swpm_txn_record_saved($txn_data, $db_row_id, $post_id)
        {
            if (isset($txn_data['email']) && $txn_data['email'] && $post_id) {
                $get_user = get_user_by('email', $txn_data['email']);

                if ($get_user && $get_user->data->user_email) {
                    $metaPostPaymentTransactionRelatedUser = get_post_meta($post_id, self::$metaPostPaymentTransactionRelatedUser, true);

                    if (!$metaPostPaymentTransactionRelatedUser) {
                        update_post_meta($post_id, self::$metaPostPaymentTransactionRelatedUser, $get_user->ID);
                    }
                }
            }

            if (isset($txn_data['subscr_id']) && $txn_data['subscr_id'] && $post_id) {
                $strip_key = $this->get_keys_stripe();

                $stripe = new \Stripe\StripeClient(
                    $strip_key['secret']
                );

                $stripe_invoice = $stripe->subscriptions->retrieve(
                    $txn_data['subscr_id'],
                    []
                );

                if ($stripe_invoice && $stripe_invoice->latest_invoice) {
                    update_post_meta($post_id, self::$metaPostSwpmInvoice, $stripe_invoice->latest_invoice);
                }
            }

            if (isset($txn_data['email']) && $txn_data['email'] && $post_id) {
                $get_user = get_user_by('email', $txn_data['email']);

                if ($get_user && $get_user->data->user_email) {
                    do_action('activecampaign_swpm_update_plan', $get_user->ID, $post_id);
                }
            }
        }

        public function get_keys_stripe()
        {
            $keys = [
                'public' => '',
                'secret' => '',
            ];

            $opts = get_option('swpm-settings');
            $live = $opts['enable-sandbox-testing'];

            if (!$live) {
                $keys['public'] = isset($opts['stripe-live-public-key']) ? $opts['stripe-live-public-key'] : '';
                $keys['secret'] = isset($opts['stripe-live-secret-key']) ? $opts['stripe-live-secret-key'] : '';
            } else {
                $keys['public'] = isset($opts['stripe-test-public-key']) ? $opts['stripe-test-public-key'] : '';
                $keys['secret'] = isset($opts['stripe-test-secret-key']) ? $opts['stripe-test-secret-key'] : '';
            }

            return $keys;
        }

        public function aemi_subscription_payments_content()
        {
            $aemi_current_user = wp_get_current_user();
            $user_data = (array) self::get_current_membership_plan_info();
            $member_current_expiry_date = false;
            $noexpire = false;

            $expiry_timestamp = $user_data['expiry_timestamp'];

            if ($expiry_timestamp == PHP_INT_MAX) {
                // No Expiry Setting
                $member_current_expiry_date = __('No expiry', AEMI_TEXT_DOMAIN);
                $noexpire = true;
            } else {
                $expiry_date = date('Y-m-d', $expiry_timestamp);
                // $member_current_expiry_date = SwpmUtils::get_formatted_date_according_to_wp_settings( $expiry_date );
                $member_current_expiry_date = date('d/m/Y', $expiry_timestamp);
            }

            $subscription_starts = SwpmUtils::get_formatted_date_according_to_wp_settings($user_data['subscription_starts']);
            $transactions = get_posts([
                'posts_per_page' => 9999,
                'post_type' => 'swpm_transactions',
                'meta_query' => [
                    [
                                'key' => self::$metaPostPaymentTransactionRelatedUser,
                                'value' => $aemi_current_user->ID,
                                'compare' => '=',
                    ],
                ],
            ]);

            foreach ($transactions as $key => $value) {
                $transactions[$key]->meta = get_post_meta($value->ID);
            }
            ?>
           
            <div class="aemi-new-layout-page-user-payment">
                <div class="paymentCard card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo __('Mis pagos', AEMI_TEXT_DOMAIN); ?></h3>
                    </div>
                    <div class="card-content">
                        <?php if ($transactions) { ?>
                        <p class="pageDescription">A continuación se muestran los detalles de los pagos que ha realizado en relación con sus suscripciones activas. También puede descargar la factura relacionada con cada pago.</p>
                        
                        <div class="filtWeb">
                            <div class="filter">
                                <div class="caseName">
                    <div class="filtWeb">
                        <div class="filter">
                            <div class="caseName">
                                <table>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Fecha</th>
                                            <th>Monto</th>
                                            <th>Nivel</th>
                                            <th>Gateway</th>
                                            <th>Estatus</th>
                                            <th>Factura</th>
                                        </tr>
                                        <?php
                                    $loop = 0;
                            foreach ($transactions as $transaction) {
                                $db_row_id = get_post_meta($transaction->ID, 'db_row_id', true);
                                $membership_level = get_post_meta($transaction->ID, 'membership_level', true);
                                $txn_date = get_post_meta($transaction->ID, 'txn_date', true);
                                $status = get_post_meta($transaction->ID, 'status', true);
                                $gateway = get_post_meta($transaction->ID, 'gateway', true);
                                $payment_amount = get_post_meta($transaction->ID, 'payment_amount', true);
                                $txn_id = get_post_meta($transaction->ID, 'txn_id', true);

                                $membership_info = SwpmPermission::get_instance($membership_level);
                                ++$loop;
                                ?>
                                        <tr class="open-case">
                                            <td>#<?php echo $loop; ?></td>
                                            <td><?php echo date('M d, Y g:i A', strtotime($txn_date)); ?></td>
                                            <td>€ <?php echo number_format($payment_amount, 2, ',', ''); ?></td>
                                            <td>
                                                <div class="planTitle" style="cursor: inherit;">
                                                    <h6><?php echo $membership_info->get('alias'); ?></h6>
                                                </div>
                                            </td>
                                            <td><?php
                                switch ($gateway) {
                                    case 'stripe-sca-subs':
                                        echo 'Stripe';
                                        break;

                                    default:
                                        echo $gateway;
                                        break;
                                }
                                ?></td>

                                            <td>
                                                <h4 class="<?php
                                                        if (
                                                            isset($transaction->meta['status_subscription_on_gateway_canceled_or_refund'][0]) &&
                                                            in_array($transaction->meta['status_subscription_on_gateway_canceled_or_refund'][0], ['customer.subscription.deleted', 'charge.refunded'])
                                                            &&
                                                            isset($transaction->meta[self::$metaPostStatusSubscriptionOnGateway.'__canceled_on_this_renewal'][0])
                                                        ) {
                                                            echo 'subTitleAutoChange';
                                                        } else {
                                                            switch ($status) {
                                                                case 'completed':
                                                                    echo 'subTitleSuccess';
                                                                    break;

                                                                case 'subscription':
                                                                    echo 'subTitleAutoChange';
                                                                    break;

                                                                default:
                                                                    echo $status.' subTitleFailed';
                                                                    break;
                                                            }
                                                        }
                                ?> " style="flex-wrap: wrap;min-height: 44px;height: auto;text-align: center;padding: 0 20px;">
                                                    <?php
                                        switch ($status) {
                                            case 'completed':
                                                echo __('Subscription successfully completed', AEMI_TEXT_DOMAIN);
                                                break;

                                            case 'subscription':
                                                echo __('Subscription automatically renewed', AEMI_TEXT_DOMAIN);
                                                break;

                                            default:
                                                echo __($status, AEMI_TEXT_DOMAIN);
                                                break;
                                        }
                                        if (
                                            isset($transaction->meta['status_subscription_on_gateway_canceled_or_refund'][0]) &&
                                            in_array($transaction->meta['status_subscription_on_gateway_canceled_or_refund'][0], ['customer.subscription.deleted', 'charge.refunded'])
                                            &&
                                            isset($transaction->meta[self::$metaPostStatusSubscriptionOnGateway.'__canceled_on_this_renewal'][0])
                                        ) {
                                            echo '<div class="subTitleFailed" style="display: flex;flex-wrap: wrap;justify-content: center;position: absolute;font-size: 11px;">';
                                            echo '<span>'.__('Subscription canceled on:', AEMI_TEXT_DOMAIN).'</span>';
                                            echo '<br />';
                                            echo '<strong>'.$transaction->meta[self::$metaPostStatusSubscriptionOnGatewayDate][0].'</strong>';
                                            echo '</div>';
                                        }
                                ?></h4>
                                            </td>

                                            <td>
                                            <?php if ($transaction && $transaction->meta[self::$metaPostSwpmInvoice]) { ?>
                                                <button style="padding: 0!important;border: none!important;background: transparent!important;border-bottom: none!important;box-shadow: unset!important;display: flex;align-items: center;" class="aemi-membership-download-invoice" type="button" data-hash="<?php echo base64_encode($transaction->ID); ?>">
                                                    <span class="flex" style="color: #4e74ef;">
                                                        <img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/download-fill.png'); ?>" alt="watch">
                                                        <p>Download</p>
                                                    </span>
                                                </button>
                                            <?php } ?>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                </table>

                            </div>
                        </div>
                    </div>
                                </div>
                            </div>
                        </div>
                            <?php
                        } else {
                            ?>
                            <div class="emptyPageDescription">
<!--                                <p>No hay información sobre suscripciones anteriores. Primero debes comprar una suscripción.</p>-->
                                <p>No hay información sobre pagos anteriores.</p>
                            </div>
                            <?php
                        }

            ?>
                    </div>
                </div>
            </div>
            <script>
                window.addEventListener('load', function(){

                    jQuery('.aemi-membership-download-invoice').click(function(){
                        if( !jQuery(this).hasClass('aemi-membership-download-invoice_disable') ){
                            var currentItemCheck = jQuery(this);
                            currentItemCheck.addClass('aemi-membership-download-invoice_disable');

                            jQuery.ajax({ 
                                url : '<?php echo get_admin_url(); ?>admin-ajax.php', 
                                type : 'POST',
                                dataType: "json",
                                data : {
                                    action : 'aemi_download_invoice_membership',
                                    hash: currentItemCheck.data('hash'),
                                    <?php echo $this->nonce_name; ?>: '<?php echo wp_create_nonce($this->nonce_action); ?>'
                                },
                                success: function( data ){

                                    if( data['type'] == 'success' ){
                                        var btnAemiPDF = document.createElement('a');
                                        btnAemiPDF.href = data['url'];
                                        btnAemiPDF.download = '';
                                        document.body.appendChild(btnAemiPDF);
                                        btnAemiPDF.click();
                                        document.body.removeChild(btnAemiPDF);
                                    } else {
                                        jQuery('body').append('<div class="get-points-gamification__notice" data-type="error">' + data['msg'] + '</div>');
                                    }

                                     if( jQuery('.get-points-gamification__notice') ){
                                        setTimeout(() => {
                                            jQuery('.get-points-gamification__notice').addClass('started');
                                            
                                            setTimeout(() => {
                                                jQuery('.get-points-gamification__notice').addClass('remove');
                                            }, 3000);
                                        }, 100);
                                     }

                                    currentItemCheck.removeClass('aemi-membership-download-invoice_disable');
                                }
                            });
                        }
                    });
                });
            </script>

           
            <?php
        }       

        public function aemiDownloadInvoiceMembership()
        {
            $returnData = [
                'type' => 'error',
                'msg' => __('For some reason it was not possible to generate the invoice file. Please try again later or contact the site administrator.', AEMI_TEXT_DOMAIN),
            ];

            if (is_user_logged_in()) {
                if (isset($_POST[$this->nonce_name]) && $_POST[$this->nonce_name] && isset($_POST['hash']) && $_POST['hash']) {
                    if (wp_verify_nonce($_POST[$this->nonce_name], $this->nonce_action)) {
                        $user_id = get_current_user_id();
                        $post_id = base64_decode($_POST['hash']);

                        if ($post_id) {
                            $metaInvoiceID = get_post_meta($post_id, self::$metaPostSwpmInvoice, true);
                            $userIDofPost = get_post_meta($post_id, self::$metaPostPaymentTransactionRelatedUser, true);

                            if ($metaInvoiceID && $userIDofPost && $userIDofPost == get_current_user_id()) {
                                $strip_key = $this->get_keys_stripe();

                                $stripe = new \Stripe\StripeClient(
                                    $strip_key['secret']
                                );

                                $data = $stripe->invoices->retrieve(
                                    $metaInvoiceID,
                                    []
                                );

                                if ($data && $data->invoice_pdf) {
                                    $returnData['type'] = 'success';
                                    $returnData['url'] = $data->invoice_pdf;
                                    unset($returnData['msg']);
                                }
                            }
                        }
                    }
                }
            }

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($returnData);
            exit;
        }

        public function aemiCancelSubscriptionMembership()
        {
            $returnData = [
                'type' => 'error',
                'msg' => __('For some reason it was not possible to cancel your subscription. Please try again, if the error persists, please contact the site administrator to request cancellation.', AEMI_TEXT_DOMAIN),
            ];

            if (is_user_logged_in()) {
                if (isset($_POST[$this->nonce_name]) && $_POST[$this->nonce_name]) {
                    if (wp_verify_nonce($_POST[$this->nonce_name], $this->nonce_action)) {
                        $user_id = get_current_user_id();

                        $auth = SwpmAuth::get_instance();
                        $user_data = (array) $auth->userData;
                        $user_data['membership_level_alias'] = $auth->get('alias');
                        $user_data['membership_level'] = $auth->get('membership_level');
                        $user_data['subscription_period'] = $auth->get('subscription_period');

                        $transactions = get_posts([
                            'posts_per_page' => 9999,
                            'post_type' => 'swpm_transactions',
                            'meta_query' => [
                                'relation' => 'and',
                                [
                                    'key' => self::$metaPostPaymentTransactionRelatedUser,
                                    'value' => get_current_user_id(),
                                    'compare' => '=',
                                ],
                                [
                                    'key' => 'subscr_id',
                                    'value' => $user_data['subscr_id'],
                                    'compare' => '=',
                                ],
                                // array(
                                //     'key' => 'txn_date',
                                //     'value' => $user_data['subscription_starts'],
                                //     'compare' => '='
                                // ),
                                [
                                    'key' => 'member_id',
                                    'value' => $user_data['member_id'],
                                    'compare' => '=',
                                ],
                                [
                                    'key' => 'email',
                                    'value' => $user_data['email'],
                                    'compare' => '=',
                                ],
                            ],
                        ]);

                        // validou uma transação e o usuário atual
                        if ($transactions) {
                            $strip_key = $this->get_keys_stripe();

                            $stripe = new \Stripe\StripeClient(
                                $strip_key['secret']
                            );

                            $return_stripe_subscriptions = $stripe->subscriptions->cancel(
                                $user_data['subscr_id'],
                                []
                            );

                            if (
                                $return_stripe_subscriptions &&
                                $return_stripe_subscriptions->status &&
                                $return_stripe_subscriptions->status == 'canceled'
                            ) {
                                $returnData['type'] = 'success';
                                $returnData['msg'] = __('Subscription canceled successfully!', AEMI_TEXT_DOMAIN);
                            }
                        }
                    }
                }
            }

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($returnData);
            exit;
        }

        public static function get_my_account_subscription_payments_url()
        {
            return wc_get_account_endpoint_url(self::$simpleMembershipWooSlug);
        }
    }

    new AEMICustomizeSimpleMembership__CustomPageInWoo();
}
