<?php get_header(); ?>

    <div class="archive-webinars container pt-5">
        <div class="row">
            <div class="col-lg-12 pb-4">
                <?php the_archive_title('<h1 class="default-title">', '</h1>'); ?>

                <nav aria-label="breadcrumb" class="breadcrumb-archive">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo home_url(); ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo home_url('wikimi'); ?>">WikiMi</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php the_archive_title(); ?></li>
                    </ol>
                </nav>
            </div>

            <?php if (isset($_GET['web']) && $_GET['web'] != '') { ?>
            <div class="col-lg-12 mb-5">
                <h4>Exhibiendo resultados para: <b class="text-primary"><?php echo $_GET['web']; ?></b></h4>
            </div>
            <?php } ?>

            <div class="col-md-3">
                <?php get_sidebar('archive-webinars'); ?>
            </div>

            <div class="col-md-9">
                <div class="row ps-md-3">
                    <?php
                        $args = [
                           'post_type' => 'webinars',
                            's' => isset($_GET['web']) ? $_GET['web'] : '',
                        ];

$query = new WP_Query($args);

if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();

        get_template_part('template-parts/content', 'webinar-archive', 'col-lg-4');
    }

    wp_reset_postdata();

    $paged = get_query_var('paged');

    echo '<div class="pagination-webinars mt-5 col-lg-12">';
    echo paginate_links([
        'base' => home_url('/webinars/page/%#%'),
        'format' => '<span></span>',
        'current' => $paged != 0 ? $paged : 1,
        'total' => $query->max_num_pages,
        'prev_text' => '<span class="m-left">&laquo;</span>',
        'next_text' => '<span class="m-right">&raquo;</span>',
        'prev_next' => true,
    ]);
    echo '</div>';
} else {
    echo '<div class="text-center alert alert-warning">Ops! Não foi encontrando nenhum resultado pra sua busca!</div>';
}
?>
                </div>
            </div>
        </div>
    </div>

<?php get_footer(); ?>
