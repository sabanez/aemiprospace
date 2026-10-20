<?php

use Envixo\AemiGotoWebinar\Helpers\GotoHelpers;
use Envixo\AemiGotoWebinar\Helpers\GotoUserWordPress;
use Envixo\AemiGotoWebinar\Helpers\GotoWebinarClass;

/**
 * Template Name: Agenda.
 */
get_header(); ?>

<?php
$argsWebinars = [];

if (is_user_logged_in()) {
    //$postRegistered = GotoUserWordPress::getUserRegisteredWebinarsPostID(get_current_user_id());

   // if ($postRegistered) {
     //   $argsWebinars['exclude'] = $postRegistered;
   // }
}

$webinars = GotoHelpers::getAtiveWebinarsInWordPress($argsWebinars);

$getLasContentAccess = false;

if (is_user_logged_in() && get_current_user_id()) {
    $last_content_accessed = get_user_meta(get_current_user_id(), '_last_content_accessed', true);

    if ($last_content_accessed) {
        $getContentLastAccess = get_post($last_content_accessed);

        if ($getContentLastAccess) {
            $contains_video_youtube_or_vimeo = preg_match('/https?:\/\/(www\.)?(youtube\.com\/watch\?v=[A-Za-z0-9_\-]+|vimeo\.com\/[0-9]+)/', $getContentLastAccess->post_content) ?: false;

            $getLasContentAccess = [
                'title'          => sanitize_text_field($getContentLastAccess->post_title),
                'link'           => get_permalink($getContentLastAccess->ID),
                'contains_video' => $contains_video_youtube_or_vimeo,
                'image_url'      => get_the_post_thumbnail_url($getContentLastAccess->ID, 'full'),
            ];
        }
    }
} ?>

<div class="layout-page-agenda">
    <main>
        <section class="agendaPage mainSection">
            <div class="sectionHeader">
                <h2>Agenda</h2>
            </div>
            <div class="sectionContainer">
                <div class="AgendaNewsCardPage card" <?php echo !$getLasContentAccess ? 'style="margin: 0 auto;"' : ''; ?>>
                    <?php if ($webinars) { ?><?php foreach ($webinars as $webinar) {
                        $webinar = new GotoWebinarClass($webinar, []); ?>
                        <div class="newList">
    <div class="newListTittle">
        <div class="newListTittleHeading">
            <h3><?php echo date('d', strtotime($webinar->getPostStartTime())); ?></h3>
            <p><?php echo strtoupper(date('M', strtotime($webinar->getPostStartTime()))); ?></p>
            <p><?php echo strtoupper(date('H:i', strtotime($webinar->getPostStartTime()))); ?></p>
        </div>
        <div class="newDistribution closed">
            <h4><?php echo sanitize_text_field($webinar->getPost()->post_title); ?></h4>
            <p><?php echo wpautop(wp_kses_post($webinar->getPost()->post_content)); ?></p>
        </div>
    </div>
    <div>
        <button class="NewsMoreInfo">Más info</button>

        <?php if ($webinar->userHasBeenSubscribedToWebinar(get_current_user_id())): ?>
            <div class="NewsButton registered">
                <a href="#" class="">Registrado</a>
            </div>

        <?php elseif ($webinar->webinarHasEnded()): ?>
            <div class="NewsButton ended">
                <a href="#" class="">Finalizado</a>
            </div>

        <?php else: ?>
            <div class="NewsButton">
                <?php
                // Obtener el nivel de membresía del usuario logueado
                $membership_level = SwpmMemberUtils::get_logged_in_members_level();

                // Obtener el valor de 'url_del_producto_en_stripe'
                $url_stripe = get_post_meta($webinar->getPost()->ID, 'url_del_producto_en_stripe', true); 
                $user_email = wp_get_current_user()->user_email; // Obtener el email del usuario
                $webinar_post_id = $webinar->getPost()->ID; // Obtener el ID del post (webinar)

                // Comprobar el nivel de membresía
                if ($membership_level == 2): 
                    // Si hay URL de Stripe, añadir el parámetro prefilled_email
                    if ($url_stripe): ?>
                        <a href="<?php echo esc_url($url_stripe . '?prefilled_email=' . urlencode($user_email) . '&metadata[webinar_post_id]=' . urlencode($webinar_post_id)); ?>">Pago y Registro</a>
                    <?php else: ?>
                        <a href="<?php echo $webinar->getWebinarSignUpLinkByUserID(get_current_user_id()); ?>">Registro</a>
                    <?php endif; ?>

                <?php elseif ($membership_level == 3): ?>
                    <!-- Comportamiento específico para nivel 3 -->
                    <a href="<?php echo $webinar->getWebinarSignUpLinkByUserID(get_current_user_id()); ?>">Registro</a>

                <?php else: ?>
                    <!-- Si no es miembro del nivel 2 o 3, usar el enlace de registro normal -->
                    <a href="<?php echo $webinar->getWebinarSignUpLinkByUserID(get_current_user_id()); ?>">Registro</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

                    <?php } ?><?php } else { ?>
                        <div class="newCardFooter">
                            <h5>No hay eventos disponibles en este momento.</h5>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>
    </main>
</div>
