<?php get_header(); ?>
<div class="aemi-generic-page-container">
  <div class="aemi-generic-page-container--container">
    <div class="aemi-title-breadcrumb-pages">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <?php echo get_aemi_title_page() ? '<h1 class="aemi-title-breadcrumb-pages__title default-title">'.get_aemi_title_page().'</h1>' : ''; ?>
            <?php
              if (function_exists('yoast_breadcrumb')) {
                  yoast_breadcrumb('<p id="breadcrumbs">', '</p>');
              }
?>
          </div>
        </div>
      </div>
    </div>
    <main class="page-general-container">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <?php
  if (have_posts()) {
      while (have_posts()) {
          the_post();

          the_content();
      }
  }
?>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>
<?php get_footer(); ?>