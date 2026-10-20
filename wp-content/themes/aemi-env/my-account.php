<?php if (is_user_logged_in()) {
    get_header();
    ?>
    <?php
    if (have_posts()) {
        while (have_posts()) {
            the_post();

            the_content();
        }
    }
    ?>
<?php
    get_footer('clean');
} else {
    get_header('clean'); ?>
<div class="login-page-general-wrapper">
  <?php
        if (have_posts()) {
            while (have_posts()) {
                the_post();

                the_content();
            }
        }
    ?>
</div>
<?php
  get_footer('clean');
}
?>