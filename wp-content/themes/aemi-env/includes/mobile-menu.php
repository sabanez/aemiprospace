<div class="mobile-menu-container">
  <div class="mobile-menu__bg"></div>

  <div class="mobile-menu__box">

    <div class="mobile-menu__profile">
      <a href="<?php echo get_aemi_my_account_url(); ?>" class="mobile-menu__user-box" title="<?php echo esc_attr( __('Go to my account', AEMI_TEXT_DOMAIN)); ?>">
        <span class="mobile-menu__user-box__image-container" data-percent="<?php echo get_aemi_current_user_percentage_level_to_level_up(); ?>">
          <svg xmlns="http://www.w3.org/2000/svg" width="125" height="125" viewBox="0 0 125 125">
            <g transform="translate(0 122) rotate(-90)" fill="#fff" >
              <circle cx="60" cy="60" r="60" stroke="none"/>
              <circle cx="60" cy="60" r="58" fill="none"/>
            </g>
          </svg>
          <span class="mobile-menu__user-box__image" style="background-image: url(<?php echo get_avatar_url( get_current_user_id() ); ?>);"></span>
        </span>
        <span class="mobile-menu__user-box__info">
          <span>
            <span class="mobile-menu__user-box__info-name"><?php echo get_aemi_current_user_name(); ?></span>
            <span class="mobile-menu__user-box__info-description">
              <span class="mobile-menu__user-box__info-description--current-level">
                <?php echo get_aemi_current_user_score_current_level()->name; ?>
              </span>
            </span>
          </span>
        </span>
      </a>
    </div>

    <div class="mobile-menu__nav">
      <ul>
        <?php
          wp_nav_menu( array(
            'theme_location' => 'mobile',
            'items_wrap' => '%3$s',
            'container' => false
          ) );
        ?>
      </ul>
    </div>
  </div>

  <button type="button" class="menu-open-sidebars mobile-menu__close">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M310.6 361.4c12.5 12.5 12.5 32.75 0 45.25C304.4 412.9 296.2 416 288 416s-16.38-3.125-22.62-9.375L160 301.3L54.63 406.6C48.38 412.9 40.19 416 32 416S15.63 412.9 9.375 406.6c-12.5-12.5-12.5-32.75 0-45.25l105.4-105.4L9.375 150.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L160 210.8l105.4-105.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-105.4 105.4L310.6 361.4z"/></svg>
  </button>

</div>