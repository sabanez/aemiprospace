<?php
/**
 * My Account Dashboard.
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 *
 * @version 4.4.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

global $aemiHelpLevelUsersClassLoaded;

?>
<div class="subScore">
	<div class="scoreLevel scoreLevelScoreContent">
		<div class="mainScore">
			<div class="card-header">
				<h6 class="card-title">Puntuación por nivel y subpuntuación</h6>
				<button class="btn claim-btn" id="claimPointsBtn">
					<svg width="19" height="14" viewBox="0 0 19 14" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path
							d="M15.2 2C15.2 0.897 14.3478 0 13.3 0H1.9C0.85215 0 0 0.897 0 2V12C0 13.103 0.85215 14 1.9 14H13.3C14.3478 14 15.2 13.103 15.2 12V8.667L19 12V2L15.2 5.333V2Z"
							fill="white"></path>
					</svg>
<!--					¿Cómo reclamar puntos?-->
                    ¿Cómo sumar competencias?
				</button>
			</div>
			
            <?php

                $taxonomy = get_aemi_user_total_score_by_taxonomies_scores(get_current_user_id());
$getUserScoreCurrentLevel = get_aemi_current_user_score_current_level();

if ($taxonomy) {
    $currentTaxActive = $taxonomy[$getUserScoreCurrentLevel->term_id];
    $is_old = true;

    foreach ($taxonomy as $taxList) {
        $is_current = ($currentTaxActive->term_id == $taxList->term_id ? 'score-level-tax-content__title--current' : false);
        $is_old = ($is_current ? false : $is_old);

        $total_percent = $aemiHelpLevelUsersClassLoaded->calculate_percentage_of_value($taxList->_score_limit_current_point_total_with_limit, $taxList->_max_level_points_in);
        $correctTotal = $is_current || $is_old ? $total_percent : 0;
        ?>
        <div class="score-level-tax-content <?php echo $is_current ? 'score-level-tax-content__title--current' : ''; ?>">
            <input type="checkbox" name="" class="score-level-tax-content--input" id="element-show-<?php echo $taxList->term_id; ?>" />
            <label for="element-show-<?php echo $taxList->term_id; ?>" class="score-level-tax-content__title-generical <?php echo $is_current ? '' : 'score-level-tax-content__title'; ?> <?php echo $is_old ? 'score-level-tax-content__title--old' : ''; ?>">
                <?php if ($is_old) { ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="31.999" height="32" viewBox="0 0 31.999 32"><g transform="translate(-2 -2)"><path d="M28.373,22.579a13.6,13.6,0,0,1-16.584-15.5,2.03,2.03,0,0,1-.181.155,5.46,5.46,0,0,1-2.128.72l-1.016.23c-3.936.891-5.9,1.336-6.373,2.842s.874,3.074,3.557,6.211l.694.811a5.905,5.905,0,0,1,1.315,1.888,6.153,6.153,0,0,1,0,2.336l-.106,1.083c-.4,4.187-.608,6.28.618,7.21s3.069.083,6.752-1.614l.955-.438A5.433,5.433,0,0,1,18,27.791a5.446,5.446,0,0,1,2.126.723l.952.438c3.685,1.7,5.528,2.544,6.752,1.616s1.024-3.024.619-7.211Z" transform="translate(0 3.049)" fill="#ef8933"/><path d="M9.846,7.453l-.525.941a5.888,5.888,0,0,1-1.312,1.891,1.851,1.851,0,0,0,.181-.155,13.6,13.6,0,0,0,16.585,15.5l-.032-.3a6.178,6.178,0,0,1,0-2.336A5.878,5.878,0,0,1,26.059,21.1l.694-.811c2.683-3.136,4.024-4.706,3.555-6.211s-2.435-1.952-6.371-2.842L22.919,11a5.48,5.48,0,0,1-2.128-.72,5.888,5.888,0,0,1-1.312-1.891l-.523-.941C16.929,3.818,15.916,2,14.4,2S11.873,3.818,9.846,7.453Z" transform="translate(3.599 0)" fill="#ef8933" opacity="0.5"/></g></svg>
                    Nivel <span><?php echo sanitize_text_field($taxList->name); ?></span> concluido
                    <span class="arrow-element-score"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/></svg></span>
                <?php } elseif ($is_current) { ?>
                    Nivel actual: <span><?php echo sanitize_text_field($taxList->name); ?></span>
                <?php } else { ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="31.999" height="32" viewBox="0 0 31.999 32"><g transform="translate(-2 -2)"><path d="M28.373,22.579a13.6,13.6,0,0,1-16.584-15.5,2.03,2.03,0,0,1-.181.155,5.46,5.46,0,0,1-2.128.72l-1.016.23c-3.936.891-5.9,1.336-6.373,2.842s.874,3.074,3.557,6.211l.694.811a5.905,5.905,0,0,1,1.315,1.888,6.153,6.153,0,0,1,0,2.336l-.106,1.083c-.4,4.187-.608,6.28.618,7.21s3.069.083,6.752-1.614l.955-.438A5.433,5.433,0,0,1,18,27.791a5.446,5.446,0,0,1,2.126.723l.952.438c3.685,1.7,5.528,2.544,6.752,1.616s1.024-3.024.619-7.211Z" transform="translate(0 3.049)" fill="#bfbfbf"/><path id="Caminho_1378" data-name="Caminho 1378" d="M9.846,7.453l-.525.941a5.888,5.888,0,0,1-1.312,1.891,1.851,1.851,0,0,0,.181-.155,13.6,13.6,0,0,0,16.585,15.5l-.032-.3a6.178,6.178,0,0,1,0-2.336A5.878,5.878,0,0,1,26.059,21.1l.694-.811c2.683-3.136,4.024-4.706,3.555-6.211s-2.435-1.952-6.371-2.842L22.919,11a5.48,5.48,0,0,1-2.128-.72,5.888,5.888,0,0,1-1.312-1.891l-.523-.941C16.929,3.818,15.916,2,14.4,2S11.873,3.818,9.846,7.453Z" transform="translate(3.599 0)" fill="#707070" opacity="0.5"/></g></svg>
                    Siguiente nivel: <?php echo sanitize_text_field($taxList->name); ?>
                    <span class="arrow-element-score"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/></svg></span>
                <?php } ?>
            </label>

            <div class="list-container-total-score">
                <div class="list-container-total-score--inner">
                    <div class="totalScore">
                        <p>Puntos totales ganados hasta el momento:</p>
                        <label><progress max="100" value="<?php echo $correctTotal; ?>" data-before="<?php echo $taxList->_start_level_at; ?>" data-after="<?php echo $taxList->_start_level_at + $taxList->_max_level_points_in; ?>">40%</progress></label>
                        <div class="totalScore-percent-icon <?php echo $correctTotal <= 10 ? 'totalScore-percent-icon-minus-2' : ''; ?>">
                            <span style="left: <?php echo $correctTotal; ?>%;"><?php echo $taxList->_start_level_at + $taxList->_score_limit_current_point_total_with_limit; ?> </span>
                        </div>
                        <ul>
                            <li></li>
                            <li style="color: var(--color-grey)">Límite máximo</li>
                        </ul>
                    </div>

                    <?php $loopSubTax = 0;
        if ($taxList->classification) { ?>
                        <?php foreach ($taxList->classification as $subtax) {
                            ++$loopSubTax; ?>
                            <div class="skill">
<!--                                --><?php //echo $loopSubTax === 1 ? '<p>Habilidades disponibles:</p>' : ''; ?>
                                <?php echo $loopSubTax === 1 ? '<p>Competencias disponibles:</p>' : ''; ?>
                                <ul>
                                    <li>
                                    <?php echo $subtax->description; ?>
                                    </li>
                                    <li><span> <?php echo $subtax->_score_limit_current_point_total_with_limit; ?></span> / 100</li>
                                </ul>
                            </div>
                        <?php } ?>
                    <?php } ?>

                </div>
            </div>
        </div>
        <?php
    }
}
?>
		</div>
	</div>


	<div class="scoreLevel">
		<div class="mainScore" id="list-content-info-box">
			<div class="card-header">
				<h6 class="card-title">Información de puntuación de contenido</h6>
			</div>
            <div id="list-content-score">
                <div  id="list-content-score-inner">
            <?php
    $posts = get_aemi_user_viewed_page_related_to_scores(get_current_user_id(), true);

if ($posts) {
    foreach ($posts as $current_post) {
        ?>
                    <div class="contentScore">
                        <p class="RestSys">
                            <?php echo sanitize_text_field($current_post['post_info']->post_title); ?>
                        </p>
                        <div class="ImmuneInt">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Puntos ganados</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo date('M d, Y g:i A', strtotime($current_post['date'])); ?></td>
                                        <td><?php echo array_sum($current_post['score_classification']); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php
    }
} else {
    echo '<div class="aemi-admin-settings-score-to-users__no-content">'.__('No data at the moment.', AEMI_TEXT_DOMAIN).'</div>';
}
?>
               </div>
            </div>
		</div>
	</div>
</div>

<script>
    document.querySelector('#claimPointsBtn').addEventListener('click',function(){
        if( !document.querySelector('#claimPointPopup video') ){
            jQuery('#vídeo-preview-score-info').append('<video loop muted autoplay loading="lazy" ><source src="<?php echo get_aemi_url_assets_imgs('vp.mp4'); ?>" type="video/mp4" loading="lazy" ></video>');
        }
    });
</script>

<!--Modal: Claim Points Start -->
<div class="scoreLevel1" id="claimPointPopup" style="display: none">
    <div class="cancel-Subscription-popup claim-points-popup">
    <div class="cancel-top-bar">
        <span>
        <h2 style="color: var(--color-secondary)">
<!--            Aprenda como reivindicar pontos!-->
            Aprende a sumar los puntos de las competencias adquiridas.
        </h2>
        </span>
        <img
        class="cross-img"
        src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/cross.png'); ?>"
        id="crossIconClaim"
        />
    </div>
    <div class="popup-content">
        <p>
<!--        Consulte o conteúdo abaixo para obter informações sobre como reivindicar pontos de conteúdo na plataforma.-->
            Visualiza el siguiente vídeo para obtener más información sobre cómo reclamar los puntos ganados con la visualización de un nuevo contenido en la plataforma.
        </p>
        <div id="vídeo-preview-score-info"></div>
    </div>
    </div>
</div>
<!--Modal: Claim Points End -->