<?php
/**
 * Login Form.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 *
 * @version 4.1.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if( isset( $_GET['sw'] ) && $_GET['sw'] == 'pe'){
  //adicionando compatibilidade com o plugin class wp-membership
  wc_add_notice( 'El correo electrónico o la contraseña proporcionados son incorrectos. Por favor revisa e intenta de nuevo.', 'error' );
}

do_action('woocommerce_before_customer_login_form'); ?>

<?php // if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) :?>

<div class="aemi-general-container-login-page" id="customer_login">

    <div class="aemi-column-login">

        <div class="aemi-login-logo">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-login.png" alt="<?php echo get_bloginfo('name'); ?>" title="<?php echo get_bloginfo('name'); ?>" />
        </div>

        
        <?php
            if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'eu-submit.jotform.com') !== false ) {
                ?>
                <div style=" background: #5ca65f; color: #fff; text-align: center; padding: 9px; border-radius: 10px; margin-bottom: 20px;">
          <strong>¡Gracias por registrarte!</strong><br> La inscripción requiere una validación, por lo que podrás acceder al espacio profesional en un plazo de 24 horas.
                </div>
                <?php
            }
?>


<?php // endif;?>

        <h2 class="default-title m-auto"><?php esc_html_e('Login', 'woocommerce'); ?></h2>

        <form class="woocommerce-form woocommerce-form-login login aemi-form-login" method="post">

            <?php do_action('woocommerce_login_form_start'); ?>

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide aemi-form-group">
				Escribe tu correo electrónico&nbsp;*
                <label for="username" class="aemi-form-group-label"><span class="required"></span></label>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text aemi-form-input" name="username" id="username" autocomplete="username" value="<?php echo (!empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>" />
                <span class="aemi-form-group-bar"></span>
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide aemi-form-group">
				Escribe tu contraseña&nbsp;*
                <label for="password" class="aemi-form-group-label"><span class="required"></span></label>
                <input class="woocommerce-Input woocommerce-Input--text input-text aemi-form-input" type="password" name="password" id="password" autocomplete="current-password" />
                <span class="aemi-form-group-bar"></span>
            </p>

            <?php do_action('woocommerce_login_form'); ?>

            <p class="form-row">

                <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme aemi-form-group-label-checkbox ">
                    <input class="woocommerce-form__input woocommerce-form__input-checkbox aemi-form-group-label-checkbox__input" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <label for="rememberme" class="aemi-form-group-label-checkbox__label"><?php esc_html_e('Remember me', 'woocommerce'); ?></label>
                </label>
                
                <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>

                <button type="submit" class="btn btn-secondary w-100" name="login" value="<?php esc_attr_e('Log in', 'woocommerce'); ?>"><?php esc_html_e('Log in', 'woocommerce'); ?></button>
            </p>
            <p class="woocommerce-LostPassword lost_password">
                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php esc_html_e('Lost your password?', 'woocommerce'); ?></a>
            </p>

            <?php do_action('woocommerce_login_form_end'); ?>

            <div class="aemi-mobile-others">
                <div class="aemi-separator mb-5">
                    <span class="aemi-separator__text"><?php esc_html_e('Or', AEMI_TEXT_DOMAIN); ?></span>
                </div>

                <a href="<?php echo get_aemi_register_page_url(); ?>" rel="noreferrer noopener" class="btn btn-primary d-table m-auto"><?php echo __('Go to registration', AEMI_TEXT_DOMAIN); ?></a>
                <?php /* <button type="button" class="btn btn-primary login-goto-to-register d-table m-auto"><?php echo __('Go to registration', AEMI_TEXT_DOMAIN); ?></button> */ ?>
            </div>

        </form>

<?php // if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) :?>

    </div>

    <div class="aemi-column-register">

        <div class="aemi-login-logo">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-login.png" alt="<?php echo get_bloginfo('name'); ?>" title="<?php echo get_bloginfo('name'); ?>" />
        </div>

        <?php /*
        <h2 class="default-title m-auto mb-4"><?php esc_html_e( 'Register', 'woocommerce' ); ?></h2>

        <p><?php esc_html_e( 'Complete the following form to create an account:', AEMI_TEXT_DOMAIN ); ?></p>

        <form method="post" class="woocommerce-form woocommerce-form-register register aemi-form-register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

            <?php do_action( 'woocommerce_register_form_start' ); ?>

            <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide aemi-form-group">
                    <label for="reg_username" class="aemi-form-group-label"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text aemi-form-input" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
                    <span class="aemi-form-group-bar"></span>
                </p>

            <?php endif; ?>

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide aemi-form-group">
                <label for="reg_email" class="aemi-form-group-label"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                <input type="email" class="woocommerce-Input woocommerce-Input--text input-text aemi-form-input" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
                    <span class="aemi-form-group-bar"></span>
            </p>

            <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide aemi-form-group">
                    <label for="reg_password" class="aemi-form-group-label"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                    <input type="password" class="woocommerce-Input woocommerce-Input--text input-text aemi-form-input" name="password" id="reg_password" autocomplete="new-password" />
                    <span class="aemi-form-group-bar"></span>
                </p>

            <?php else : ?>

                <p><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'woocommerce' ); ?></p>

            <?php endif; ?>

            <?php do_action( 'woocommerce_register_form' ); ?>

            <p class="woocommerce-form-row form-row">
                <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
                <button type="submit" class="btn btn-secondary w-100" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
            </p>

            <?php do_action( 'woocommerce_register_form_end' ); ?>

        </form>

        <div class="aemi-mobile-others">
            <div class="aemi-separator mb-5">
                <span class="aemi-separator__text"><?php esc_html_e( 'Or', AEMI_TEXT_DOMAIN ); ?></span>
            </div>

            <button type="button" class="btn btn-primary login-goto-to-login d-table m-auto"><?php echo __('Go to login', AEMI_TEXT_DOMAIN); ?></button>
        </div>
        */ ?>

    </div>

  <div class="login-container-lateral-animation">
    
    <div class="login-container-lateral-animation-inner">
      <div class="login-abstract-icon"></div>
      <div class="login-abstract-icon-two"></div>
      <div class="login-abstract-icon-three"></div>

      <div class="login-container-lateral-animation-text">
        <div class="login-container-lateral-animation-text-login">
          <h2><?php echo __('Still not registered?', AEMI_TEXT_DOMAIN); ?></h2>
          <p>
            <?php echo __('Registration for this space is completely free and without obligation.<br />It will allow you to access various content and services in just a few clicks!', AEMI_TEXT_DOMAIN); ?>
          </p>
          <?php /* <button type="button" class="btn btn-secondary login-goto-to-register"><?php echo __('Register', AEMI_TEXT_DOMAIN); ?></button> */ ?>
          <a href="<?php echo get_aemi_register_page_url(); ?>" rel="noreferrer noopener" class="btn btn-secondary"><?php echo __('Register', AEMI_TEXT_DOMAIN); ?></a>
        </div>
        <div class="login-container-lateral-animation-text-register">
            <?php /*
            <h2><?php echo __('Do you have an account?', AEMI_TEXT_DOMAIN); ?></h2>
              <p>
                <?php echo __("Log in to access the contenido collection", AEMI_TEXT_DOMAIN); ?>
              </p>
              <button type="button" class="btn btn-secondary login-goto-to-login"><?php echo __('Go to login screen', AEMI_TEXT_DOMAIN); ?></button>
        </div>
        */ ?>
      </div>
    </div>
    
    <div class="login-abstract-icon-four"></div>

  </div>
</div>
<?php // endif;?>

<?php do_action('woocommerce_after_customer_login_form'); ?>
