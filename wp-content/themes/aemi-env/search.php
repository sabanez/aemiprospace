<?php get_header();

$consulta_personalizada = new WP_Query([
  'post_type' => 'post', // Substitua 'post' pelo tipo de post que você está consultando
  's' => esc_html(get_search_query()),
]);

$total_de_posts = $consulta_personalizada->found_posts;
?>
<div class="aemi-new-layout-page-search-page">
  <main>
    <section class="searchResultsPage container" id="searchPage">
      <h2 class="pageTitle">Resultados de la búsqueda</h2>

      <div class="flex searchBarHolder">
        <p class="pageDescription">
          Consulta los resultados de:
          <span class="resultsFound">(<?php echo $total_de_posts; ?> Resultados encontrados)</span>
        </p>

        <form class="searchBar" action="<?php echo home_url(); ?>">
          <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g id="tabler:search" opacity="0.2">
              <path id="Vector"
                d="M23.625 23.625L16.875 16.875M3.375 11.25C3.375 12.2842 3.57869 13.3082 3.97445 14.2636C4.3702 15.2191 4.95027 16.0872 5.68153 16.8185C6.4128 17.5497 7.28093 18.1298 8.23637 18.5256C9.19181 18.9213 10.2158 19.125 11.25 19.125C12.2842 19.125 13.3082 18.9213 14.2636 18.5256C15.2191 18.1298 16.0872 17.5497 16.8185 16.8185C17.5497 16.0872 18.1298 15.2191 18.5256 14.2636C18.9213 13.3082 19.125 12.2842 19.125 11.25C19.125 10.2158 18.9213 9.19181 18.5256 8.23637C18.1298 7.28093 17.5497 6.4128 16.8185 5.68153C16.0872 4.95027 15.2191 4.3702 14.2636 3.97445C13.3082 3.57869 12.2842 3.375 11.25 3.375C10.2158 3.375 9.19181 3.57869 8.23637 3.97445C7.28093 4.3702 6.4128 4.95027 5.68153 5.68153C4.95027 6.4128 4.3702 7.28093 3.97445 8.23637C3.57869 9.19181 3.375 10.2158 3.375 11.25Z"
                stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </g>
          </svg>

          <input type="text" name="s" placeholder="Buscar ..." value="<?php echo esc_html(get_search_query()); ?>">
        </form>
      </div>

      <?php if (have_posts()) { ?>

        <div class="resultsHolder">
          <?php while (have_posts()) { ?>
            <?php
              the_post();
              $thumbnail = get_the_post_thumbnail($post->ID, 'wikimi-thumbnail', ['class' => 'card-image-full']);
              $thumbnail = $thumbnail ? $thumbnail : '<img width="305" height="205" src="'.get_template_directory_uri().'/assets/images/box-posts-default.jpg" class="attachment-wikimi-thumbnail size-wikimi-thumbnail wp-post-image card-image-full" />';
              $content_type = get_aemi_content_types_post($post->ID, 'content_type', false);
              $favorite_button = favorite_button($post->ID);
              ?>
              <div class="card">
                <div class="card-image-holder">
                  <a href="<?php echo get_permalink($post->ID); ?>"><?php echo $thumbnail; ?></a>

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
                      <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g id="solar:play-bold" opacity="0.8">
                          <path id="Vector"
                            d="M15.1649 6.62461C15.5051 6.80552 15.7897 7.0756 15.9881 7.40589C16.1865 7.73618 16.2914 8.11424 16.2914 8.49957C16.2914 8.88489 16.1865 9.26295 15.9881 9.59324C15.7897 9.92353 15.5051 10.1936 15.1649 10.3745L6.0897 15.3095C4.62841 16.1042 2.8335 15.0701 2.8335 13.4352V3.56461C2.8335 1.92836 4.62841 0.8949 6.0897 1.68894L15.1649 6.62461Z"
                            fill="var(--color-purple)"></path>
                        </g>
                      </svg>
                      <p><a href="<?php echo get_permalink($post->ID); ?>"><?php echo sanitize_text_field($content_type); ?></a></p>
                    </div>
                  </div>
                </div>

                <div class="card-content">
                  <a href="<?php echo get_permalink($post->ID); ?>" class="card-title"><?php echo sanitize_text_field($post->post_title); ?></a>
                  <a href="<?php echo get_permalink($post->ID); ?>" class="card-para-info"><?php echo get_aemi_text_excerpt($post, 350); ?></a>
                </div>
              </div>
          <?php } ?>

        </div>
        <?php echo aemi_pagination(); ?>
      <?php } else { ?>
        <div>
          <h2>Ningún resultado encontrado.</h2>
        </div>
      <?php } ?>
    </section>
  </main>
</div>
<?php get_footer(); ?>