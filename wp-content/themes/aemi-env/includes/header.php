<?php

$blogName = get_bloginfo( 'name' );
?>
<div class="new-layout-v1">


    <!-- HEADER: Start -->
    <header class="pageSpacer fullWidth">
        <div class="holder flex justifyBetween">
            <div class="leftHolder flex">
                <div class="logo" style="width: 130px">
                    <a href="<?php echo home_url(); ?>" title="<?php echo $blogName; ?>">
                        <img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/images/logo.png'); ?>" title="<?php echo $blogName; ?>" alt="<?php echo $blogName; ?>" />
                    </a>
                </div>

                <?php if (is_user_logged_in()) { ?>
                <div class="flex menuHolder" id="mobileMenu">
                    <nav class="fullWidth flex mobileProfileMenu alignStart">
                        <div class="mobileProfile">
                            <div class="closeSidebar">
                                <svg id="closeIcon" class="svIcon cursor" stroke="currentColor" fill="currentColor"
                                    stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M405 136.798L375.202 107 256 226.202 136.798 107 107 136.798 226.202 256 107 375.202 136.798 405 256 285.798 375.202 405 405 375.202 285.798 256z">
                                    </path>
                                </svg>
                            </div>
                        </div>

                        <ul class="flex mobileNavMenu mobile-menu-item">
                            <?php if (is_user_logged_in()) { ?>

                                <li class="flex mobileProfileMenu">
                                    
                                    <div class="profile flex cursor">
                                        <div style="
                                                margin-right: 15px;
                                                display: flex;
                                                justify-content: center;
                                                align-items: center;
                                                width: 52px;
                                                height: 52px;
                                                border-radius: 50%;
                                                background:
                                                    radial-gradient(closest-side, white 79%, transparent 80%),
                                                    conic-gradient(#EF8933 calc(<?php echo get_aemi_current_user_percentage_level_to_level_up(); ?>%), #46374F 0);
                                "
                                        >
                                        <div style="
                                                text-align: center;
                                                width: 46px;
                                                height: 46px;
                                                background-color: #ddd;
                                                border-radius: 50%;
                                                background-size: cover;
                                                background-image: url(<?php echo get_avatar_url(get_current_user_id()); ?>);
                                                display: flex;
                                                justify-content: center;
                                                align-items: center;
                                                "></div>
                                        </div>
                                        
                                        <div class="profileInfo">
                                            <a href="<?php echo get_aemi_my_account_url(); ?>">
                                                <p id="CustomeSize">
                                                    <span style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;font-weight: 600 !important;text-wrap: none;max-width: 118px; color: #fff;"><?php echo sanitize_text_field(get_aemi_current_user_name()); ?></span>
                                                </p>
                                                <span class="flex profileLevel" style="gap: 2px;color: var(--color-light-green);font-size: 14px;margin-top: 2px;font-weight: 600;">
                                                    <img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/star.png'); ?>" alt="star" style="width: 16px; height: 16px;" />
                                                    <?php echo get_aemi_current_user_score_current_level()->name; ?>
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </li>

                                <?php
                                  $replace_menu = strtr(preg_replace('/(<a[^>]*>)(.*?)(<\/a>)/', '$1<div class="inline flex align-left">$2</div>$3',
                                      wp_nav_menu([
                                          'echo' => false,
                                          'theme_location' => 'primary',
                                          'items_wrap' => '%3$s',
                                          'container' => false,
                                      ])),
                                      [
                                      'class="envixoifv__header_a__link">' => 'class="envixoifv__header_a__link"><div class="inline flex align-left"><svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.703 2.34896C10.7905 2.34375 10.8822 2.34375 11.001 2.34375H13.9999C14.1176 2.34375 14.2093 2.34375 14.2968 2.34896C14.838 2.38186 15.3588 2.56769 15.7986 2.88485C16.2383 3.20201 16.5791 3.63747 16.7812 4.14062C16.8135 4.22083 16.8437 4.30729 16.8801 4.41979L16.8833 4.43021C16.9708 4.66354 17.1697 4.92917 17.4489 5.14167C17.4801 5.16563 17.5114 5.1875 17.5426 5.20833C19.7853 5.21146 20.9562 5.25208 21.7635 5.90938C21.9301 6.04479 22.0812 6.19583 22.2155 6.36146C22.9166 7.22292 22.9166 8.49896 22.9166 11.051C22.9166 11.699 22.9166 12.0219 22.7603 12.275C22.7296 12.3244 22.6947 12.3711 22.6562 12.4146C22.4583 12.6375 22.1489 12.7302 21.5291 12.9167L16.6666 14.375V13.5417C16.6666 12.9891 16.4471 12.4592 16.0564 12.0685C15.6657 11.6778 15.1358 11.4583 14.5833 11.4583H10.4166C9.86405 11.4583 9.33415 11.6778 8.94345 12.0685C8.55275 12.4592 8.33325 12.9891 8.33325 13.5417V14.375L3.47075 12.9167C2.85096 12.7302 2.54054 12.6375 2.34367 12.4146C2.30512 12.371 2.27027 12.3243 2.2395 12.275C2.08325 12.0219 2.08325 11.699 2.08325 11.051C2.08325 8.49896 2.08325 7.22292 2.78429 6.36146C2.91971 6.19479 3.07075 6.04375 3.23638 5.90938C4.04367 5.25313 5.2145 5.21146 7.45825 5.20833C7.4895 5.1875 7.52075 5.16563 7.552 5.14167C7.83013 4.92917 8.03013 4.6625 8.11658 4.43021C8.15617 4.31146 8.18533 4.22188 8.21867 4.13958C8.42091 3.63663 8.76172 3.20138 9.20149 2.88441C9.64126 2.56745 10.1619 2.38177 10.703 2.34896ZM15.4364 5.01875C15.4614 5.08333 15.4885 5.14583 15.5197 5.20833H9.48013C9.51033 5.14583 9.53742 5.08333 9.56346 5.01875V5.01563L9.56867 5.00521L9.57388 4.99271L9.57908 4.98021L9.58221 4.96979L9.58429 4.96458L9.58846 4.95208L9.59263 4.93958L9.59471 4.93333L9.59783 4.925L9.59992 4.91771L9.602 4.91146C9.64263 4.79063 9.65513 4.75208 9.66763 4.72292C9.75945 4.49419 9.9143 4.29622 10.1142 4.15202C10.3141 4.00783 10.5508 3.92332 10.7968 3.90833C10.8301 3.90625 10.8718 3.90625 11.0208 3.90625H13.977C14.126 3.90625 14.1676 3.90625 14.201 3.90833C14.703 3.93958 15.1426 4.25625 15.3312 4.72292C15.3416 4.75208 15.3551 4.78854 15.3958 4.91146L15.3978 4.91771L15.3999 4.925L15.403 4.93333L15.4051 4.93958L15.4093 4.95208L15.4135 4.96458L15.4155 4.96979L15.4197 4.98021L15.4239 4.99271L15.4291 5.00521L15.4333 5.01563L15.4353 5.01875H15.4364ZM14.5833 13.0208H10.4166C10.2785 13.0208 10.146 13.0757 10.0483 13.1734C9.95062 13.2711 9.89575 13.4035 9.89575 13.5417V15.7938C9.89578 15.8978 9.92694 15.9994 9.98524 16.0855C10.0435 16.1717 10.1263 16.2384 10.2228 16.2771L10.952 16.5688C11.9456 16.9663 13.0542 16.9663 14.0478 16.5688L14.777 16.2771C14.8736 16.2384 14.9563 16.1717 15.0146 16.0855C15.0729 15.9994 15.1041 15.8978 15.1041 15.7938V13.5417C15.1041 13.4035 15.0492 13.2711 14.9515 13.1734C14.8539 13.0757 14.7214 13.0208 14.5833 13.0208ZM8.34367 16.0104L3.13117 14.4458C3.16242 17.6896 3.352 20.6771 4.49783 21.6958C5.87075 22.9167 8.08117 22.9167 12.4999 22.9167C16.9187 22.9167 19.1291 22.9167 20.502 21.6958C21.6478 20.6781 21.8374 17.6896 21.8687 14.4458L16.6551 16.0094C16.6159 16.3869 16.4742 16.7466 16.2455 17.0495C16.0168 17.3524 15.7096 17.5871 15.3572 17.7281L14.628 18.0198C13.2619 18.5663 11.7379 18.5663 10.3718 18.0198L9.64263 17.7281C9.29023 17.5871 8.98308 17.3524 8.75433 17.0495C8.52559 16.7466 8.38395 16.3869 8.34471 16.0094L8.34367 16.0104Z" fill="white"></path></svg>',
                                      'WikiMi</div></a>' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20 22H6.5C5.57174 22 4.6815 21.6313 4.02513 20.9749C3.36875 20.3185 3 19.4283 3 18.5V5C3 4.20435 3.31607 3.44129 3.87868 2.87868C4.44129 2.31607 5.20435 2 6 2H20C20.2652 2 20.5196 2.10536 20.7071 2.29289C20.8946 2.48043 21 2.73478 21 3V21C21 21.2652 20.8946 21.5196 20.7071 21.7071C20.5196 21.8946 20.2652 22 20 22ZM19 20V17H6.5C6.10218 17 5.72064 17.158 5.43934 17.4393C5.15804 17.7206 5 18.1022 5 18.5C5 18.8978 5.15804 19.2794 5.43934 19.5607C5.72064 19.842 6.10218 20 6.5 20H19Z" fill="white"></path>
                                      </svg>WikiMi</div></a>',
                                      'Contáctenos</div></a>' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.62 10.79C8.06 13.62 10.38 15.93 13.21 17.38L15.41 15.18C15.68 14.91 16.08 14.82 16.43 14.94C17.55 15.31 18.76 15.51 20 15.51C20.55 15.51 21 15.96 21 16.51V20C21 20.55 20.55 21 20 21C10.61 21 3 13.39 3 4C3 3.45 3.45 3 4 3H7.5C8.05 3 8.5 3.45 8.5 4C8.5 5.25 8.7 6.45 9.07 7.57C9.18 7.92 9.1 8.31 8.82 8.59L6.62 10.79Z" fill="white"></path>
                                      </svg> Contáctenos</div></a>',
                                      ]
                                  );

                                echo preg_replace('/<span class\=\"envixoifv__header_a__item\"><div class\=\"envixoifv__total_unviewed__number\">(.*?)<\/div><\/span>/', '<span class="envixoifv__header_a__item"><div class="envixoifv__total_unviewed__number">$1</div></span></div>', $replace_menu);

                                ?>
                            <?php } else { ?>
                                <li style="margin: 25px 0;"></li>
                            <?php } ?>
                        </ul>

                        <div class="responsiveProfile">
                           
                        </div>
                    </nav>

                    <ul class="bottomNavMenu">
                        <li><a href="<?php echo get_page_url_subscription_buy(); ?>" class="flex">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                                    height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M3.00488 6.99981L11.4502 1.36961C11.7861 1.14568 12.2237 1.14568 12.5596 1.36961L21.0049 6.99981V20.9998C21.0049 21.5521 20.5572 21.9998 20.0049 21.9998H4.00488C3.4526 21.9998 3.00488 21.5521 3.00488 20.9998V6.99981ZM12.0049 10.9998C13.1095 10.9998 14.0049 10.1044 14.0049 8.99981C14.0049 7.89524 13.1095 6.99981 12.0049 6.99981C10.9003 6.99981 10.0049 7.89524 10.0049 8.99981C10.0049 10.1044 10.9003 10.9998 12.0049 10.9998ZM8.00488 15.9998V17.9998H16.0049V15.9998H8.00488ZM8.00488 12.9998V14.9998H16.0049V12.9998H8.00488Z">
                                    </path>
                                </svg>Membresía</a></li>
                        <li><a href="https://www.aemi.es/politica-de-privacidad-y-proteccion-de-datos/" target="_blank" class="flex">
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                                    height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="none" d="M0 0h24v24H0z"></path>
                                    <path
                                        d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 6h2v2h-2V7zm0 4h2v6h-2v-6z">
                                    </path>
                                </svg>Política de privacidad</a></li>
                        <li><a href="<?php echo wp_logout_url(home_url()); ?>" class="flex"><svg stroke="currentColor" fill="currentColor" stroke-width="0"
                                    viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="none" stroke-width="2"
                                        d="M13,9 L13,2 L1,2 L1,22 L13,22 L13,15 M22,12 L5,12 M17,7 L22,12 L17,17">
                                    </path>
                                </svg>Cerrar sesión</a></li>
                    </ul>
                </div>
                <?php } ?>
            </div>

            <?php if (is_user_logged_in()) { ?>
            <div class="rightHolder flex">
                <ul class="iconMenu flex">
                    <li class="searchHolder" id="searchInputHolder">
                        <form action="<?php echo home_url(); ?>" method="GET">
                            <span class="searchInput flex">
                                <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M31.875 31.875L21.625 21.625M1.125 13.0833C1.125 14.6537 1.43431 16.2087 2.03527 17.6596C2.63624 19.1104 3.51708 20.4287 4.62751 21.5392C5.73795 22.6496 7.05623 23.5304 8.50708 24.1314C9.95793 24.7324 11.5129 25.0417 13.0833 25.0417C14.6537 25.0417 16.2087 24.7324 17.6596 24.1314C19.1104 23.5304 20.4287 22.6496 21.5392 21.5392C22.6496 20.4287 23.5304 19.1104 24.1314 17.6596C24.7324 16.2087 25.0417 14.6537 25.0417 13.0833C25.0417 11.5129 24.7324 9.95793 24.1314 8.50708C23.5304 7.05623 22.6496 5.73795 21.5392 4.62751C20.4287 3.51708 19.1104 2.63624 17.6596 2.03527C16.2087 1.43431 14.6537 1.125 13.0833 1.125C11.5129 1.125 9.95793 1.43431 8.50708 2.03527C7.05623 2.63624 5.73795 3.51708 4.62751 4.62751C3.51708 5.73795 2.63624 7.05623 2.03527 8.50708C1.43431 9.95793 1.125 11.5129 1.125 13.0833Z" stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                <input type="text" name="s" placeholder="<?php echo esc_attr(__('Write what you are looking for...', AEMI_TEXT_DOMAIN)); ?>" class="fullWidth" />
                            </span>
                        </form>
                    </li>
                    <li class="cursor" id="searchIcon">
                        <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M31.875 31.875L21.625 21.625M1.125 13.0833C1.125 14.6537 1.43431 16.2087 2.03527 17.6596C2.63624 19.1104 3.51708 20.4287 4.62751 21.5392C5.73795 22.6496 7.05623 23.5304 8.50708 24.1314C9.95793 24.7324 11.5129 25.0417 13.0833 25.0417C14.6537 25.0417 16.2087 24.7324 17.6596 24.1314C19.1104 23.5304 20.4287 22.6496 21.5392 21.5392C22.6496 20.4287 23.5304 19.1104 24.1314 17.6596C24.7324 16.2087 25.0417 14.6537 25.0417 13.0833C25.0417 11.5129 24.7324 9.95793 24.1314 8.50708C23.5304 7.05623 22.6496 5.73795 21.5392 4.62751C20.4287 3.51708 19.1104 2.63624 17.6596 2.03527C16.2087 1.43431 14.6537 1.125 13.0833 1.125C11.5129 1.125 9.95793 1.43431 8.50708 2.03527C7.05623 2.63624 5.73795 3.51708 4.62751 4.62751C3.51708 5.73795 2.63624 7.05623 2.03527 8.50708C1.43431 9.95793 1.125 11.5129 1.125 13.0833Z"
                                stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </li>
                    <li class="cursor">
                        <a href="<?php echo get_favorite_page_url(); ?>">
                            <svg width="37" height="33" viewBox="0 0 37 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M33.092 17.6185L18.5162 32L3.94041 17.6185C2.97901 16.6865 2.22172 15.5662 1.71624 14.3283C1.21077 13.0904 0.968046 11.7616 1.00337 10.4257C1.03869 9.08973 1.35129 7.77553 1.92149 6.56585C2.49168 5.35616 3.30712 4.2772 4.31644 3.39689C5.32577 2.51659 6.50712 1.85402 7.78611 1.4509C9.0651 1.04778 10.414 0.912858 11.7479 1.05462C13.0818 1.19637 14.3718 1.61174 15.5367 2.27457C16.7015 2.9374 17.716 3.83333 18.5162 4.90594C19.3199 3.84112 20.3355 2.95302 21.4996 2.29722C22.6636 1.64143 23.951 1.23206 25.2812 1.09473C26.6113 0.957401 27.9556 1.09507 29.2299 1.49913C30.5042 1.90319 31.6811 2.56493 32.6869 3.44294C33.6926 4.32095 34.5057 5.39633 35.0751 6.60177C35.6445 7.80722 35.958 9.11678 35.9961 10.4485C36.0341 11.7802 35.7958 13.1054 35.2961 14.3411C34.7964 15.5769 34.046 16.6966 33.092 17.6301"
                                    stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    </li>
                </ul>

                <div class="profileMenu">
                    <div class="profile flex cursor">
                        <div style="
                                  margin-left: 25px;
                                  display: flex;
                                  justify-content: center;
                                  align-items: center;
                                  width: 52px;
                                  height: 52px;
                                  border-radius: 50%;
                                  background:
                                    radial-gradient(closest-side, white 79%, transparent 80%),
                                    conic-gradient(#EF8933 calc(<?php echo get_aemi_current_user_percentage_level_to_level_up(); ?>%), #46374F 0);
"
                        >
                        <div style="
                                text-align: center;
                                width: 46px;
                                height: 46px;
                                background-color: #ddd;
                                border-radius: 50%;
                                background-size: cover;
                                background-image: url(<?php echo get_avatar_url(get_current_user_id()); ?>);
                                display: flex;
                                justify-content: center;
                                align-items: center;
                                "></div>
                        </div>
                        
                        <div class="profileInfo">
                            <a href="<?php echo get_aemi_my_account_url(); ?>">
                                <p id="CustomeSize"><span style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;font-weight: 600 !important;text-wrap: none;max-width: 118px; color: #fff;"><?php echo sanitize_text_field(get_aemi_current_user_name()); ?></span></p>
                                <span class="flex profileLevel">
                                    <img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/star.png'); ?>" alt="star" />
                                    <?php echo get_aemi_current_user_score_current_level()->name; ?>
                                </span>
                            </a>
                        </div>
                        <span class="dropdownIcon" id="profileDropdownTrigger">
                            <svg width="12" height="6" viewBox="0 0 12 6" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 0L6 6L12 0H0Z" fill="white" />
                            </svg>
                        </span>

                        <div class="profileDropdown fullWidth" id="profileDropdown">
                            <ul>
								<li>
                                    <a href="https://prospace.aemi.es/mi-cuenta/">
                                        <span class="flex menuGap">
                                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="user" 
     class="svg-inline--fa fa-user fa-w-14" role="img" 
     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="height:1em; width:1em;">
    <path fill="currentColor" d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0
        96 57.3 96 128s57.3 128 128 128zm89.6 32h-17.8c-22.2 
        10.3-46.8 16-72 16s-49.8-5.7-72-16h-17.8C60.5 288 0 348.5 
        0 424v24c0 13.3 10.7 24 24 24h400c13.3 0 24-10.7 
        24-24v-24c0-75.5-60.5-136-134.4-136z"></path>
</svg>

                                            Mi perfil
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo get_page_url_subscription_buy(); ?>">
                                        <span class="flex menuGap">
                                            <svg stroke="currentColor" fill="currentColor" stroke-width="0"
                                                viewBox="0 0 24 24" height="1em" width="1em"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M3.00488 6.99981L11.4502 1.36961C11.7861 1.14568 12.2237 1.14568 12.5596 1.36961L21.0049 6.99981V20.9998C21.0049 21.5521 20.5572 21.9998 20.0049 21.9998H4.00488C3.4526 21.9998 3.00488 21.5521 3.00488 20.9998V6.99981ZM12.0049 10.9998C13.1095 10.9998 14.0049 10.1044 14.0049 8.99981C14.0049 7.89524 13.1095 6.99981 12.0049 6.99981C10.9003 6.99981 10.0049 7.89524 10.0049 8.99981C10.0049 10.1044 10.9003 10.9998 12.0049 10.9998ZM8.00488 15.9998V17.9998H16.0049V15.9998H8.00488ZM8.00488 12.9998V14.9998H16.0049V12.9998H8.00488Z">
                                                </path>
                                            </svg>
                                            Membresía
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.aemi.es/politica-de-privacidad-y-proteccion-de-datos/" target="_blank">
                                        <span class="flex  menuGap">
                                            <svg stroke="currentColor" fill="currentColor" stroke-width="0"
                                                viewBox="0 0 24 24" height="1em" width="1em"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill="none" d="M0 0h24v24H0z"></path>
                                                <path
                                                    d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 6h2v2h-2V7zm0 4h2v6h-2v-6z">
                                                </path>
                                            </svg>
                                            Política de privacidad
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo wp_logout_url(home_url()); ?>">
                                        <span class="flex  menuGap">
                                            <svg stroke="currentColor" fill="currentColor" stroke-width="0"
                                                viewBox="0 0 24 24" height="1em" width="1em"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill="none" stroke-width="2"
                                                    d="M13,9 L13,2 L1,2 L1,22 L13,22 L13,15 M22,12 L5,12 M17,7 L22,12 L17,17">
                                                </path>
                                            </svg>
                                            Cerrar sesión
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="menuBar">
                        <svg width="18" height="15" viewBox="0 0 18 12" fill="none" xmlns="http://www.w3.org/2000/svg"
                            id="menuBar" class="cursor">
                            <path d="M0 12H18V10H0V12ZM0 7H18V5H0V7ZM0 0V2H18V0H0Z" fill="white" />
                        </svg>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </header>
    <!-- HEADER: End -->
</div>