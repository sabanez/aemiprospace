<?php get_header();
$get_favorite_pages_ids = get_favorite_pages();

if (!is_array($get_favorite_pages_ids)) {
    $get_favorite_pages_ids = unserialize($get_favorite_pages_ids);
}

$favorites = ($get_favorite_pages_ids && count($get_favorite_pages_ids) > 0 ? get_posts(['post__in' => $get_favorite_pages_ids]) : false);
?>
<div class="aemi-new-layout-page-favourites-page">
  <main>
    <section class="favouritePage container" id="contact">
      <h2 class="pageTitle">Tus favoritos</h2>
<!--      <p class="pageDescription">Encuentra todos tus artículos favoritos aquí.<!-- artículos favoritos.</p>-->
      <p class="pageDescription">Aquí encontrarás todos tus artículos favoritos.</p>

      <div class="resultsHolder">
      <?php if ($favorites) { ?>
        <?php foreach ($favorites as $favorite) {
            $thumbnail = get_the_post_thumbnail($favorite->ID, 'wikimi-thumbnail', ['class' => 'card-image-full']);
            $thumbnail = $thumbnail ? $thumbnail : '<img width="305" height="205" src="'.get_template_directory_uri().'/assets/images/box-posts-default.jpg" class="attachment-wikimi-thumbnail size-wikimi-thumbnail wp-post-image card-image-full" />';
            $content_type = get_aemi_content_types_post($favorite->ID, 'content_type', false);
            $favorite_button = favorite_button($favorite->ID);
            ?>
          <div class="card">
            <div class="card-image-holder">
              <a href="<?php echo get_permalink($favorite->ID); ?>"><?php echo $thumbnail; ?></a>

              <div class="icons">
                <button type="button" class="heartIcon <?php echo $favorite_button['class']; ?>" data-id="<?php echo $favorite_button['data-id']; ?>">
                  <svg width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="mdi:heart-outline">
                      <path id="Vector"
                        d="M14.6207 22.4146L14.4998 22.5354L14.3669 22.4146C8.62734 17.2067 4.83317 13.7629 4.83317 10.2708C4.83317 7.85417 6.64567 6.04167 9.06234 6.04167C10.9232 6.04167 12.7357 7.25 13.3761 8.89333H15.6236C16.264 7.25 18.0765 6.04167 19.9373 6.04167C22.354 6.04167 24.1665 7.85417 24.1665 10.2708C24.1665 13.7629 20.3723 17.2067 14.6207 22.4146ZM19.9373 3.625C17.8348 3.625 15.8169 4.60375 14.4998 6.13833C13.1828 4.60375 11.1648 3.625 9.06234 3.625C5.34067 3.625 2.4165 6.53708 2.4165 10.2708C2.4165 14.8262 6.52484 18.56 12.7478 24.2029L14.4998 25.7979L16.2519 24.2029C22.4748 18.56 26.5832 14.8262 26.5832 10.2708C26.5832 6.53708 23.659 3.625 19.9373 3.625Z"
                        fill="#FF3366"></path>
                    </g>
                  </svg>
                </button>

                <div class="webniar">
<!--                  <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">-->
<!--                    <g id="solar:play-bold" opacity="0.8">-->
<!--                      <path id="Vector"-->
<!--                        d="M15.1649 6.62461C15.5051 6.80552 15.7897 7.0756 15.9881 7.40589C16.1865 7.73618 16.2914 8.11424 16.2914 8.49957C16.2914 8.88489 16.1865 9.26295 15.9881 9.59324C15.7897 9.92353 15.5051 10.1936 15.1649 10.3745L6.0897 15.3095C4.62841 16.1042 2.8335 15.0701 2.8335 13.4352V3.56461C2.8335 1.92836 4.62841 0.8949 6.0897 1.68894L15.1649 6.62461Z"-->
<!--                        fill="var(--color-purple)"></path>-->
<!--                    </g>-->
<!--                  </svg>-->
                    <?php if($content_type === "Webinar") {
                        echo '<svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="solar:play-bold" opacity="0.8"><path id="Vector" d="M15.1649 6.62461C15.5051 6.80552 15.7897 7.0756 15.9881 7.40589C16.1865 7.73618 16.2914 8.11424 16.2914 8.49957C16.2914 8.88489 16.1865 9.26295 15.9881 9.59324C15.7897 9.92353 15.5051 10.1936 15.1649 10.3745L6.0897 15.3095C4.62841 16.1042 2.8335 15.0701 2.8335 13.4352V3.56461C2.8335 1.92836 4.62841 0.8949 6.0897 1.68894L15.1649 6.62461Z" fill="var(--color-purple)" /></g></svg>';
                    }
                    ?>
                  <p><a href="<?php echo get_permalink($favorite->ID); ?>"><?php echo sanitize_text_field($content_type); ?></a></p>
                </div>
              </div>
            </div>

            <div class="card-content">
              <a href="<?php echo get_permalink($favorite->ID); ?>" class="card-title"><?php echo sanitize_text_field($favorite->post_title); ?></a>
<!--              <a href="--><?php //echo get_permalink($favorite->ID); ?><!--" class="card-para-info">--><?php //echo get_aemi_text_excerpt($favorite, 350); ?><!--</a>-->
            </div>
          </div>
            <?php } ?>
        <?php } else { ?>
          <div class="col-lg-12">
            <div class="page-favorite__no-content">
              <h2><?php echo __('No content bookmarked', AEMI_TEXT_DOMAIN); ?></h2>
            </div>
          </div>
        <?php } ?>
        
      </div>


      <div class="flex justifyCenter">
<!--        <a class="btn-primary" href="--><?php //echo home_url(); ?><!--" title="--><?php //echo __('Discover more', AEMI_TEXT_DOMAIN); ?><!--">Descubra más artículos</a>-->
        <a class="btn-primary" href="<?php echo get_nav_menu_url_by_title('WikiMi'); ?>" title="<?php echo __('Discover more', AEMI_TEXT_DOMAIN); ?>">Descubra más artículos</a>
      </div>
    </section>
  </main>
</div>
<?php get_footer(); ?>