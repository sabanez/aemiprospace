<?php /*
<footer class="aemi-footer">
      <div class="aemi-footer__primary">
        <div class="container">
          <div class="row">
            <div class="col-lg-12">
              <div class="aemi-footer__title">
                <p class="default-title"><?php echo __( 'Quick access navigation', AEMI_TEXT_DOMAIN ); ?></p>
              </div>
              <div class="aemi-footer__menu">
                <ul class="aemi-footer__menu-ul">
                  <?php
                    wp_nav_menu( array(
                      'theme_location' => ( !is_user_logged_in() ? 'footer_user_not_logged_in' : 'footer' ),
                      'items_wrap' => '%3$s',
                      'container' => false
                    ) );
                  ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="aemi-footer__creditos-container">
        <div class="container">
          <p>© <?php echo date('Y') ?> AEMI. <?php echo __( 'All rights reserved.', AEMI_TEXT_DOMAIN ); ?></p>
        </div>
      </div>
    </footer>
    <?php require_once 'includes/mobile-menu.php'; ?> */
?>
    <div class="extra-margin-desarrollado-por-envixo"></div>
    <?php 
    /*<div class="desarrollado-por-envixo">
      <a href="https://envixo.com/" target="_blank" title="Desarrollado por Envixo">Desarrollado por <strong>Envixo</strong></a>
    </div>*/ ?>
    <?php wp_footer(); ?>
  </body>
</html> 