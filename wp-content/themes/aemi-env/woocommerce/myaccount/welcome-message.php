<?php defined('ABSPATH') || exit; ?>
<div class="welcome-card-customize">
    <div class="pageSpacer">
        <div class="welcomeCard card">
            <div class="card-content">
                <img src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/images/welcome.svg'); ?>">
                <h2>Bienvenido a AEMI</h2>
<!--                <p style="width: 90%;margin: 10px auto;">Este es su panel de control donde podrá ver información sobre su perfil, pagos, puntajes y resultados. seminarios web disponibles. Para comenzar, vayamos a la página de mis puntajes donde encontrará la información sobre los puntos obtenidos.</p>-->
                <p style="width: 90%;margin: 10px auto;">Este es tu panel de control, donde podrás ver información sobre tu perfil, intereses, pagos, competencias adquiridas. Para comenzar, vayamos a la página de competencias donde encontrarás información sobre los puntos obtenidos.</p>
                <a href="<?php echo get_aemi_my_account_url(); ?>?welcome-my-account=true" class="btn-primary" style="display: inline-flex; align-items: center;">Ir a Mis Puntuaciones</a>
            </div>
        </div>
    </div>
</div>