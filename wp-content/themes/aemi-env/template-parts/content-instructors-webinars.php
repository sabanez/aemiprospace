<div class="instructor">
    <div class="row">
        <div class="col-lg-2">
            <div class="photo-instructor" style="background: url('<?= $args['photo_instructor']['sizes']['large'] ?>')"></div>
        </div>

        <div class="col-lg-10 ps-md-3">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="name-instructor"><?= $args['name_instructor'] ?></h2>
                    <span class="occupation-instructor"><?= $args['occupation_instructor'] ?></span>
                </div>

                <div class="col-md-6 text-end">
                    <a class="know-more-instructor" href="<?= $args['know_more_instructor'] ?>"> <?= __('Saber mais', AEMI_TEXT_DOMAIN) ?> </a>
                </div>
            </div>

            <p class="about-instructor"><?= $args['about_instructor'] ?></p>
        </div>
    </div>
</div>