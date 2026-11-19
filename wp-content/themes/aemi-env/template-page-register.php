<?php
/**
* Template Name: Página de registro.
*/
?>
<?php get_header('clean'); ?>
<div class="login-page-general-wrapper aemi-registration-open-page" id="page-with-jotform">
	<div class="aemi-general-container-login-page" id="customer_login">
		<div class="aemi-column-login"></div>
		<div class="aemi-column-register">
			<?php /*
			<div class="aemi-login-logo">
				<img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-login.png" alt="<?php echo get_bloginfo('name'); ?>" title="<?php echo get_bloginfo('name'); ?>" />
			</div>

			<p>Completa el siguiente formulario para crear una cuenta:</p> */ ?>

			<div>
				<?php echo do_shortcode('[wppb-register]'); ?>
			</div>

			<div class="aemi-mobile-others">
				<div class="aemi-separator mb-5">
					<span class="aemi-separator__text"><?php esc_html_e('Or', AEMI_TEXT_DOMAIN); ?></span>
				</div>

				<a href="<?php echo get_aemi_my_account_url(); ?>" class="btn btn-primary login-goto-to-login d-table m-auto"><?php echo __('Go to login', AEMI_TEXT_DOMAIN); ?></a>
			</div>

		</div>

		<div class="login-container-lateral-animation" style="animation: unset;">
		    
		    <div class="login-container-lateral-animation-inner" style="animation: unset;">
		      <div class="login-abstract-icon"></div>
		      <div class="login-abstract-icon-two"></div>
		      <div class="login-abstract-icon-three"></div>

		      <div class="aemi-register-lateral-logo">
		        <img src="https://prospace.aemi.es/wp-content/uploads/2026/11/logoAEMI_CMJN_white_orange_CC.png" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" title="<?php echo esc_attr(get_bloginfo('name')); ?>" />
		        <p class="aemi-register-lateral-tagline">Espacio profesional</p>
		        <p class="aemi-register-lateral-login-link">¿Ya tienes una cuenta? <a href="<?php echo get_aemi_my_account_url(); ?>">Iniciar sesión</a></p>
		      </div>

		      <div class="login-container-lateral-animation-text">
		        <div class="login-container-lateral-animation-text-login"></div>
		        <div class="login-container-lateral-animation-text-register">
					<h2>¿Tienes una cuenta en el <br>espacio profesional?</h2>		          	
		          	<a href="<?php echo get_aemi_my_account_url(); ?>" class="btn btn-secondary login-goto-to-login">Ir a la página de inicio de sesión</a>
		        </div>
		      </div>
		    </div>
		    
		    <div class="login-abstract-icon-four"></div>

	  	</div>
	</div>
</div>
<?php get_footer('clean'); ?>
