<?php
/**
 * My Account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 *
 * @version 3.5.0
 */
defined('ABSPATH') || exit; ?>

<?php
$__aemi__imported_especialidad = get_user_meta(get_current_user_id(), '__aemi__imported_especialidad', true);
$__aemi__imported_profesion = get_user_meta(get_current_user_id(), '__aemi__imported_profesion', true);
$aemi_imported__profession = ($__aemi__imported_profesion ? $__aemi__imported_profesion : (is_super_admin(get_current_user_id()) ? 'Adm' : $__aemi__imported_especialidad));

$aemi_current_user = wp_get_current_user();
$user_data = (array) AEMICustomizeSimpleMembership__CustomPageInWoo::get_current_membership_plan_info();
$member_current_expiry_date = false;
$noexpire = false;

$expiry_timestamp = $user_data['expiry_timestamp'];

if ($expiry_timestamp == PHP_INT_MAX) {
    $member_current_expiry_date = __('No expiry', AEMI_TEXT_DOMAIN);
    $noexpire = true;
} else {
    $expiry_date = date('Y-m-d', $expiry_timestamp);
    $member_current_expiry_date = date('d/m/Y', $expiry_timestamp);
}

// $billing_first_name = get_user_meta(get_current_user_id(), 'billing_first_name', true);
// $billing_last_name = get_user_meta(get_current_user_id(), 'billing_last_name', true);
// $billing_company = get_user_meta(get_current_user_id(), 'billing_company', true);
$billing_address_1 = get_user_meta(get_current_user_id(), 'billing_address_1', true);
$billing_address_2 = get_user_meta(get_current_user_id(), 'billing_address_2', true);
$billing_city = get_user_meta(get_current_user_id(), 'billing_city', true);
$billing_postcode = get_user_meta(get_current_user_id(), 'billing_postcode', true);
$billing_country = get_user_meta(get_current_user_id(), 'billing_country', true);
$billing_state = get_user_meta(get_current_user_id(), 'billing_state', true);
// $billing_phone = get_user_meta(get_current_user_id(), 'billing_phone', true);
// $billing_email = get_user_meta(get_current_user_id(), 'billing_email', true);
// $shipping_first_name = get_user_meta(get_current_user_id(), 'shipping_first_name', true);
// $shipping_last_name = get_user_meta(get_current_user_id(), 'shipping_last_name', true);
// $shipping_company = get_user_meta(get_current_user_id(), 'shipping_company', true);
$shipping_address_1 = get_user_meta(get_current_user_id(), 'shipping_address_1', true);
$shipping_address_2 = get_user_meta(get_current_user_id(), 'shipping_address_2', true);
$shipping_city = get_user_meta(get_current_user_id(), 'shipping_city', true);
$shipping_postcode = get_user_meta(get_current_user_id(), 'shipping_postcode', true);
$shipping_country = get_user_meta(get_current_user_id(), 'shipping_country', true);
$shipping_state = get_user_meta(get_current_user_id(), 'shipping_state', true);
// $shipping_phone = get_user_meta(get_current_user_id(), 'shipping_phone', true);

if (isset($_GET['welcome-my-account']) && $_GET['welcome-my-account'] == true) {
    update_user_meta(get_current_user_id(), '__aemi_viewed_welcome_page_my_account', 'yes');
} elseif (isset($_GET['reset-welcome-my-account']) && $_GET['reset-welcome-my-account'] == true) {
    update_user_meta(get_current_user_id(), '__aemi_viewed_welcome_page_my_account', 'no');
}

$aemi_viewed_welcome_page_my_account = get_user_meta(get_current_user_id(), '__aemi_viewed_welcome_page_my_account', true);
?>
<div class="aemi-new-layout-my-account-container">
<main>
        <section class="sidebar fullWidth">
            <div class="mainHero fullWidth flex alignStart">
                <!-- Sidebar: Start -->
                <div class="leftSideBar" id="lefSid">
                    <div class="detailProfile">
                        <div class="sideBarClose">
                            <img id="closeIconL" src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/close.png'); ?>" alt="close" />
                        </div>
                        <div class="profile flex fullWidth gap20">
                            <div class="profileImg edit-profile-photo">
                                <?php do_action('woocommerce_edit_account_form_start'); ?> 
                            </div>

                            <div class="profileContent">
                                <h5><?php echo sanitize_text_field(get_aemi_current_user_name()); ?></h5>
                                <ul class="flex alignStart">
                                    <?php if ($aemi_imported__profession) { ?><li class="professionList flex">
                                        <span>Profesión:</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 24 24" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M12 11.75C12.1989 11.75 12.3897 11.829 12.5303 11.9697C12.671 12.1103 12.75 12.3011 12.75 12.5V13.25H13.5C13.6989 13.25 13.8897 13.329 14.0303 13.4697C14.171 13.6103 14.25 13.8011 14.25 14C14.25 14.1989 14.171 14.3897 14.0303 14.5303C13.8897 14.671 13.6989 14.75 13.5 14.75H12.75V15.5C12.75 15.6989 12.671 15.8897 12.5303 16.0303C12.3897 16.171 12.1989 16.25 12 16.25C11.8011 16.25 11.6103 16.171 11.4697 16.0303C11.329 15.8897 11.25 15.6989 11.25 15.5V14.75H10.5C10.3011 14.75 10.1103 14.671 9.96967 14.5303C9.82902 14.3897 9.75 14.1989 9.75 14C9.75 13.8011 9.82902 13.6103 9.96967 13.4697C10.1103 13.329 10.3011 13.25 10.5 13.25H11.25V12.5C11.25 12.3011 11.329 12.1103 11.4697 11.9697C11.6103 11.829 11.8011 11.75 12 11.75Z"
                                                fill="black" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M11.948 1.25C11.049 1.25 10.3 1.25 9.706 1.33C9.078 1.414 8.511 1.6 8.056 2.055C7.6 2.511 7.414 3.078 7.33 3.705C7.25 4.3 7.25 5.05 7.25 5.948V6.026C5.229 6.092 4.015 6.328 3.172 7.172C2 8.343 2 10.229 2 14C2 17.771 2 19.657 3.172 20.828C4.343 22 6.229 22 10 22H14C17.771 22 19.657 22 20.828 20.828C22 19.657 22 17.771 22 14C22 10.229 22 8.343 20.828 7.172C19.985 6.328 18.771 6.092 16.75 6.026V5.948C16.75 5.05 16.75 4.3 16.67 3.706C16.586 3.078 16.4 2.511 15.944 2.056C15.489 1.6 14.922 1.414 14.294 1.33C13.7 1.25 12.95 1.25 12.052 1.25H11.948ZM15.25 6.002V6C15.25 5.036 15.248 4.388 15.184 3.905C15.121 3.444 15.014 3.246 14.884 3.116C14.754 2.986 14.556 2.879 14.094 2.816C13.612 2.752 12.964 2.75 12 2.75C11.036 2.75 10.388 2.752 9.905 2.817C9.444 2.879 9.246 2.986 9.116 3.117C8.986 3.247 8.879 3.444 8.816 3.905C8.753 4.388 8.75 5.036 8.75 6V6.002C9.142 6 9.558 6 10 6H14C14.442 6 14.858 6 15.25 6.002ZM16 14C16 15.0609 15.5786 16.0783 14.8284 16.8284C14.0783 17.5786 13.0609 18 12 18C10.9391 18 9.92172 17.5786 9.17157 16.8284C8.42143 16.0783 8 15.0609 8 14C8 12.9391 8.42143 11.9217 9.17157 11.1716C9.92172 10.4214 10.9391 10 12 10C13.0609 10 14.0783 10.4214 14.8284 11.1716C15.5786 11.9217 16 12.9391 16 14Z"
                                                fill="black" />
                                        </svg>
                                        <?php echo sanitize_text_field($aemi_imported__profession); ?>
                                    </li>
                                    <?php } else { ?>
                                    <li class="professionList flex"></li>
                                    <?php } ?>
                                    
                                    <li class="flex">
                                        <span>Nivel:</span>
                                        <span class="introductory flex">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                                viewBox="0 0 22 22" fill="none">
                                                <path
                                                    d="M16.9426 15.3691C15.6897 15.6699 14.3817 15.6553 13.1358 15.3266C11.8899 14.9979 10.745 14.3654 9.8035 13.4856C8.86204 12.6058 8.15348 11.5063 7.74124 10.2855C7.32901 9.06465 7.22599 7.76065 7.44139 6.49023C7.40863 6.52187 7.37404 6.55156 7.33781 6.57915C7.08114 6.7744 6.76031 6.84682 6.11864 6.99165L5.53656 7.12365C3.28156 7.63423 2.15406 7.88907 1.88547 8.75165C1.61781 9.61332 2.38597 10.5126 3.92322 12.3102L4.32106 12.7749C4.75739 13.2855 4.97647 13.5412 5.07456 13.8566C5.17264 14.1728 5.13964 14.5138 5.07364 15.1949L5.01314 15.8155C4.78122 18.2144 4.66481 19.4134 5.36697 19.946C6.06914 20.4795 7.12514 19.9937 9.23531 19.0211L9.78256 18.7699C10.3821 18.4931 10.6818 18.3556 10.9999 18.3556C11.318 18.3556 11.6177 18.4931 12.2181 18.7699L12.7636 19.0211C14.8746 19.9927 15.9306 20.4786 16.6319 19.9469C17.335 19.4134 17.2186 18.2144 16.9866 15.8155L16.9426 15.3691Z"
                                                    fill="white" />
                                                <path opacity="0.5"
                                                    d="M8.39022 4.95701L8.08955 5.49601C7.75955 6.08817 7.59455 6.38426 7.33789 6.57951C7.37455 6.55201 7.40847 6.52267 7.44147 6.49059C7.22601 7.7611 7.32903 9.06521 7.74131 10.2861C8.15358 11.507 8.86224 12.6066 9.80381 13.4864C10.7454 14.3663 11.8905 14.9988 13.1365 15.3274C14.3826 15.656 15.6906 15.6705 16.9436 15.3694L16.9253 15.1953C16.8602 14.5142 16.8272 14.1732 16.9253 13.8569C17.0234 13.5416 17.2416 13.2858 17.6788 12.7753L18.0766 12.3105C19.6139 10.5138 20.3821 9.61459 20.1135 8.75201C19.8458 7.88942 18.7183 7.63367 16.4633 7.12401L15.8803 6.99201C15.2396 6.84717 14.9187 6.77476 14.6611 6.57951C14.4045 6.38426 14.2395 6.08817 13.9095 5.49601L13.6097 4.95701C12.4483 2.87434 11.8681 1.83301 11 1.83301C10.1319 1.83301 9.55164 2.87434 8.39022 4.95701Z"
                                                    fill="white" />
                                            </svg>
                                            <?php echo get_aemi_current_user_score_current_level()->name; ?>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card scoreCard" style="overflow: unset;">
                            <p class="flex justifyBetween">
                            Mi puntuación
<!--                                <span class="tooltip">-->
<!--                                    <img src="--><?php // echo get_aemi_url_assets_imgs('new-layout-v1/icon/not-find.png');?><!--" alt="message" />-->
<!--                                    Información-->
<!--                                    <span class="tooltiptext">Las puntuaciones se calculan en función de los puntos que recibió.</span>-->
<!--                                </span>-->
                            </p>
                            <label><progress max="100" value="<?php echo get_aemi_current_user_percentage_level_to_level_up(); ?>"><?php echo get_aemi_current_user_percentage_level_to_level_up(); ?>"><?php echo get_aemi_current_user_percentage_level_to_level_up(); ?>%</progress></label>
<!--                            <p class="flex">--><?php // echo get_aemi_current_user_score();?><!--  Points<span>(--><?php // echo get_aemi_current_user_percentage_level_to_level_up();?><!--%)</span></p>-->
                            <p class="flex"><?php echo get_aemi_current_user_score(); ?>  Puntos<span>(<?php echo get_aemi_current_user_percentage_level_to_level_up(); ?>%)</span></p>
                        </div>

                        <div class="subscriptionCard card" style="overflow: unset;">
                            <p class="flex justifyBetween">
                                Mis suscripciones
<!--                                <span class="tooltip">-->
<!--                                    <img src="--><?php // echo get_aemi_url_assets_imgs('new-layout-v1/icon/not-find.png');?><!--" alt="message" />-->
<!--                                    Información-->
<!--                                    <span class="tooltiptext">Las puntuaciones se calculan en función de los puntos que recibió.</span>-->
<!--                                </span>-->
                            </p>

                            <p class="flex justifyBetween">Plan actual<span><?php echo current_user_can('administrator') ? 'Pro (Adm)' : $user_data['membership_level_alias']; ?></span></p>
                            <p class="flex justifyBetween">Válido hasta<span><?php echo ucfirst(get_replace_month_by_current_lang(esc_attr($member_current_expiry_date))); ?></span></p>
                            <?php if (aemi_current_user_is_professional()) { ?>
                            <?php
                                if (
                                    !(
                                        isset($user_data['transaction']->meta['status_subscription_on_gateway_canceled_or_refund'][0])
                                        && in_array($user_data['transaction']->meta['status_subscription_on_gateway_canceled_or_refund'][0], ['customer.subscription.deleted', 'charge.refunded'])
                                    )
                                ) {
                                    ?>
                                <button class="cancel-title" id="activeTittle" style="background: none;border: none;border-bottom: 1px dashed var(--color-danger-light);">Cancelar suscripción activa</button>
                            <?php } ?>
                        <?php } else { ?>
                            <div>
                                <p><a href="<?php echo get_page_url_subscription_buy(); ?>" class="view-title">Ver plano</a></p>
                            </div>
                        <?php } ?>
                        </div>
                    </div>
                    <div class="profileTabs">
                        <ul class="flex justifyBetween">
                            <li class="TabButton TabButtonSelected" id="Tab1" onclick="activeTab(event, 'Tab1')"><a
                                    href="#">Información personal</a></li>
                            <li class="TabButton " id="Tab2" onclick="activeTab(event, 'Tab2')"><a href="#">Direcciones</a></li>
                        </ul>
                    </div>
                    <div class="detailTab tab-section d-chart-n d-chart-show" id="SectionTab1">
                        <ul class="flex">
                            <li class="fullWidth flex justifyBetween alignStart">
                                <div>
                                    <h4>Nombre completo</h4>
                                    <h5 id="replace-user-name"><?php echo sanitize_text_field(get_aemi_current_user_name()); ?></h5>
                                </div>
                                <button id="updateInfo" class="flex justifyCenter"><span
                                        class="flex justifyCenter"><img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/pensil.png'); ?>"
                                            alt="update-info" />Actualizar</span></button>
                            </li>
                            <li class="fullWidth flex justifyBetween alignStart">
                                <div>
                                    <h4>Correo electrónico</h4>
                                    <h5 id="replace-user-email"><?php echo sanitize_text_field($current_user->user_email); ?></h5>
                                </div>
                            </li>
                            <li class="fullWidth flex justifyBetween alignStart">
                                <div>
                                    <h4>Contraseña</h4>
                                    <h5>**********</h5>
                                </div>
                                <button id="updatePwd" class="flex justifyCenter"><span
                                        class="flex justifyCenter"><img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/pensil.png'); ?>"
                                            alt="update-info" />Actualizar</span></button>
                            </li>
                        </ul>
                    </div>
                    <div class="detailTab tab-section d-chart-n" id="SectionTab2" style="display: none;">
                        <ul class="flex">
                            <li class="fullWidth flex justifyBetween alignStart">
                                <div class="addressDetail flex alignStart">
                                    <h5>Dirección de facturación</h5>
                                    <h4><?php echo sanitize_text_field($billing_address_1); ?></h4>
                                    <?php echo $billing_address_2 ? '<h4>'.sanitize_text_field($billing_address_2).'</h4>' : ''; ?>
                                    <h4><?php echo sanitize_text_field($billing_state).($billing_postcode ? ',' : ''); ?> <?php echo sanitize_text_field($billing_postcode); ?></h4>
                                    <h4><?php echo sanitize_text_field($billing_country); ?></h4>
                                </div>
                                <button id="billAdd" class="flex justifyCenter"><span
                                        class="flex justifyCenter"><img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/pensil.png'); ?>"
                                            alt="update-info" />Editar</span></button>
                            </li>
                            <li class="fullWidth flex justifyBetween alignStart">
                                <div class="addressDetail flex alignStart">
                                    <h5>Dirección de envío</h5>
                                    <h4><?php echo sanitize_text_field($shipping_address_1); ?></h4>
                                    <?php echo $shipping_address_2 ? '<h4>'.sanitize_text_field($shipping_address_2).'</h4>' : ''; ?>
                                    <h4><?php echo sanitize_text_field($shipping_state).($shipping_postcode ? ',' : ''); ?> <?php echo sanitize_text_field($shipping_postcode); ?></h4>
                                    <h4><?php echo sanitize_text_field($shipping_country); ?></h4>
                                </div>
                                <button id="shippAdd" class="flex justifyCenter"><span
                                        class="flex justifyCenter"><img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/pensil.png'); ?>"
                                            alt="update-info" />Editar</span></button>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Sidebar: End -->

                <!-- Content: Start -->
                <div class="rightbar fullWidth">
                    <div class="tabContainer">
                        <div class="Mymenu-bar">
                            <svg id="myIns" stroke="currentColor" fill="currentColor" stroke-width="0"
                                viewBox="0 0 1024 1024" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M904 160H120c-4.4 0-8 3.6-8 8v64c0 4.4 3.6 8 8 8h784c4.4 0 8-3.6 8-8v-64c0-4.4-3.6-8-8-8zm0 624H120c-4.4 0-8 3.6-8 8v64c0 4.4 3.6 8 8 8h784c4.4 0 8-3.6 8-8v-64c0-4.4-3.6-8-8-8zm0-312H120c-4.4 0-8 3.6-8 8v64c0 4.4 3.6 8 8 8h784c4.4 0 8-3.6 8-8v-64c0-4.4-3.6-8-8-8z">
                                </path>
                            </svg>
                        </div>
                        <nav>
                            <ul class="menu">
                                <?php do_action('woocommerce_account_navigation'); ?>
                            </ul>
                        </nav>
                    </div>

                    <?php if (!$aemi_viewed_welcome_page_my_account || $aemi_viewed_welcome_page_my_account && $aemi_viewed_welcome_page_my_account != 'yes') { ?>
                        <?php require_once 'welcome-message.php'; ?>
                    <?php } ?>
                            
                    <?php do_action('woocommerce_account_content'); ?>
                   

                </div>
                <!-- Content: End -->
            </div>
        </section>
    </main>

    <!-- billing -->
    <div class="scoreLevel1" id="billingAdd" style="display:none">
            <div class="overlay"></div>
            <div class="mainScoreU">
                <div class="updInfo">
                    <p class="firParaU">Actualizar dirección de facturación</p>
                    <img id="closeIconS" src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/close.png'); ?>" alt="close" />
                </div>
                <div class="SpecifyIns" id="spcfrm">
                    <div class="custom-aemi-notices"></div>
                    <div class="aemi-customize-form-to-new-layout">
                        <?php do_action('woocommerce_before_edit_account_address_form');
woocommerce_account_edit_address('billing'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- shipping -->
        <div class="scoreLevel1" id="shippingAdd" style="display:none">
            <div class="overlay"></div>
            <div class="mainScoreU">
                <div class="updInfo">
                    <p class="firParaU">Actualizar dirección de envío</p>
                    <img id="closeIconB" src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/close.png'); ?>" alt="close" />
                </div>
                <div class="SpecifyIns" id="spcfrm">
                    <div class="custom-aemi-notices"></div>
                    <div class="aemi-customize-form-to-new-layout">
                        <?php do_action('woocommerce_before_edit_account_address_form');
woocommerce_account_edit_address('shipping'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!--info-->
        <div class="scoreLevel1" id="updateInfoModal" style="display: none;">
            <div class="overlay"></div>
            <div class="mainScoreU">
                <div class="updInfo">
                    <p class="firParaU">Actualizar información personal</p>
                    <img id="closeIconI" src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/close.png'); ?>" alt="close" />
                </div>
                <div class="SpecifyIns" id="spcfrm">
                    <form id="update-my-account-data-form" method="POST">
                        <div class="input-row">
                            <div class="daiKir">
<!--                                <label for="fname">Ingresa tu primer nombre <span>*</span></label>-->
                                <label for="fname">Pon tu nombre <span>*</span></label>
                                <input type="text" id="fname" name="fname" placeholder="<?php echo esc_attr(sanitize_text_field($current_user->first_name)); ?>" value="<?php echo esc_attr(sanitize_text_field($current_user->first_name)); ?>" />
                            </div>
                            <div class="daiKir">
<!--                                <label for="lname">Ingrese su apellido <span>*</span></label>-->
                                <label for="lname">Pon tus apellidos <span>*</span></label>
                                <input type="text" id="lname" name="lname"  placeholder="<?php echo esc_attr(sanitize_text_field($current_user->last_name)); ?>" value="<?php echo esc_attr(sanitize_text_field($current_user->last_name)); ?>" />
                            </div>
                        </div>
                        <div class="input-rowSec">
<!--                            <label for="email">Ingrese su dirección de correo electrónico <span>*</span></label>-->
                            <label for="email">Ingresa tu dirección de correo electrónico <span>*</span></label>
                            <input type="text" id="email" name="email" placeholder="" value="<?php echo sanitize_text_field($current_user->user_email); ?>" />
                            <div id="update-my-account-data__nonce"><?php echo do_shortcode('[render_nonce_field_update_data_reset_form]'); ?></div>
                        </div>
                        <div class="modelBtn">
                            <button class="primary-btn">Actualizar información</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!--pwd-->
        <div class="scoreLevel1" id="updateInfoPwd" style="display: none;">
            <div class="overlay"></div>
            <div class="mainScoreU">
                <div class="updInfo">
                    <p class="firParaU">Actualiza contraseña</p>
                    <img id="closeIconP" src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/close.png'); ?>" alt="close">
                </div>
                <div class="SpecifyIns" id="spcfrm">
                    <form id="password-reset-form" method="POST">
                        <div class="input-rowSec">
                            <label for="password">Introduzca su nueva contraseña <span>*</span></label>
                            <input type="password" id="password" name="new-password">
                        </div>
                        <div class="input-rowSec">
                            <label for="repeat">Repita su nueva contraseña <span>*</span></label>
                            <input type="password" id="password" name="confirm-password">
                            <div id="password-reset__nonce"><?php echo do_shortcode('[render_nonce_field_password_reset_form]'); ?></div>
                        </div>
                        <div class="modelBtn">
                            <button class="primary-btn">Actualiza contraseña</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        

    <?php if (aemi_current_user_is_professional()) { ?>
        <?php
        if (
            !(
                isset($user_data['transaction']->meta['status_subscription_on_gateway_canceled_or_refund'][0])
                && in_array($user_data['transaction']->meta['status_subscription_on_gateway_canceled_or_refund'][0], ['customer.subscription.deleted', 'charge.refunded'])
            )
        ) {
            ?>
                <!-- cancel active -->
                <div class="scoreLevel1" id="cancelPopup" style="display: none">
                    <div class="overlay"></div>
                    <div class="cancel-Subscription-popup">
                        
                        <div class="cancel-top-bar">
                        <span>
                            <img class="cancel-img" src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/cancel.png'); ?>"/>
                            <h2 style="color: red">Cancelar suscripción a Socio de AEMI</h2>
                        </span>
                        <img class="cross-img" src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/cross.png'); ?>" id="crossIcon"/>
                        </div>
                        <div class="popup-content">
                        <p>¿Estás seguro que quieres dejar de ser socio de AEMI? Perderás todas las ventajas de socio al finalizar el periodo de la cuota ya abonada.</p>
                        </div>
                        <div class="popup-buttons">
                            <button class="warning-btn" id="cancel-subscription-my-account">Sí, cancelar mi suscripción</button>
                            <button class="primary-btn" id="cancel-subscription-my-account-close-modal">No, quiero conservarlo</button>
                        </div>
                    </div>
                </div>

                <script>
                    jQuery('#cancel-subscription-my-account-close-modal').click(function(){
                        jQuery('#cancelPopup').removeClass('showModal');
                    });
                    
                    jQuery('#cancel-subscription-my-account').click(function(){
                        if( !jQuery(this).hasClass('cancel-subscription-my-account_disable') ){
                            var currentItemCheck = jQuery(this);
                            currentItemCheck.addClass('cancel-subscription-my-account_disable');
                            jQuery(this).prop('disabled', 'disabled')

                            jQuery.ajax({ 
                                url : '<?php echo get_admin_url(); ?>admin-ajax.php', 
                                type : 'POST',
                                dataType: "json",
                                data : {
                                    action : 'aemi_cancel_subscription_membership',
                                    aemi_membership_inoice_pdf: '<?php echo wp_create_nonce('aemi_membership_inoice_pdf_action'); ?>'
                                },
                                success: function( data ){
                                    if( data['type'] == 'success' ){
                                        jQuery('body').append('<div class="get-points-gamification__notice" data-type="success">' + data['msg'] + '</div>');

                                        jQuery('#cancelPopup').remove();
                                    } else {
                                        jQuery('body').append('<div class="get-points-gamification__notice" data-type="error">' + data['msg'] + '</div>');
                                    }

                                        if( jQuery('.get-points-gamification__notice') ){
                                        setTimeout(() => {
                                            jQuery('.get-points-gamification__notice').addClass('started');
                                            
                                            setTimeout(() => {
                                                jQuery('.get-points-gamification__notice').addClass('remove');
                                                window.location.reload();

                                            }, 3000);
                                        }, 100);
                                        }

                                    jQuery('#cancelPopup').remove();

                                    setTimeout(() => {
                                        jQuery('#activeTittle').remove();
                                    }, 1000);
                                }
                            });
                        }
                    });
                </script>
        <?php } ?>
    <?php } ?>
    </div>


    <script>
    jQuery(document).ready(function($) {
        
        $('#billingAdd form, #shippingAdd form').submit(function(e) {
            e.preventDefault();

            let elementNotices = $(this).parent().parent().parent().find('.custom-aemi-notices');
            let elementBtn = $(this).find('.button');

            elementBtn.prop('disabled', true);
            elementNotices.html('');
            

            var formData = $(this).serialize();

            var formDataJSON = {};
            formData.split('&').forEach(function(keyValue) {
                var parts = keyValue.split('=');
                formDataJSON[decodeURIComponent(parts[0])] = decodeURIComponent(parts[1]);
            });

            formDataJSON.action = 'aemi_my_account_update_edit_billing_data';
            formDataJSON.<?php echo AEMIcustomUpdatesInfoMyAccountPage::nonce_shipping_billing_name(); ?> = '<?php echo AEMIcustomUpdatesInfoMyAccountPage::nonce_shipping_billing_action(); ?>';
            
            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: formDataJSON,
                success: function(response) {
                    $(elementNotices).html(response.message);

                    if( response.success ){
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        elementBtn.removeProp('disabled');
                        elementBtn.removeAttr('disabled');
                    }
                }
            });
        });

        $('#update-my-account-data-form').submit(function(e) {
            e.preventDefault();

            $('#update-my-account-data-form .modelBtn button').prop('disabled', true);

            var formData = $(this).serialize();

            var formDataJSON = {};
            formData.split('&').forEach(function(keyValue) {
                var parts = keyValue.split('=');
                formDataJSON[decodeURIComponent(parts[0])] = decodeURIComponent(parts[1]);
            });

            formDataJSON.action = 'aemi_my_account_update_data';

            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: formDataJSON,
                success: function(response) {
                    alert(response.message);

                    if( response.success ){
                        $('#replace-user-name').text( $('#update-my-account-data-form input[name="fname"]').val() + ' ' + $('#update-my-account-data-form input[name="lname"]').val() );
                        $('#replace-user-email').text( $('#update-my-account-data-form input[name="email"]').val() );
                    }

                    $('#update-my-account-data-form .modelBtn button').removeProp('disabled');
                    $('#update-my-account-data-form .modelBtn button').removeAttr('disabled');

                    jQuery('#closeIconI').click();
                    
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        data: {action: 'aemi_my_account_update_data_nonce'},
                        success: function(response) {
                            $('#update-my-account-data__nonce').html(response.data.t);
                        }
                    });

                }
            });
        });
    });
    </script>

    <script>
    jQuery(document).ready(function($) {
        $('#password-reset-form').submit(function(e) {
            e.preventDefault();

            var newPassword = $('#password-reset-form input[name="new-password"]').val();
            var confirmPassword = $('#password-reset-form input[name="confirm-password"]').val();

            if (newPassword !== confirmPassword) {
                alert('As senhas não coincidem. Por favor, tente novamente.');
            } else {

                $('#password-reset-form .modelBtn button').prop('disabled', true);

                var formData = $(this).serialize();

                var formDataJSON = {};
                formData.split('&').forEach(function(keyValue) {
                    var parts = keyValue.split('=');
                    formDataJSON[decodeURIComponent(parts[0])] = decodeURIComponent(parts[1]);
                });

                formDataJSON.action = 'aemi_my_account_reset_password';
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: formDataJSON,
                    success: function(response) {
                        alert(response.data.message);
                        jQuery('#closeIconP').click();
                        $('#password-reset-form input[name="new-password"]').val('');
                        $('#password-reset-form input[name="confirm-password"]').val('');

                        $('#password-reset-form .modelBtn button').removeProp('disabled');
                        $('#password-reset-form .modelBtn button').removeAttr('disabled');

                        $.ajax({
                            type: 'POST',
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            data: {action: 'aemi_my_account_reset_password_nonce'},
                            success: function(response) {
                                $('#password-reset__nonce').html(response.data.t);
                            }
                        });

                    }
                });
            }
        });
    });
    </script>
    
</div>