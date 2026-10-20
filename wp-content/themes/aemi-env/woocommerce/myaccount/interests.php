<?php

defined('ABSPATH') || exit;

$updatedMsg = '';
if (AemiCustomerInterests::set_aemi_customer_interests(get_current_user_id())) {
//    $updatedMsg = '<div class="woocommerce-message" role="alert" style="margin-bottom: 20px; margin-top: 20px;">'.__('Tus preferencias han sido atualizadas.', AEMI_TEXT_DOMAIN).'</div>';
    $updatedMsg = '<div class="woocommerce-message" role="alert" style="margin-bottom: 20px; margin-top: 20px;">'.__('Tus preferencias han sido actualizadas.', AEMI_TEXT_DOMAIN).'</div>';
}

$interests = get_interest_list_for_users();

$data = get_user_meta(get_current_user_id(), '_current_user_step_insterest', true);

if ($data == false) {
    $data = [];
}

$chunk_array = array_chunk($interests, 6);

?>
<div class="aemi-new-layout-page-interests">
    <div class="interestCard card">
        <div class="card-header">
            <h4 class="card-title">Mis intereses</h4>
        </div>
        <div class="card-content">
            <?php
                if (isset($updatedMsg) && $updatedMsg) {
                    echo $updatedMsg;
                }
?>

            <p class="pageDescription">Especifica las especialidades que te interesan. Esto nos ayudará a personalizar mejor tu experiencia.</p>

            <form method="POST" id="form-customer-interests">
                <?php echo AemiCustomerInterests::get_wp_input_nonce_field(); ?>
                <div class="interestList gap20 flex">
                <?php
                if ($interests) {
                    foreach ($chunk_array as $chunk) {
                        echo '<div class="interestListRow gap10">';
                        foreach ($chunk as $item) { ?>
                                <label class="interestItemTitle"><?php echo $item['title']; ?>
                                    <input type="checkbox" id="<?php echo $item['id']; ?>" name="interests_option[]" value="<?php echo $item['id']; ?>" <?php echo in_array($item['id'], $data) ? 'checked' : ''; ?> />
                                    <span class="checkBox"></span>
                                </label>
                            <?php }
                        echo '</div>';
                    }
                }
?>
                </div>
                
                <div class="Updatebtn">
                    <button>Actualizar intereses</button>
                </div>
            </form>
        </div>
    </div>
</div>