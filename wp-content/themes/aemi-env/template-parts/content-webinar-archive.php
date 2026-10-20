<article class="mb-4 pb-2 <?=$args?>">
    <div class="post-webinar ">
        <header>
            <a href="<?php the_permalink() ?>">
                <div class="thumbnail-webinars" style="background: url(<?=get_the_post_thumbnail_url(get_the_ID(), 'medium')?>);"></div>
            </a>
        </header>

        <footer class="box-aemi-category-wikimi__inner">
            <p class="date"><i class="fa-solid fa-calendar-days"></i> <?= get_the_date('d/m/Y') ?></p>

            <a href="<?php the_permalink() ?>">
                <h3 class="title-post-webinars">
                    <?= get_the_title() ?>
                </h3>
            </a>

            <p class="box-aemi-category-wikimi__description description-post-webinars"><?= esc_attr(get_the_excerpt()) ?></p>

            <div class="wrapper-btn-post-webinars">
                <a href="<?php the_permalink() ?>">
                    <span class="btn btn-secondary"><i class="fa-solid fa-plus"></i> <?= __('Inscribirse', AEMI_TEXT_DOMAIN) ?></span>
                </a>
            </div>
        </footer>
    </div>
</article>

