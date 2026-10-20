<?php get_header();

$thumbnail = get_the_post_thumbnail();
$featured_img_url = get_the_post_thumbnail_url($post->ID);

$content_for_subscribers = get_post_meta($post->ID, '_content_for_subscribers', true);
$aemi_disable_thumbnail_in_single = get_post_meta($post->ID, '_aemi_disable_thumbnail_in_single', true);
$estimateReadingTime = estimateReadingTime(get_the_content());
$minutes = $estimateReadingTime['minutes'];
$minutes = ($minutes ? $minutes : 1);
$post_date_format = 'M d, Y h:i A';
$local = strtolower(get_locale());

$categories = get_the_category($post->ID);
$categories_ids = [];

if ($categories) {
    foreach ($categories as $list_categories) {
        $categories_ids[] = $list_categories->term_id;
    }
}

$related_content_posts = get_posts(['posts_per_page' => 5, 'exclude' => [$post->ID], 'category' => implode(',', $categories_ids)]);

if (strpos($local, 'en_') !== false || $local == 'en') {
    $post_date_format = 'm-d-Y';
}

$category_list = get_aemi_content_types_post($post->ID, 'category');
$contentLevel_list = get_aemi_content_types_post($post->ID, 'content_level');

$aemi_user_can_view_pro_content = aemi_user_can_view_pro_content($post->ID);

$aemi_level_by_post = aemi_get_level_related_to_post_id($post->ID);

$contentTableInformation = '';
$contentTableInformationNoPontuation = '';

$level_relation = get_posts_aemi_content_level_names($post->ID, 0, true);

if ($aemi_level_by_post) {
    foreach ($aemi_level_by_post as $level) {
        if ($level->sub_categories) {
            foreach ($level->sub_categories as $sub_categories) {
                $contentTableInformation .= '<tr>';
                $contentTableInformation .= '<td><p>'.$sub_categories->description.'</p></td>';
                $contentTableInformation .= '<td class="text-center"><span>'.$sub_categories->level_points.'</span></td>';
                $contentTableInformation .= '</tr>';

                $contentTableInformationNoPontuation .= ($contentTableInformationNoPontuation ? '<div></div>' : '').$sub_categories->description.'';
            }
        }
    }
}

if (is_user_logged_in() && get_current_user_id() && $aemi_user_can_view_pro_content) {
    update_user_meta(get_current_user_id(), '_last_content_accessed', $post->ID);
}
$favorite_button = favorite_button($post->ID);
?>
<main class="wikiDetailsPage">
  <section class="hero">
    <img class="hero-bg-img" src="<?php echo $featured_img_url; ?>" alt="banner">
  </section>
  <section class="pageSpacer holder">
    <div class="contentHolder flex gap20">
      <div class="leftContent card">
        <div class="badgesHolder fullWidth flex">
          <div class="flex highligher">
            <img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/levelsdotfyi.png'); ?>" alt="levelsdotfyi">
            <span>Nivel: <?php echo implode(', ', $level_relation); ?></span>
          </div>
          <ul class="badgesHolder flex">
            <li><?php echo get_aemi_content_types_post($post->ID, 'content_type', false); ?></li>
            
            <?php if (verify_user_viewed_post($post->ID, get_current_user_id())) { ?>
            <li class="single-page-element-viewed">Visualizado</li>
            <?php } ?>
          </ul>
        </div>
        <div class="card-content dynamicRenderSection">
          <h3 class="blogTitle">
            <?php echo get_the_title(); ?>
          </h3>
          <?php
              if (have_posts()) {
                  while (have_posts()) {
                      the_post();

                      the_content();
                  }
              }
?>
        </div>
        <?php echo do_shortcode('[skills_and_points_box disable_multiple_loaded="true"]'); ?>
      </div>
        
      <div class="rightContent">
        <div class="informationCard card">
          <div class="card-header">
            <h4>Información de contenido</h4>
          </div>
          <div class="card-content">
            <ul>
              <li>
                <p>
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M12 2C17.523 2 22 6.477 22 12C22 17.523 17.523 22 12 22C6.477 22 2 17.523 2 12C2 6.477 6.477 2 12 2ZM12 6C11.7348 6 11.4804 6.10536 11.2929 6.29289C11.1054 6.48043 11 6.73478 11 7V12C11.0001 12.2652 11.1055 12.5195 11.293 12.707L14.293 15.707C14.4816 15.8892 14.7342 15.99 14.9964 15.9877C15.2586 15.9854 15.5094 15.8802 15.6948 15.6948C15.8802 15.5094 15.9854 15.2586 15.9877 14.9964C15.99 14.7342 15.8892 14.4816 15.707 14.293L13 11.586V7C13 6.73478 12.8946 6.48043 12.7071 6.29289C12.5196 6.10536 12.2652 6 12 6Z"
                      fill="#555555"></path>
                  </svg>
                  Tiempo estimado de lectura
                </p>
                <span>
                  <?php echo $minutes; ?>
                  <?php echo __($minutes > 1 ? 'minutes' : 'minute', AEMI_TEXT_DOMAIN); ?>
                </span>
              </li>
              <li>
                <p>
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M9 10H7V12H9V10ZM13 10H11V12H13V10ZM17 10H15V12H17V10ZM19 3H18V1H16V3H8V1H6V3H5C4.46957 3 3.96086 3.21071 3.58579 3.58579C3.21071 3.96086 3 4.46957 3 5V19C3 19.5304 3.21071 20.0391 3.58579 20.4142C3.96086 20.7893 4.46957 21 5 21H19C19.5304 21 20.0391 20.7893 20.4142 20.4142C20.7893 20.0391 21 19.5304 21 19V5C21 4.46957 20.7893 3.96086 20.4142 3.58579C20.0391 3.21071 19.5304 3 19 3ZM19 19H5V8H19V19Z"
                      fill="#555555"></path>
                  </svg>
                  Fecha de publicación
                </p>
                <span><?php echo get_the_date($post_date_format, $post); ?></span>
              </li>
              <li>
                <p>
                  <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M10 12.5C11.3807 12.5 12.5 11.3807 12.5 10C12.5 8.61929 11.3807 7.5 10 7.5C8.61929 7.5 7.5 8.61929 7.5 10C7.5 11.3807 8.61929 12.5 10 12.5Z"
                      fill="#555555"></path>
                    <path
                      d="M19.3375 9.7875C18.6024 7.88603 17.3262 6.24164 15.6668 5.05755C14.0073 3.87347 12.0372 3.20161 10 3.125C7.96283 3.20161 5.99275 3.87347 4.33326 5.05755C2.67377 6.24164 1.39761 7.88603 0.662509 9.7875C0.612863 9.92482 0.612863 10.0752 0.662509 10.2125C1.39761 12.114 2.67377 13.7584 4.33326 14.9424C5.99275 16.1265 7.96283 16.7984 10 16.875C12.0372 16.7984 14.0073 16.1265 15.6668 14.9424C17.3262 13.7584 18.6024 12.114 19.3375 10.2125C19.3872 10.0752 19.3872 9.92482 19.3375 9.7875ZM10 14.0625C9.19652 14.0625 8.41108 13.8242 7.743 13.3778C7.07493 12.9315 6.55423 12.297 6.24675 11.5547C5.93927 10.8123 5.85882 9.99549 6.01557 9.20745C6.17232 8.4194 6.55924 7.69553 7.12739 7.12738C7.69554 6.55923 8.41941 6.17231 9.20745 6.01556C9.9955 5.85881 10.8123 5.93926 11.5547 6.24674C12.297 6.55422 12.9315 7.07492 13.3779 7.743C13.8242 8.41107 14.0625 9.19651 14.0625 10C14.0609 11.0769 13.6323 12.1093 12.8708 12.8708C12.1093 13.6323 11.0769 14.0608 10 14.0625Z"
                      fill="#555555"></path>
                  </svg>
                  Vistas totales
                </p>
                <span><?php echo AemiPagePostsViews::get_total_page_view($post->ID); ?></span>
              </li>
              <li>
                <p>
                  <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.06665 10.2668C7.09419 10.2668 6.16156 9.88049 5.47393 9.19286C4.78629 8.50522 4.39999 7.57259 4.39999 6.60013V3.6668C4.39999 3.4723 4.47725 3.28578 4.61477 3.14825C4.7523 3.01073 4.93883 2.93346 5.13332 2.93346H5.86665C6.06114 2.93346 6.24767 2.8562 6.3852 2.71868C6.52272 2.58115 6.59999 2.39462 6.59999 2.20013C6.59999 2.00564 6.52272 1.81911 6.3852 1.68159C6.24767 1.54406 6.06114 1.4668 5.86665 1.4668H5.13332C4.54984 1.4668 3.99026 1.69858 3.57768 2.11116C3.1651 2.52374 2.93332 3.08332 2.93332 3.6668V6.60013C2.93426 7.4286 3.13629 8.24447 3.52204 8.97766C3.90779 9.71084 4.46575 10.3394 5.14799 10.8095C5.80346 11.3874 6.33504 12.0921 6.71064 12.8811C7.08623 13.6701 7.29805 14.527 7.33332 15.4001C7.33332 16.7616 7.87415 18.0673 8.83684 19.0299C9.79952 19.9926 11.1052 20.5335 12.4667 20.5335C13.8281 20.5335 15.1338 19.9926 16.0965 19.0299C17.0592 18.0673 17.6 16.7616 17.6 15.4001V14.5641C18.2913 14.3856 18.8937 13.9612 19.2944 13.3703C19.6951 12.7794 19.8666 12.0626 19.7766 11.3543C19.6866 10.6461 19.3415 9.99493 18.8058 9.52296C18.2701 9.051 17.5806 8.79061 16.8667 8.79061C16.1527 8.79061 15.4633 9.051 14.9275 9.52296C14.3918 9.99493 14.0467 10.6461 13.9567 11.3543C13.8667 12.0626 14.0382 12.7794 14.4389 13.3703C14.8396 13.9612 15.442 14.3856 16.1333 14.5641V15.4001C16.1333 16.3726 15.747 17.3052 15.0594 17.9929C14.3717 18.6805 13.4391 19.0668 12.4667 19.0668C11.4942 19.0668 10.5616 18.6805 9.87393 17.9929C9.18629 17.3052 8.79999 16.3726 8.79999 15.4001C8.83711 14.5259 9.05122 13.6683 9.42937 12.8793C9.80752 12.0902 10.3418 11.3861 11 10.8095C11.6795 10.3378 12.2346 9.7085 12.6178 8.97541C13.0009 8.24232 13.2007 7.4273 13.2 6.60013V3.6668C13.2 3.08332 12.9682 2.52374 12.5556 2.11116C12.143 1.69858 11.5835 1.4668 11 1.4668H10.2667C10.0722 1.4668 9.88563 1.54406 9.74811 1.68159C9.61058 1.81911 9.53332 2.00564 9.53332 2.20013C9.53332 2.39462 9.61058 2.58115 9.74811 2.71868C9.88563 2.8562 10.0722 2.93346 10.2667 2.93346H11C11.1945 2.93346 11.381 3.01073 11.5185 3.14825C11.6561 3.28578 11.7333 3.4723 11.7333 3.6668V6.60013C11.7333 7.08164 11.6385 7.55844 11.4542 8.0033C11.2699 8.44816 10.9999 8.85237 10.6594 9.19286C10.3189 9.53334 9.91469 9.80342 9.46982 9.98769C9.02496 10.172 8.54817 10.2668 8.06665 10.2668ZM16.8667 13.2001C16.4777 13.2001 16.1046 13.0456 15.8296 12.7706C15.5545 12.4955 15.4 12.1224 15.4 11.7335C15.4 11.3445 15.5545 10.9714 15.8296 10.6964C16.1046 10.4213 16.4777 10.2668 16.8667 10.2668C17.2556 10.2668 17.6287 10.4213 17.9037 10.6964C18.1788 10.9714 18.3333 11.3445 18.3333 11.7335C18.3333 12.1224 18.1788 12.4955 17.9037 12.7706C17.6287 13.0456 17.2556 13.2001 16.8667 13.2001Z"
                      fill="#555555"></path>
                  </svg>
                  Skills
                </p>
              </li>
            </ul>
            <div class="content-box">
              <?php echo $contentTableInformationNoPontuation; ?>
            </div>
          </div>
        </div>
        
        <?php if ($related_content_posts) { ?>
        <div class="moreContent informationCard card">
          <div class="card-header">
            <h4>Más contenido relacionado</h4>
          </div>
          <div class="card-content">
            <?php foreach ($related_content_posts as $related_post) { ?>
                <?php $content_for_subscribers = get_post_meta($related_post->ID, '_content_for_subscribers', true); ?>
                <div class="docChild <?php echo $content_for_subscribers ? 'orange-box' : ''; ?>">
                  <div class="webLeft">
                    <a href="<?php echo get_permalink($related_post->ID); ?>">
                    <?php
                      $thumbnail = get_the_post_thumbnail($related_post->ID, 'thumbnail');
                echo $thumbnail ? $thumbnail : '<img width="305" height="205" src="'.get_template_directory_uri().'/assets/images/box-posts-default.jpg" class="attachment-wikimi-thumbnail size-wikimi-thumbnail wp-post-image" />';
                ?>
                    </a>
                    <?php if ($content_for_subscribers) { ?>
                      <span class="content_type_related_pro">Socio</span>
                    <?php } ?>
                    
                    <?php if (verify_user_viewed_post($related_post->ID, get_current_user_id())) { ?>
                      <span class="content_type_related_viewed_content">Visualizado</span>
                    <?php } ?>
                    <!-- <span class="content_type_related_content"><?php echo get_aemi_content_types_post($related_post->ID, 'content_type', false); ?></span> -->
                  </div>
                  <div class="webFich">
                    <h3>
                    <a href="<?php echo get_permalink($related_post->ID); ?>"><?php echo sanitize_text_field($related_post->post_title); ?></a>
                    </h3>
                    <div class="redPub">
                      <a href="<?php echo get_permalink($related_post->ID); ?>"><span>Ver 
                        <img style="position: relative; top: 2px;" src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/rightarow.png'); ?>" alt="Ver"></span></a>
                    </div>
                  </div>
                </div>
            <?php } ?>
          </div>
        </div>
        <?php } ?>
      </div>
    </div>
  </section>
</main>

<?php if ($aemi_user_can_view_pro_content == false) { ?>
<div class="overlaySub"></div>
<div class="scoreLevel1" style=" display: block; backdrop-filter: blur(8px); opacity: 1; visibility: visible; transform: scale(1);">
  <div class="mainScoreU" style="width: 914px">
    <div class="exclusive">
      <img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/images/learning.png'); ?>" alt="learning">
      <h1>¡Este es contenido exclusivo para suscriptores!</h1>
      <p>
        Lamentablemente, este contenido solo está disponible para usuarios asociados.<br>
        ¿Quieres conocer las ventajas y hacerte usuario Socio?
      </p>
      <ul>
        <li>
          <a href="javascript:history.back()" class="goBack">
            <img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/left-arrow.png'); ?>" alt="left-arrow">
            <h3>Regresa</h3>
          </a>
        </li>
        <li>
          <a href="<?php echo get_page_url_subscription_buy(); ?>" class="subPlan">
            <img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/star3.png'); ?>" alt="star">
            <h3>Suscríbete al plan</h3>
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>
<?php } ?>
<?php get_footer(); ?>