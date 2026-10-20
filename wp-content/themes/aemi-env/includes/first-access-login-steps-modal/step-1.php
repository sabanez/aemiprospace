<h4 class="default-title h5"><?php echo $title; ?></h4>

<div class="aemi-first-access__content-text"><?php echo $description; ?></div>

<p class="pt-4 text-center"><?php echo __('Please select an option:', AEMI_TEXT_DOMAIN); ?></p>

<?php $loopItem = 0;
foreach (AEMIFirstAccessLoginStepsModal::get_interests_type() as $key_interests => $value_interests) {
    ++$loopItem; ?>
  <input class="aemi-first-acess-interest-input aemi-first-acess-interest-input-<?php echo $loopItem; ?>" type="radio" name="first_step_interest" value="<?php echo $key_interests; ?>" id="interest-<?php echo $key_interests; ?>" />
<?php } ?>

<div class="aemi-first-access__buttons_labels">
  <ul>
    <?php $loopItem = 0;
foreach (AEMIFirstAccessLoginStepsModal::get_interests_type() as $key_interests => $value_interests) {
    ++$loopItem; ?>
    <li>
      <label for="interest-<?php echo $key_interests; ?>" class="aemi-first-acess-interest-input-<?php echo $loopItem; ?>">
        <span><?php echo $value_interests; ?></span>
      </label>
    </li>
    <?php } ?>
  </ul>
</div>

<div class="aemi-first-access__next-step">
  <button class="btn btn-primary" style="color: #fff!important;"><?php echo __('Next step', AEMI_TEXT_DOMAIN); ?>
    <svg width="8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" style="position: relative;top: 2px;">
    <!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. -->
    <path style="fill:#fff" d="M64 448c-8.188 0-16.38-3.125-22.62-9.375c-12.5-12.5-12.5-32.75 0-45.25L178.8 256L41.38 118.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0l160 160c12.5 12.5 12.5 32.75 0 45.25l-160 160C80.38 444.9 72.19 448 64 448z"/></svg>
  </button>
</div>