<?php
/**
 * Lost password reset form.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-reset-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.5
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_reset_password_form' );
?>

<div class="aemi-general-container-login-page" id="customer_login">

	<div class="aemi-column-login">

		<div class="aemi-login-logo">
			<img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-login.png" alt="<?php echo get_bloginfo('name'); ?>" title="<?php echo get_bloginfo('name'); ?>" />
		</div>

		<form method="post" class="woocommerce-ResetPassword lost_reset_password">

			<p><?php echo apply_filters( 'woocommerce_reset_password_message', esc_html__( 'Enter a new password below.', 'woocommerce' ) ); ?></p><?php // @codingStandardsIgnoreLine ?>

			
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide aemi-form-group">
				<label for="password_1" class="aemi-form-group-label"><?php esc_html_e( 'New password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="password" class="woocommerce-Input woocommerce-Input--text input-text aemi-form-input" name="password_1" id="password_1" autocomplete="new-password" />
				<span class="aemi-form-group-bar"></span>
			</p>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide aemi-form-group">
				<label for="password_2" class="aemi-form-group-label"><?php esc_html_e( 'Re-enter new password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="password" class="woocommerce-Input woocommerce-Input--text input-text aemi-form-input" name="password_2" id="password_2" autocomplete="new-password" />
				<span class="aemi-form-group-bar"></span>
			</p>

			<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>" />
			<input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>" />

			<div class="clear"></div>

			<?php do_action( 'woocommerce_resetpassword_form' ); ?>

			<p class="woocommerce-form-row form-row">
				<input type="hidden" name="wc_reset_password" value="true" />
				<button type="submit" class="woocommerce-Button button" value="<?php esc_attr_e( 'Save', 'woocommerce' ); ?>"><?php esc_html_e( 'Save', 'woocommerce' ); ?></button>
			</p>

			<?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>

		</form>
	
	</div>

	<div class="login-container-lateral-animation">

		<div class="login-container-lateral-animation-inner">
			<div class="login-abstract-icon"></div>
			<div class="login-abstract-icon-two"></div>
			<div class="login-abstract-icon-three"></div>
		</div>

		<div class="login-abstract-icon-four"></div>

	</div>
</div>


<?php
do_action( 'woocommerce_after_reset_password_form' );

