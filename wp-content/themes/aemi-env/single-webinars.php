<?php get_header(); ?>

<div class="container">
    <div class="row">
        <div class="col-md-8">

            <ul class="nav nav-pills mb-3 tabs-single-webinars" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true"><?=esc_html_e('Sobre el Webinar', AEMI_TEXT_DOMAIN)?></button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"><?=esc_html_e('Recursos', AEMI_TEXT_DOMAIN)?></button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false"><?=esc_html_e('Instructor', AEMI_TEXT_DOMAIN)?></button>
                </li>
            </ul>

            <div class="tab-content tab-content-webinars" id="pills-tabContent">
                <div class="tab-pane fade show active about-webinar" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                    <p class="fw-bold text-dark"><?=esc_html_e('Información sobre el webinar', AEMI_TEXT_DOMAIN)?></p>
                    <?php the_content() ?>
                </div>

                <div class="tab-pane fade resources-webinar" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                    <?php
                        $recursos = get_field('repeater_recursos');

                        echo '<ul>';
                            if (is_countable($recursos) && count($recursos) > 0):
                                foreach ($recursos as $recurso):

                                    get_template_part('template-parts/content', 'resources-webinars', $recurso);

                                endforeach;
                            endif;
                        echo '</ul>';
                    ?>
                </div>

                <div class="tab-pane fade instructors-webinar" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                    <?php
                    $instructors = get_field('repeater_instructors');

                    if (is_countable($instructors) && count($instructors) > 0):
                        foreach ($instructors as $instructor):

                            get_template_part('template-parts/content', 'instructors-webinars', $instructor);

                        endforeach;
                    endif;
                    ?>
                </div>
            </div>

        </div>

        <div class="col-md-4">
            <?php get_sidebar('single-webinar') ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
