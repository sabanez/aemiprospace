<?php
/**
 * Template Name: Recomendaciones.
 */
$limitPerPage = 36;
get_header(); ?>
<?php get_header();
$args_news = [
    'tax_query' => aemiRegisterCustomTags::returnCustomizedArgsToQuery(),
    'posts_per_page' => $limitPerPage,
    'order' => 'DESC',
    'paged' => (get_query_var('paged')) ? get_query_var('paged') : 1,
];

if (isset($args_news['tax_query'][0]['terms'])) {
    // remove os conteudo basicos em comuns
    $chave = array_search(103, $args_news['tax_query'][0]['terms']);

    // Verificar se o valor 103 foi encontrado
    if ($chave !== false) {
        // Remover o item com o valor 103 do array
        unset($args_news['tax_query'][0]['terms'][$chave]);
    }
}

if (aemi_current_user_is_professional()) {
    $args_news['meta_key'] = '_content_for_subscribers';
    // $args_news['orderby'] = 'meta_value';
    // $args_news['order'] = 'DESC';
    $args_news['orderby'] = array(
        'meta_value' => 'DESC', 
        'date' => 'DESC' 
    );
}

$argsTax = AemiFilterPostsBasedOnUserLevel::getQueryTaxArgsShowOnlyUserLevelRelatedContent();

if (isset($args_news['tax_query']) && $args_news['tax_query'] && $argsTax) {
    $args_news['tax_query'][] = $argsTax;
}

$query_news_tocount = new WP_Query($args_news);
$posts_max_num_pages = $query_news_tocount->max_num_pages;

// echo '<pre>';
// print_r( $args_news );
// echo '</pre>';
$query_news = get_posts($args_news);

// contenidos basicos em comum
// $args_news['posts_per_page'] = 500;
// $args_news['tax_query'][0]['terms'] = [103];
// $args_news['paged'] = 1;

// $query_news_103 = get_posts($args_news);
// $query_news_tocount_103 = new WP_Query($args_news);
// $posts_103_max_num_pages = $query_news_tocount->max_num_pages;

// if (count($query_news) < $limitPerPage) {
//     $query_news_resultado_final = array_merge($query_news, $query_news_103);
//     $query_news = $query_news_resultado_final;
// }

?>
<div class="aemi-new-layout-page-favourites-page">
  <main>
    <section class="favouritePage container" id="contact">
      <h2 class="pageTitle">Recomendaciones</h2>

      
        <?php if ($query_news) {
            echo '<div class="resultsHolder">';
            foreach ($query_news as $post) {
                $thumbnail = get_the_post_thumbnail($post->ID, 'wikimi-thumbnail', ['class' => 'card-image-full']);
                $thumbnail = $thumbnail ? $thumbnail : '<img width="305" height="205" src="'.get_template_directory_uri().'/assets/images/box-posts-default.jpg" class="attachment-wikimi-thumbnail size-wikimi-thumbnail wp-post-image card-image-full" />';
                $content_type = get_aemi_content_types_post($post->ID, 'content_type', false);
                $post_button = favorite_button($post->ID);
                $aemi_tags_list = '';
        
                if( isset( $_GET['view_aemi_tags'] ) && $_GET['view_aemi_tags'] == '4a57sd40a8q0q4f046ad468sd4as8dq68w7gwekluqklwda'){
                  $aemi_tags = get_the_terms( $post->ID, 'aemi_tags' );
          
                  if( $aemi_tags ){
                      $aemi_tags = array_column( $aemi_tags, 'name' );
          
                      if( $aemi_tags ){
                          $aemi_tags_list = implode(',', $aemi_tags);
                      }
                  }
                }
                ?>
          <div class="card">
            <?= $aemi_tags_list ?>
            <div class="card-image-holder">
              <a href="<?php echo get_permalink($post->ID); ?>"><?php echo $thumbnail; ?></a>

              <div class="icons">
                <button type="button" class="heartIcon <?php echo $post_button['class']; ?>" data-id="<?php echo $post_button['data-id']; ?>">
                  <svg width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="mdi:heart-outline">
                      <path id="Vector"
                        d="M14.6207 22.4146L14.4998 22.5354L14.3669 22.4146C8.62734 17.2067 4.83317 13.7629 4.83317 10.2708C4.83317 7.85417 6.64567 6.04167 9.06234 6.04167C10.9232 6.04167 12.7357 7.25 13.3761 8.89333H15.6236C16.264 7.25 18.0765 6.04167 19.9373 6.04167C22.354 6.04167 24.1665 7.85417 24.1665 10.2708C24.1665 13.7629 20.3723 17.2067 14.6207 22.4146ZM19.9373 3.625C17.8348 3.625 15.8169 4.60375 14.4998 6.13833C13.1828 4.60375 11.1648 3.625 9.06234 3.625C5.34067 3.625 2.4165 6.53708 2.4165 10.2708C2.4165 14.8262 6.52484 18.56 12.7478 24.2029L14.4998 25.7979L16.2519 24.2029C22.4748 18.56 26.5832 14.8262 26.5832 10.2708C26.5832 6.53708 23.659 3.625 19.9373 3.625Z"
                        fill="#FF3366"></path>
                    </g>
                  </svg>
                </button>

                <?php
                // echo '<div>';
                // echo '<div style="background:#fff; padding: 10px;">' . get_aemi_content_types_post($post->ID, 'aemi_tags', false) . '</div>';
                // echo '<div style="background:#fff; padding: 10px;">' . $post->post_date . '</div>';
                // echo '<div style="background:#fff; padding: 10px;">' . ( get_post_meta( $post->ID, '_content_for_subscribers', true ) ? 'PRO' : 'FREE' ) . '</div>';
                // echo '</div>';

                ?>
                <div class="webniar">
                    <?php if ($content_type === 'Webinar') {
                        echo '<svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="solar:play-bold" opacity="0.8"><path id="Vector" d="M15.1649 6.62461C15.5051 6.80552 15.7897 7.0756 15.9881 7.40589C16.1865 7.73618 16.2914 8.11424 16.2914 8.49957C16.2914 8.88489 16.1865 9.26295 15.9881 9.59324C15.7897 9.92353 15.5051 10.1936 15.1649 10.3745L6.0897 15.3095C4.62841 16.1042 2.8335 15.0701 2.8335 13.4352V3.56461C2.8335 1.92836 4.62841 0.8949 6.0897 1.68894L15.1649 6.62461Z" fill="var(--color-purple)" /></g></svg>';
                    }
                ?>
                  <p><a href="<?php echo get_permalink($post->ID); ?>"><?php echo sanitize_text_field($content_type); ?></a></p>
                </div>
              </div>
            </div>

            <div class="card-content">
              <a href="<?php echo get_permalink($post->ID); ?>" class="card-title"><?php echo sanitize_text_field($post->post_title); ?></a>
            </div>
          </div>
        <?php }

            echo '</div>';
            $total_pages = $posts_max_num_pages;
            $pagination_links = aemi_pagination([
                'current' => $args_news['paged'],
                'total' => $total_pages,
            ]);

            if ($pagination_links) {
                echo '<div class="searchResultsPage" style="width: 100%;">'.strtr($pagination_links, ['class="paginationHolder"' => 'class="paginationHolder" style="margin-top: 0;"']).'</div>';
            }
            wp_reset_postdata();
        } else {
            ?>
                    <div class="page-post__no-content">
                    <h2><?php echo __('No content at the moment.', AEMI_TEXT_DOMAIN); ?></h2>
                    </div>
                    <?php
        } ?>
    </section>
  </main>
</div>
<?php get_footer(); ?>