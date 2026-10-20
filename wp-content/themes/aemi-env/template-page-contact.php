<?php
/**
 * Template Name: Contact.
 */
get_header(); ?>
<div class="aemi-new-layout-contact-page">
<main>
        <section class="contactPage pageSpacer" id="contact">
            <h2 class="pageTitle">Ponte en contacto con nuestro equipo</h2>
            <p>Estamos a tu disposición para cualquier duda.</p>
                
            <div class="contact-section">
                <div class="contact-container">
                    <div class="content flex alignStart">
                        <div class="flex leftHolder">
                            <div class="inlineItem">
                                <p class="title">Llámanos:</p>
                                <a href="tel:+34">
                                    <span class="tel flex">
                                        <img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/info.png'); ?>">
                                        <h4>+34 934 45 34 62</h4>
                                    </span>
                                </a>
                            </div>

                            <div class="inlineItem">
<!--                                <p class="title">Comuníquese por Whatsapp:</p>-->
                                <p class="title">Comunícate con nosotros por WhatsApp:</p>
                                <a href="https://wa.me/34635894290" target="_blank">
                                    <span class="whatsapp flex">
                                        <img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/whatsapp.png'); ?>">
                                        <h4>+34 635 89 42 90</h4>
                                    </span>
                                </a>
                            </div>

                            <div class="inlineItem">
                                <p class="title">Síguenos en las redes sociales:</p>
                                <span class="social flex">
                                    <a target="_blank" rel="noreferrer noopener" href="https://www.instagram.com/aemi_microinmunoterapia/?hl=es">
                                        <img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/insta.png'); ?>">
                                    </a>
                                    <a target="_blank" rel="noreferrer noopener" href="https://www.facebook.com/microinmunoterapia/">
                                        <img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/facebook.png'); ?>">
                                    </a>
                                    <a target="_blank" rel="noreferrer noopener" href="https://www.linkedin.com/company/aemi-microinmunoterapia/">
                                        <img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/linkedin-logo.png'); ?>">
                                    </a>
                                    <a target="_blank" rel="noreferrer noopener" href="https://www.youtube.com/c/AEMI_microinmunoterapia">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="1.1em" viewBox="0 0 576 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z"/></svg>
                                    </a>
                                </span>
                            </div>

                            <div class="inlineItem">
                                <span class="flex map">
                                    <img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/location.png'); ?>">
                                    <p>Av. Portal de l'Àngel, 36 | 08002 Barcelona</p>
                                </span>
                            </div>
                        </div>

                        <div class="rightHolder">
                            <div class="card contactForm fullWidth">
                                <div class="card-header">
<!--                                    <h3 class="card-title">Rellenar el formulario</h3>-->
                                    <h3 class="card-title" style="text-overflow: unset;white-space: normal;">Completa el siguiente formulario</h3>
                                </div>

                                <div class="card-content">
                                  <?php echo strtr(do_shortcode('[contact-form-7 id="203" title="Contact"]'), ['----replace--default--user-name---' => sanitize_text_field(get_aemi_current_user_name())]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </main>
</div>
<?php get_footer(); ?>