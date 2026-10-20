<?php

$defaultInterestsTitle = ($stepModalDefaultOptions && isset($stepModalDefaultOptions['interests_title']) && $stepModalDefaultOptions['interests_title'] ? esc_attr($stepModalDefaultOptions['interests_title']) : '');
$interestsTitle = ($stepModal && isset($stepModal['interests_title']) && $stepModal['interests_title'] ? esc_attr($stepModal['interests_title']) : $defaultInterestsTitle);

?>
<h4 class="default-title h5"><?php echo $title; ?></h4>

<div class="aemi-first-access__content-text mb-4"><?php echo $description; ?></div>

<p class="default-title h5 text-start default-title-border-bottom w-100"><span class="text-primary"><?php echo $interestsTitle; ?></span></p>

<div class="interestList gap20 flex">
<?php
$categories_interest = get_interest_list_for_users();

$chunk_array = array_chunk($categories_interest, 9);
foreach ($chunk_array as $chunk) {
    echo '<div class="interestListRow gap10">';
    foreach ($chunk as $interest) {
        ?>
        <label class="interestItemTitle"><?php echo $interest['title']; ?>
            <input type="checkbox" id="<?php echo $interest['id']; ?>" name="interest_user[]" value="<?php echo $interest['id']; ?>" />
            <span class="checkBox"></span>
        </label>
    <?php
    }
    echo '</div>';
}
?>
</div>
<div class="aemi-first-access__next-step">
  <button class="btn btn-secondary" style="color: #fff!important;"><?php echo __('Save', AEMI_TEXT_DOMAIN); ?> 
    <svg width="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" style="position: relative;top: 2px;">
    <!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. -->
    <path style="fill:#fff" d="M64 448c-8.188 0-16.38-3.125-22.62-9.375c-12.5-12.5-12.5-32.75 0-45.25L178.8 256L41.38 118.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0l160 160c12.5 12.5 12.5 32.75 0 45.25l-160 160C80.38 444.9 72.19 448 64 448z"/></svg>
  </button>
</div>