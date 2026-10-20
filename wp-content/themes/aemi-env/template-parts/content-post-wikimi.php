<?php
$category_archive = sanitize_title(get_the_archive_title());
$categories = get_the_category();

$meta = get_user_meta(get_current_user_id(), '_aemi_favorite_pages');

if (count($meta) > 0) {
    $liked = in_array(get_the_ID(), $meta[0]) ? 'fa-solid fa-heart text-dark' : '';
} else {
    $liked = '';
}

?>

<article class="post-wikimi mb-5 pb-4 pb-md-0 mb-md-4 <?= count($args) > 0 ? $args : '' ?>">
    <div class="box-aemi-category-wikimi post-pro">
        <?php if ( aemi_get_post_view_user(get_the_ID()) ): ?>
        <div class="viewer">
            <?php echo esc_html_e('visualizado', AEMI_TEXT_DOMAIN) ?>
        </div>
        <?php endif; ?>

        <header>
            <a href="<?php the_permalink() ?>">
                <div class="thumbnail-box-eami-category-wikimi" style="background: url(<?=get_the_post_thumbnail_url(get_the_ID(), 'medium')?>);"></div>
            </a>

            <div class="row row-bottom-header-post-wikimi">
                <div class="col-6 col-md-6">
                    <?php if (get_post_format() != false): ?>
                        <div class="post-format-wikimi">
                            <?php
                                switch (get_post_format()):
                                    case 'video':
                                        echo '<i class="fa-solid fa-play"></i>';
                                        break;
                                    case 'aside':
                                        echo '<i class="fa-regular fa-file-lines"></i>';
                                        break;
                                endswitch;
                            ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-6 col-md-6">
                    <?php
                    $level = get_the_terms(get_the_ID(), 'content_level');
                    if ($level != false): ?>
                        <div class="content-level-post-wikimi">
                            <?=$level[0]->name?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <footer class="box-aemi-category-wikimi__inner">
            <span class="box-aemi-category">
                <?php
                if (is_countable($categories) && count($categories) > 0) {
                    foreach ($categories as $category) {
                        if ($category->slug == $category_archive) {
                            echo $category->name;
                        }
                    }
                }
                ?>
            </span>

            <a href="<?php the_permalink() ?>">
                <h3 class="box-aemi-category-wikimi__title">
                    <?= get_the_title() ?>
                </h3>
            </a>

            <p class="box-aemi-category-wikimi__description"><?= esc_attr(get_the_excerpt()) ?></p>

            <div class="row-bottom-footer-post-wikimi">
                <a href="<?php the_permalink() ?>">
                    <span class="btn btn-secondary btn-ready-post"><?= __('ver publicación', AEMI_TEXT_DOMAIN) ?></span>
                </a>

                <i data-id="<?=get_the_ID()?>" class="fa-regular fa-heart <?=$liked?> like-action"></i>
            </div>
        </footer>
    </div>
</article>

