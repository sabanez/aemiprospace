<?php
$aemiFirstAccessLoginStepsModal = AEMIFirstAccessLoginStepsModal::get_options();
$stepArgs = ($aemiFirstAccessLoginStepsModal[$args['aemiStep']] ? $aemiFirstAccessLoginStepsModal[$args['aemiStep']] : []);
$currentStep = $args['aemiStep'];
$nonce = $args['nonce'];

$stepModal = get_aemi_array_value_by_user_locale($stepArgs);
$stepModalDefaultOptions = get_aemi_array_value_by_user_locale($stepArgs, get_aemi_default_locale());

$defaultTitle = ($stepModalDefaultOptions && isset($stepModalDefaultOptions['title']) && $stepModalDefaultOptions['title'] ? esc_attr($stepModalDefaultOptions['title']) : '');
$defaultDescription = ($stepModalDefaultOptions && isset($stepModalDefaultOptions['description']) && $stepModalDefaultOptions['description'] ? nl2br($stepModalDefaultOptions['description']) : '');
$defaultName = ($stepModalDefaultOptions && isset($stepModalDefaultOptions['name']) && $stepModalDefaultOptions['name'] ? $stepModalDefaultOptions['name'] : '');
$defaultSubtitle = ($stepModalDefaultOptions && isset($stepModalDefaultOptions['subtitle']) && $stepModalDefaultOptions['subtitle'] ? nl2br($stepModalDefaultOptions['subtitle']) : __('Equipo AEMI', AEMI_TEXT_DOMAIN));
$defaultThumbnail_id = ($stepModalDefaultOptions && isset($stepModalDefaultOptions['thumbnail_id']) && $stepModalDefaultOptions['thumbnail_id'] ? $stepModalDefaultOptions['thumbnail_id'] : '');

$title = ($stepModal && isset($stepModal['title']) && $stepModal['title'] ? esc_attr($stepModal['title']) : $defaultTitle);
$description = ($stepModal && isset($stepModal['description']) && $stepModal['description'] ? nl2br($stepModal['description']) : $defaultDescription);
$name = ($stepModal && isset($stepModal['name']) && $stepModal['name'] ? nl2br($stepModal['name']) : $defaultName);
$subtitle = ($stepModal && isset($stepModal['subtitle']) && $stepModal['subtitle'] ? nl2br($stepModal['subtitle']) : $defaultSubtitle);
$thumbnail_id = ($stepModal && isset($stepModal['thumbnail_id']) && $stepModal['thumbnail_id'] ? $stepModal['thumbnail_id'] : $defaultThumbnail_id);

$title = do_shortcode($title);
$description = do_shortcode($description);
$name = do_shortcode($name);
$subtitle = do_shortcode($subtitle);
$urlThumbnail = '';

if ($thumbnail_id) {
    $get_attachment_image = wp_get_attachment_image_url($thumbnail_id, 'full');

    if ($get_attachment_image) {
        $urlThumbnail = $get_attachment_image;
    }
}

?><div class="aemi-first-access-login-steps-modal">
  <div class="aemi-first-access__container">
    <div class="container">
      <div class="aemi-first-access__box">
        
        <div class="aemi-first-access__progress-bar-container">
          <div class="<?php echo $currentStep >= 1 ? 'aemi-first-access__progress-bar--active' : ''; ?>"></div>
          <div class="<?php echo $currentStep >= 2 ? 'aemi-first-access__progress-bar--active' : ''; ?>"></div>
<!--          <div class="--><?php // echo ( $currentStep >= 3 ? 'aemi-first-access__progress-bar--active' : '' );?><!--"></div>-->
        </div>

        <?php if ($currentStep <= 3) { ?>
        <div class="aemi-first-access__professional">
          <div>
            <div class="aemi-first-access__professional-thumbnail" style="background-image: url(<?php echo $urlThumbnail; ?>);"></div>
            <p class="aemi-first-access__professional-name"><?php echo $name; ?></p>
            <p class="aemi-first-access__professional-description"><?php echo $subtitle; ?></p>
          </div>
        </div>
        <?php } ?>
        <div class="aemi-first-access__content w-100" <?php echo $currentStep === '4' ? 'style="width: 100%;"' : ''; ?>>
          <form action="<?php echo home_url(); ?>/" method="POST" class="w-100">
            <?php echo $nonce; ?>
            <input type="hidden" value="<?php echo $currentStep; ?>" name="aemi-form-steps" />

            <?php require_once 'step-'.$currentStep.'.php'; ?>

              <?php if ($currentStep >= 1 && $currentStep <= 2) {
                  echo '<p class="text-center pt-4">';
                  echo __('Step', AEMI_TEXT_DOMAIN).' ';
                  echo $args['aemiStep'];
                  echo '/2';
                  echo '</p>';
              }
?>
            <p class="text-center pt-4">

            </p>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>