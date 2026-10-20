<?php
$category = $args['category'];
$params   = $args['params'];
?>

<?php
    if ( $category ):
        $category_thumbnail_id = get_term_meta($category->term_id, '_category_thumbnail_id', true);
        $url = $category_thumbnail_id ? wp_get_attachment_url($category_thumbnail_id) : '';

        if ( isset($params['container_class']) ): ?>

        <article class="<?= esc_attr($params['container_class']) ?>> mb-4">
            <div class="box-aemi-category-wikimi">
                <header>
                    <div class="thumbnail-box-eami-category-wikimi" style="background: url(<?=$url?>);"></div>
                </header>

                <footer class="box-aemi-category-wikimi__inner">
                    <h3 class="box-aemi-category-wikimi__title"><?= esc_attr($category->name) ?></h3>

                    <?php if ($category->description): ?>
                    <p class="box-aemi-category-wikimi__description"><?= esc_attr($category->description) ?></p>
                    <?php endif ?>

                    <span class="btn btn-secondary"><?= __('See publications', AEMI_TEXT_DOMAIN) ?></span>
                </footer>

                <a href="<?= get_category_link($category->term_id) ?>" class="box-aemi-category-wikimi__link"></a>';
            </div>
        </article>

<?php   endif;
    endif;
?>