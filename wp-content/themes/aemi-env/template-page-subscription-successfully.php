<?php
/**
 * Template Name: Subscription successfully!
 */
get_header(); ?>
<main class="page-price-suscripciones">
	<div class="container">
		<div class="col-md-12">
			<div class="userCard card alreadySubscribedCard">

				<div class="card-content">
           			<?php if ( is_user_logged_in() ) { ?>	
	       				<?php

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
	       				?>
						<span class="-page-suscripciones_susccess__icon" style="margin-bottom: 0;">
							<svg style="width: 80px;height:80px" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M18.4832 16.7671C17.1164 17.0953 15.6895 17.0794 14.3303 16.7208C12.9712 16.3622 11.7221 15.6721 10.6951 14.7124C9.66804 13.7526 8.89506 12.5531 8.44535 11.2213C7.99564 9.88951 7.88325 8.46696 8.11824 7.08105C8.0825 7.11557 8.04477 7.14796 8.00524 7.17805C7.72524 7.39105 7.37524 7.47005 6.67524 7.62805L6.04024 7.77205C3.58024 8.32905 2.35024 8.60706 2.05723 9.54806C1.76523 10.4881 2.60324 11.4691 4.28024 13.4301L4.71424 13.9371C5.19024 14.4941 5.42923 14.7731 5.53623 15.1171C5.64323 15.4621 5.60723 15.8341 5.53523 16.5771L5.46923 17.2541C5.21623 19.8711 5.08924 21.1791 5.85524 21.7601C6.62124 22.3421 7.77323 21.8121 10.0752 20.7511L10.6722 20.4771C11.3262 20.1751 11.6532 20.0251 12.0002 20.0251C12.3472 20.0251 12.6742 20.1751 13.3292 20.4771L13.9242 20.7511C16.2272 21.8111 17.3792 22.3411 18.1442 21.7611C18.9112 21.1791 18.7842 19.8711 18.5312 17.2541L18.4832 16.7671Z" fill="#EF8933"></path>
								<path opacity="0.5" d="M9.15319 5.408L8.82519 5.996C8.46519 6.642 8.28519 6.965 8.00519 7.178C8.04519 7.148 8.08219 7.116 8.11819 7.081C7.88314 8.46701 7.99553 9.88967 8.44528 11.2216C8.89504 12.5535 9.66812 13.7531 10.6953 14.7128C11.7225 15.6726 12.9716 16.3627 14.331 16.7212C15.6903 17.0797 17.1173 17.0954 18.4842 16.767L18.4642 16.577C18.3932 15.834 18.3572 15.462 18.4642 15.117C18.5712 14.773 18.8092 14.494 19.2862 13.937L19.7202 13.43C21.3972 11.47 22.2352 10.489 21.9422 9.548C21.6502 8.607 20.4202 8.328 17.9602 7.772L17.3242 7.628C16.6252 7.47 16.2752 7.391 15.9942 7.178C15.7142 6.965 15.5342 6.642 15.1742 5.996L14.8472 5.408C13.5802 3.136 12.9472 2 12.0002 2C11.0532 2 10.4202 3.136 9.15319 5.408Z" fill="#EF8933"></path>
							</svg>
						</span>

						<p class="card-description" style="color: var(--color-secondary);">¡Enhorabuena, ya eres socio de AEMI!</p>

						<div class="subscriptionCard card" style="width: 340px; max-width: 90%; overflow: unset;">
                            <p class="flex justifyBetween">
                                Mis suscripciones
<!--                                <span class="tooltip">-->
<!--                                    <img src="--><?php //echo get_aemi_url_assets_imgs('new-layout-v1/icon/not-find.png'); ?><!--" alt="message" />-->
<!--                                    Información-->
<!--                                    <span class="tooltiptext">Las puntuaciones se calculan en función de los puntos que-->
<!--                                         recibió.</span>-->
<!--                                </span>-->
                            </p>

                            <p class="flex justifyBetween">Plan actual<span><?php echo current_user_can('administrator') ? 'Pro (Adm)' : $user_data['membership_level_alias']; ?></span></p>
                            <p class="flex justifyBetween">Válido hasta<span><?php echo ucfirst(get_replace_month_by_current_lang(esc_attr($member_current_expiry_date))); ?></span></p>
                        </div>

					<?php } ?>
					<a href="<?php echo home_url(); ?>" title="<?php echo __('Go to homepage', AEMI_TEXT_DOMAIN); ?>" class="btn-primary" style="display: inline-flex; align-items: center;"><?php echo __('Go to homepage', AEMI_TEXT_DOMAIN); ?></a>
				</div>
			</div>
		</div>
	</div>
</main>
<?php get_footer(); ?>