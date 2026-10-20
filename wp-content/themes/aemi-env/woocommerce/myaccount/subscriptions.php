<?php

defined('ABSPATH') || exit;

$data = get_user_meta(get_current_user_id(), 'customer_subscriptions');
$data = unserialize($data[0]);

if ($data == false) {
    $data = [[]];
}

?>
<div class="aemi-new-layout-page-user-subscription">
    <div class="subscriptionPage">
        <div class="subscriptionCard card">
            <div class="card-header">
                <h3 class="card-title">Mis envíos</h3>
            </div>

            <div class="card-content">

                <?php
                    if (isset($_GET['success']) && $_GET['success']) {
//                        echo '<div class="woocommerce-message" role="alert" style="margin-bottom: 20px; margin-top: 20px;">'.__('Tus preferencias han sido atualizadas.', AEMI_TEXT_DOMAIN).'</div>';
                        echo '<div class="woocommerce-message" role="alert" style="margin-bottom: 20px; margin-top: 20px;">'.__('Tus preferencias han sido actualizadas.', AEMI_TEXT_DOMAIN).'</div>';
                    }
?>
                
                <div class="actionList">
                    <form method="POST" id="form-curstomer-subscriptions">
                        <ul>
                        
                            <?php foreach (aemi_subscriptions_options() as $key => $value) { ?>
                                <li class="singleActionList">
                                    <div class="leftAction">
                                        <h5><?php echo $value['name']; ?></h5>
                                        <p>
                                        <?php
                                            switch ($value['id']) {
                                                case 'newsletter_mensual':
                                                    echo 'A través de esta newsletter recibirás artículos y casos clínicos en los que diferentes profesionales comparten su experiencia en microinmunoterapia. También recibirás noticias sobre la asociación y la microinmunoterapia, y otros materiales formativos.';
                                                    break;

                                                case 'newsletter_formaciones':
                                                    echo 'A través de estos recordatorios recibirás avisos en tu correo de las próximas actividades formativas de AEMI: webinars, MasterClass, formaciones flash, eventos, etc.';
                                                    break;

                                                case 'recordation_helpmi':
                                                    echo '¿Le gustaría recibir nuestro boletín mensual directamente en su correo registrado? Enviamos principalmente datos para...';
                                                    break;

                                                case 'newsletter_icomi':
                                                    echo 'En este envío especial se comparten todas las informaciones relacionadas con la próxima edición de ICoMI.';
                                                    break;
                                            }

                                ?>
                                        </p>
                                    </div>

                                    <div class="rightAction">
                                        <label class="action" for="<?php echo $value['id']; ?>_yes">SÍ
                                            <input type="radio" id="<?php echo $value['id']; ?>_yes" name="<?php echo $value['id']; ?>" value="<?php echo $value['id']; ?>" <?php echo in_array($value['id'], $data[0]) ? 'checked' : ''; ?>>
                                            <span class="checkmarkk"></span>
                                        </label>

                                        <label class="action" for="<?php echo $value['id']; ?>_no">No
                                            <input type="radio" id="<?php echo $value['id']; ?>_no" name="<?php echo $value['id']; ?>" value="no" <?php echo !in_array($value['id'], $data[0]) ? 'checked' : ''; ?>>
                                            <span class="checkmarkk"></span>
                                        </label>
                                    </div>
                                </li>
                                <?php } ?>
                        </ul>

                                <div class="updateSub">
                                    <button type="submit" id="button11" class="btn-primary" name="save_account_details" id="subutton" disabled="disabled">Actualizar envíos</button>
                                </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
