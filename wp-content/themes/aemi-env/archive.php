<?php get_header(); ?>

    <div class="container mt-5 archive-posts-wikimi">
        <div class="row">
            <div class="col-lg-12">
                <?php the_archive_title('<h1 class="default-title">', '</h1>'); ?>

                <nav aria-label="breadcrumb" class="breadcrumb-archive">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?=home_url()?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?=home_url('wikimi')?>">WikiMi</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php the_archive_title()?></li>
                    </ol>
                </nav>
            </div>

            <div class="col-lg-12 mt-5">
                <div class="row">
                    <?php
                        if (have_posts()):
                            while (have_posts()):the_post();
                                get_template_part('template-parts/content', 'post-wikimi', 'col-md-6 col-lg-4 col-xl-3');
                            endwhile;
                        endif;
                    ?>

                    <div class="col-lg-12 mt-4">
                        <?php
                            echo get_the_posts_pagination(array(
                                'class' => 'aemi-pagination',
                                'prev_next' => false
                            ))
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php get_footer(); ?>