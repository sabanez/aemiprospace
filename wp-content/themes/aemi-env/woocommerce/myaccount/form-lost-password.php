<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-lost-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.2
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );
?>
<div class="aemi-general-container-login-page" id="customer_login">

	<div class="aemi-column-login">

		<div class="aemi-login-logo">
			<img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-login.png" alt="<?php echo get_bloginfo('name'); ?>" title="<?php echo get_bloginfo('name'); ?>" />
		</div>

		<form method="post" class="woocommerce-ResetPassword lost_reset_password">

			<p><?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' ) ); ?></p><?php // @codingStandardsIgnoreLine ?>

			
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide aemi-form-group">
				<label for="user_login" class="aemi-form-group-label"><?php esc_html_e( 'Username or email', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text aemi-form-input" name="user_login" id="user_login" autocomplete="off" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" />
				<span class="aemi-form-group-bar"></span>
			</p>

			<div class="clear"></div>

			<?php do_action( 'woocommerce_lostpassword_form' ); ?>

			<p class="woocommerce-form-row form-row">
				<input type="hidden" name="wc_reset_password" value="true" />
				<button type="submit" class="woocommerce-Button button" value="<?php esc_attr_e( 'Reset password', 'woocommerce' ); ?>"><?php esc_html_e( 'Reset password', 'woocommerce' ); ?></button>
			</p>

			<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

			<hr style="margin: 30px 0;" />

			<a href="<?php echo home_url(); ?>" title="<?php echo __("Volver a la página anterior", AEMI_TEXT_DOMAIN); ?>" style="color: #999;text-align: center;display: block;">
				<?php echo __("Volver a la página anterior", AEMI_TEXT_DOMAIN); ?>
			</a>
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
do_action( 'woocommerce_after_lost_password_form' );
