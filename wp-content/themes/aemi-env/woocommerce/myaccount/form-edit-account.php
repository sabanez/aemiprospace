<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' ); ?>

<form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

    <!-- Información de la cuenta -->
    <div class="fieldset-groups">
        <legend class="title-section-account mt-0"><?= esc_html_e('Información de la cuenta', AEMI_TEXT_DOMAIN) ?></legend>

        <fieldset class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
            <p>
                <label for="account_first_name" class="label-animation"><?php esc_html_e( 'Nombre', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" />
            </p> 
        </fieldset>

        <fieldset class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
            <p>
                <label for="account_last_name" class="label-animation"><?php esc_html_e( 'Apellido', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" />
            </p>
        </fieldset>

        <fieldset class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <p>
                <label for="account_email" class="label-animation"><?php esc_html_e( 'E-mail', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                <input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" />
            </p>
        </fieldset>

        <fieldset class="d-none woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <p>
                <label for="account_display_name" class="label-animation"><?php esc_html_e( 'Display name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" /> <span><em><?php esc_html_e( 'This will be how your name will be displayed in the account section and in reviews', 'woocommerce' ); ?></em></span>
            </p>
        </fieldset>

        <div class="clearfix"></div>
    </div>

    <!-- Datos del Domicilio -->
    <div class="fieldset-groups d-none">
        <legend class="title-section-account"><?= esc_html_e('Datos del Domicilio', AEMI_TEXT_DOMAIN) ?></legend>

        <fieldset class="form-row form-row-first address-field validate-required" id="billing_address_1_field" data-priority="50">
            <p>
                <label for="billing_address_1" class="label-animation"><?=esc_html_e('Dirección', 'woocommerce')?> <abbr class="required" title="obrigatório">*</abbr></label>
                <input type="text" class="input-text " name="billing_address_1" id="billing_address_1" autocomplete="address-line1">
            </p>
        </fieldset>

        <fieldset class="form-row form-row-last address-field" id="billing_address_2_field" data-priority="60">
            <p>
                <label for="billing_address_2" class="label-animation"><?=esc_html_e('Complementar', 'woocommerce')?> <span class="optional">(opcional)</span></label>
                <input type="text" class="input-text " name="billing_address_2" id="billing_address_2" autocomplete="address-line2">
            </p>
        </fieldset>

        <fieldset class="form-row form-row-first address-field validate-required validate-postcode" id="billing_postcode_field" data-priority="65">
            <p>
                <label for="billing_postcode" class="label-animation"><?=esc_html_e('Código postal', 'woocommerce')?> <abbr class="required" title="obrigatório">*</abbr></label>
                <input type="text" class="input-text " name="billing_postcode" id="billing_postcode" placeholder="" value="" autocomplete="postal-code">
            </p>
        </fieldset>

        <fieldset class="form-row form-row-last address-field validate-required" id="billing_city_field" data-priority="70">
            <p>
                <label for="billing_city" class="label-animation"><?=esc_html_e('Ciudad', 'woocommerce')?> <abbr class="required" title="obrigatório">*</abbr></label>
                <input type="text" class="input-text " name="billing_city" id="billing_city" autocomplete="address-level2">
            </p>
        </fieldset>

        <div class="clearfix"></div>
    </div>

    <!-- Cambiar contraseña -->
    <div class="fieldset-groups">
        <legend class="title-section-account"><?php esc_html_e( 'Cambiar contraseña', AEMI_TEXT_DOMAIN ); ?></legend>

        <div class="text-center mb-3">
            <span><?= esc_html_e('Complete los campos a continuación solo si realmente desea cambiar su contraseña actual.', 'woocommerce')?></span>
        </div>

        <fieldset class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <p>
                <label for="password_current" class="label-animation"><?php esc_html_e( 'Contraseña actual (dejar en blanco para no cambiar)', 'woocommerce' ); ?></label>
                <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" autocomplete="off" />
            </p>
        </fieldset>

        <fieldset class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
            <p>
                <label for="password_1" class="label-animation"><?php esc_html_e( 'Introduzca su nueva contraseña', 'woocommerce' ); ?></label>
                <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="off" />
            </p>
        </fieldset>

        <fieldset  class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
            <p>
                <label for="password_2" class="label-animation"><?php esc_html_e( 'Repite tu nueva contraseña', 'woocommerce' ); ?></label>
                <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="off" />
            </p>
        </fieldset>

        <div class="clearfix"></div>
    </div>

    <?php do_action( 'woocommerce_edit_account_form' ); ?>

    <p class="text-center mt-4">
        <?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
        <button type="submit" class="woocommerce-Button button" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Guardar', 'woocommerce' ); ?></button>
        <input type="hidden" name="action" value="save_account_details" />
    </p>

    <?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
