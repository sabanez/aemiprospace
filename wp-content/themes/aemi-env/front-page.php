<?php

use Envixo\AemiGotoWebinar\Helpers\GotoHelpers;
use Envixo\AemiGotoWebinar\Helpers\GotoUserWordPress;
use Envixo\AemiGotoWebinar\Helpers\GotoWebinarClass;

get_header(); ?><?php
$argsWebinars = [
    'numberposts' => 4,
];

if (is_user_logged_in()) {
    $postRegistered = GotoUserWordPress::getUserRegisteredWebinarsPostID(get_current_user_id());


    if ($postRegistered) {
        //$argsWebinars['exclude'] = $postRegistered;
    }
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
}

?>
    <main>
        <section class="WrapperSection">
            <div class="HeroCards flex">
                <div class="AgendaNewsCard card" <?php echo !$getLasContentAccess ? 'style="margin: 0 auto;"' : ''; ?>>
    <div class="card-header">
        <h5 class="card-title">Webinars</h5>
    </div>
    <?php if ($webinars) { ?>
        <!-- Contenedor con scroll: solo 3 visibles -->
        <div class="agenda-scroll" data-visible="3">
        <?php foreach ($webinars as $webinar) {
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
                                    <a href="<?php echo esc_url($url_stripe . '?prefilled_email=' . urlencode($user_email) . '&metadata[webinar_post_id]=' . urlencode($webinar_post_id)); ?>">Registro</a>
                                <?php else: ?>
                                    <a href="<?php echo $webinar->getWebinarSignUpLinkByUserID(get_current_user_id()); ?>">Registro</a>
                                <?php endif; ?>

                            <?php elseif ($membership_level == 3): ?>
                                <!-- Comportamiento específico para nivel 3 -->
                                <a href="<?php echo $webinar->getWebinarSignUpLinkByUserID(get_current_user_id()); ?>">Registro</a>

                            <?php else: ?>
                                <!-- Si no es miembro del nivel 2 o 3, usar el enlace de registro normal TEMPRORAY MODIFICATION TO PREVENT FREE REGISTRATION FOR NON CONNECTED USERS-->
                                <a href="<?php /*echo $webinar->getWebinarSignUpLinkByUserID(get_current_user_id());*/ echo esc_url($url_stripe . '?prefilled_email=' . urlencode($user_email) . '&metadata[webinar_post_id]=' . urlencode($webinar_post_id)); ?>">Registro</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php } ?>
        </div><!-- /agenda-scroll -->

        <script>
(function () {
  // Altura de referencia = altura total del card de Guías clínicas
  function getGuiaHeight() {
    var guiaCard = document.querySelector('.videoCard.card');
    return guiaCard ? guiaCard.getBoundingClientRect().height : 0;
  }

  // Ajusta la altura del contenedor de scroll para que el CARD completo mida igual que Guías clínicas
  function setScrollToMatchCard(scrollEl, targetCardHeight) {
    if (!scrollEl || !targetCardHeight) return;

    var card = scrollEl.closest('.card');
    if (!card) return;

    var cardRect = card.getBoundingClientRect();
    var scrollRect = scrollEl.getBoundingClientRect();
    var csCard = getComputedStyle(card);

    var paddingBottom = parseFloat(csCard.paddingBottom) || 0;
    var paddingTop    = parseFloat(csCard.paddingTop) || 0;
    var borderTop     = parseFloat(csCard.borderTopWidth) || 0;
    var borderBottom  = parseFloat(csCard.borderBottomWidth) || 0;

    // Distancia desde el TOP del card hasta el TOP del contenedor con scroll (incluye header, paddings, márgenes)
    var topGap = (scrollRect.top - cardRect.top);

    // Altura disponible real para el contenedor de scroll
    var available = targetCardHeight - topGap - paddingBottom - borderBottom;

    // Seguridad mínima
    if (available < 80) available = 80;

    scrollEl.style.height    = available + 'px';
    scrollEl.style.maxHeight = available + 'px';
    scrollEl.style.overflowY = 'auto';
  }

  function applyHeights() {
    var targetH = getGuiaHeight();
    if (!targetH) return;

    // WEBINARS: contenedor con data-visible
    var webinarsScroll = document.querySelector('.AgendaNewsCard.card .agenda-scroll[data-visible]');
    if (webinarsScroll) setScrollToMatchCard(webinarsScroll, targetH);

    // EVENTOS PRESENCIALES: id específico
    var presencialesScroll = document.getElementById('agendaScroll');
    if (presencialesScroll) setScrollToMatchCard(presencialesScroll, targetH);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', applyHeights);
    window.addEventListener('load', applyHeights);
  } else {
    applyHeights();
  }

  // Reaplica en resize por si cambia la altura de Guías clínicas en responsive
  window.addEventListener('resize', applyHeights);
})();
</script>


    <?php } else { ?>
        <div class="newCardFooter">
            <h5>No hay webinars disponibles en este momento.</h5>
        </div>
    <?php } ?>
    <?php if ($webinars): ?>
        <?php /* 
        <div class="newViewMore">
            <a href="/agenda" class="btn-primary flex">Ver más</a>
        </div>
        */ ?>
    <?php endif; ?>
</div>



				
				<?php
// Obtener la fecha actual
$current_date = current_time('Y-m-d H:i:s');

// Query para obtener los eventos presenciales con fecha futura
$args = array(
    'post_type'      => 'evento_presencial', // El slug de tu CPT
    'posts_per_page' => -1,                  // Traer todos los posts
    'meta_key'       => 'fecha',             // El campo personalizado donde está la fecha
    'meta_value'     => $current_date,       // Comparar con la fecha actual
    'meta_compare'   => '>',                 // Solo los que tienen una fecha futura
    'orderby'        => 'meta_value',        // Ordenar por fecha
    'order'          => 'ASC',               // De forma ascendente (más cercanos primero)
    'meta_type'      => 'DATETIME'           // Definir el tipo de meta_value como datetime
);

$eventos_presenciales = new WP_Query($args);
?>

<div class="AgendaNewsCard card">
    <!-- Este bloque siempre será visible -->
    <div class="card-header">
        <h5 class="card-title">Eventos presenciales</h5>
    </div>

    <?php if ($eventos_presenciales->have_posts()) : ?>
        <!-- Contenedor con scroll -->
        <div id="agendaScroll" class="agenda-scroll">
            <?php while ($eventos_presenciales->have_posts()) : $eventos_presenciales->the_post();
                // Obtener el valor del campo personalizado "fecha"
                $fecha_evento = get_post_meta(get_the_ID(), 'fecha', true);
                $formatted_date = date('d', strtotime($fecha_evento));
                $formatted_month = strtoupper(date('M', strtotime($fecha_evento)));
                $formatted_time = date('H:i', strtotime($fecha_evento));
                ?>
                <div class="newList">
                    <div class="newListTittle">
                        <div class="newListTittleHeading">
                            <h3><?php echo $formatted_date; ?></h3>
                            <p><?php echo $formatted_month; ?></p>
                            <p><?php echo $formatted_time; ?></p>
                        </div>
                        <div class="newDistribution closed">
                            <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <script>
        (function () {
            function setAgendaHeight() {
                var container = document.getElementById('agendaScroll');
                if (!container) return;

                var items = container.querySelectorAll('.newList');
                var visible = 3;

                if (items.length <= visible) {
                    container.style.maxHeight = 'none';
                    container.style.overflowY = 'visible';
                    return;
                }

                // Altura exacta de los 3 primeros (incluye márgenes vía offsetTop)
                var first = items[0];
                var last  = items[visible - 1];
                var height = (last.offsetTop + last.offsetHeight) - first.offsetTop;

                container.style.maxHeight = height + 'px';
                container.style.overflowY = 'auto';
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', setAgendaHeight);
            } else {
                setAgendaHeight();
            }
            // Recalcula por si cambian fuentes/anchos
            window.addEventListener('resize', setAgendaHeight);
        })();
        </script>
    <?php else : ?>
        <!-- Si no hay eventos, se mostrará este mensaje -->
        <div class="newCardFooter">
            <h5>No hay eventos presenciales futuros disponibles.</h5>
        </div>
    <?php endif; ?>
    
    <?php wp_reset_postdata(); ?>
</div>




                <?php if ($getLasContentAccess) { ?>
                    <div class="videoCard card">
                        <div class="card-header" style="min-height:auto;">
                            <h5 class="card-title">
                                <?php echo $getLasContentAccess['contains_video'] ? 'Guías clínicas' : 'Guías clínicas'; ?> </h5>
                        </div>
						 
                        <div class="videoCardFooter" style="display:none;">
                            <div>
                                <a href="<?php echo $getLasContentAccess['link']; ?>" class="<?php echo $getLasContentAccess['contains_video'] ? 'continue-video' : ''; ?>">

                                    <span style="background-image: url(<?php echo $getLasContentAccess['image_url'] ? $getLasContentAccess['image_url'] : ''; ?>);" class="view-backgroud-image"></span>
                                    <?php echo $getLasContentAccess['contains_video'] ? '' : '<span class="view-container-btn">Ver contenido</span>'; ?>
                                </a>
                            </div>
                        </div>
						
						

                        <h5 class="card-title-content-name" style="display:none;">
                            <a href="<?php echo $getLasContentAccess['link']; ?>" class="<?php echo $getLasContentAccess['contains_video'] ? 'continue-video' : ''; ?>"><?php echo $getLasContentAccess['title']; ?></a>
                        </h5>
										 <!-- Línea horizontal separadora -->
  <hr class="separator-line" style="margin-bottom:20px; margin-right:10px; margin-left:10px;color: #fff; display:none;">
<div class="guia-clinica-container" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%;">
  <a href="https://prospace.aemi.es/wikimi/?content_type%5B%5D=115">
    <img src="https://prospace.aemi.es/wp-content/uploads/2025/03/Guías-clínicas-espacio-pro.jpg" alt="Guía clínica" style="width: 350px; margin: 5px 10px; border-radius: 10px;">
  </a>
  <a href="https://prospace.aemi.es/wikimi/?content_type%5B%5D=115" class="btn-primary flex" style="max-height:45px; display: flex; align-items: center; justify-content: center; margin: 30px;">
    Ver guías
  </a>
</div>



                    </div>

                <?php } ?>
            </div>
			
        </section>
        <!-- Suggestions section -->
        <?php
        $args = [
            'tax_query'       => aemiRegisterCustomTags::returnCustomizedArgsToQuery(),
            'container_class' => 'col-lg-3 mb-4',
        ];

        $argsTax = AemiFilterPostsBasedOnUserLevel::getQueryTaxArgsShowOnlyUserLevelRelatedContent();

        if (isset($args['tax_query']) && $args['tax_query'] && $argsTax) {
            $args['tax_query'][] = $argsTax;
        }

        $args = array_merge([
            'posts_per_page' => 4,
        ], $args);

        // get posts
        $posts = get_posts($args);

        ?>
        <section class="SuggestionWrapper">
            <div class="SuggestionWrapperHeading">
                <h4>Sugerencias de contenido personalizado</h4>
            </div>
            <div class="SuggestionWrapperCard">

                <?php if ($posts) { ?><?php foreach ($posts as $post) { ?><?php
                    $thumbnail = get_the_post_thumbnail($post->ID, 'wikimi-thumbnail', ['class' => 'card-image-full']);
                    $thumbnail = $thumbnail ? $thumbnail : '<img width="305" height="205" src="' . get_template_directory_uri() . '/assets/images/box-posts-default.jpg" class="attachment-wikimi-thumbnail size-wikimi-thumbnail wp-post-image card-image-full" />';
                    $content_type = get_aemi_content_types_post($post->ID, 'content_type', false);
                    $favorite_button = favorite_button($post->ID);
                    ?>
                    <div class="SuggestionContent card">
                        <div class="wrapperDoctor">
                            <a href="<?php echo get_permalink($post->ID); ?>"><?php echo $thumbnail; ?></a>
                            <div class="SuggestionContentIcons">
                                <button type="button" class="heartIcon <?php echo $favorite_button['class']; ?>" data-id="<?php echo $favorite_button['data-id']; ?>">
                                    <svg width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g id="mdi:heart-outline">
                                            <path id="Vector" d="M14.6207 22.4146L14.4998 22.5354L14.3669 22.4146C8.62734 17.2067 4.83317 13.7629 4.83317 10.2708C4.83317 7.85417 6.64567 6.04167 9.06234 6.04167C10.9232 6.04167 12.7357 7.25 13.3761 8.89333H15.6236C16.264 7.25 18.0765 6.04167 19.9373 6.04167C22.354 6.04167 24.1665 7.85417 24.1665 10.2708C24.1665 13.7629 20.3723 17.2067 14.6207 22.4146ZM19.9373 3.625C17.8348 3.625 15.8169 4.60375 14.4998 6.13833C13.1828 4.60375 11.1648 3.625 9.06234 3.625C5.34067 3.625 2.4165 6.53708 2.4165 10.2708C2.4165 14.8262 6.52484 18.56 12.7478 24.2029L14.4998 25.7979L16.2519 24.2029C22.4748 18.56 26.5832 14.8262 26.5832 10.2708C26.5832 6.53708 23.659 3.625 19.9373 3.625Z" fill="#FF3366"></path>
                                        </g>
                                    </svg>
                                </button>
                                <div class="wrapperWebniar">
                                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g id="solar:play-bold" opacity="0.8">
                                            <path id="Vector" d="M15.1649 6.62461C15.5051 6.80552 15.7897 7.0756 15.9881 7.40589C16.1865 7.73618 16.2914 8.11424 16.2914 8.49957C16.2914 8.88489 16.1865 9.26295 15.9881 9.59324C15.7897 9.92353 15.5051 10.1936 15.1649 10.3745L6.0897 15.3095C4.62841 16.1042 2.8335 15.0701 2.8335 13.4352V3.56461C2.8335 1.92836 4.62841 0.8949 6.0897 1.68894L15.1649 6.62461Z" fill="var(--color-purple)"/>
                                        </g>
                                    </svg>
                                    <p>
                                        <a href="<?php echo get_permalink($post->ID); ?>"><?php echo sanitize_text_field($content_type); ?></a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="SuggestionWrapperCardFooter">
                            <h5>
                                <a href="<?php echo get_permalink($post->ID); ?>" class="card-title"><?php echo sanitize_text_field($post->post_title); ?></a>
                            </h5>
                            <p>
                                <a href="<?php echo get_permalink($post->ID); ?>" class="card-para-info"><?php echo get_aemi_text_excerpt($post, 260); ?></a>
                            </p>
                        </div>
                    </div>
                <?php } ?><?php } ?>


            </div>
            <div style="text-align: center;">
                <a href="<?php echo get_permalink(404); ?>" style="display: inline-flex;" class="suggestionsButton btn-primary flex">
                    <!--        Vea más recomendaciones-->
                    Ver más recomendaciones
                </a>
            </div>
        </section>
        <!--swiper slider   -->
        <?php $slides = get_aemi_array_value_by_user_locale(get_post_meta($post->ID, '_slides_home', true)); ?>
        <?php if ($slides) { ?>
            <section class="aemi-home-slider-container">
                <!-- Slider main container -->
                <div class="swiper">
                    <!-- Additional required wrapper -->
                    <div class="swiper-wrapper">
                        <?php foreach ($slides as $slide) { ?><?php
                            $thumbnail_url = '';

                            if (wp_is_mobile()) {
                                if (isset($slide['thumbnail_id_mobile']) && $slide['thumbnail_id_mobile']) {
                                    $thumbnail_url = wp_get_attachment_image_url($slide['thumbnail_id_mobile'], 'full');
                                } else {
                                    $thumbnail_url = wp_get_attachment_image_url($slide['thumbnail_id'], 'full');
                                }
                            } else {
                                if (isset($slide['thumbnail_id']) && $slide['thumbnail_id']) {
                                    $thumbnail_url = wp_get_attachment_image_url($slide['thumbnail_id'], 'full');
                                }
                            }
                            ?>
                            <div class="swiper-slide">
                                <div>
                                    <img src="<?php echo $thumbnail_url; ?>"/>
                                    <div class="slide-home_content">
                                        <div>
                                            <?php
                                            // echo ( isset( $slide['title'] ) && $slide['title'] ? '<p class="slide-home__title">' . $slide['title'] . '</p>' : '' );
                                            // echo ( isset( $slide['content'] ) && $slide['content'] ? '<div class="slide-home__content">' . nl2br( $slide['content'] ) . '</div>' : '' );

                                            // if( isset( $slide['button_text'] ) && $slide['button_text'] && isset( $slide['button_link'] ) && $slide['button_link'] ){
                                            //   echo '<a href="' . esc_attr( $slide['button_link'] ) . '" title="' . esc_attr( $slide['button_text'] ) . '" class="btn btn-secondary">' . $slide['button_text'] . '</a>';
                                            // }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <!-- If we need pagination -->
                    <div class="swiper-pagination"></div>
                    <!-- If we need navigation buttons -->
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </section>
        <?php } ?>

        <section class="MicroWrapperCard card">

            <?php

            $args = [
                'number'  => 99,
                'exclude' => 1,
            ];

            // get testimonials
            $categories = get_categories($args);
            ?>
            <div class="MicroWrapperHeader">
                <h3>Microinmunoterapia</h3>
            </div>
            <div class="MicroWrapperdescription">

                <?php if ($categories) { ?><?php foreach ($categories as $category) { ?><?php
                    $category_thumbnail_id = get_term_meta($category->term_id, '_category_thumbnail_id', true);
                    $url = $category_thumbnail_id ? wp_get_attachment_url($category_thumbnail_id) : '';
                    ?>
                    <div class="MicroWrapperText">
                        <div class="MicroWrapperIcon" style="background-image: url(<?php echo $url; ?>);">
                            <a href="<?php echo get_category_link($category->term_id); ?>"></a>
                        </div>
                        <div class="enfermedadesText">
                            <h4>
                                <a href="<?php echo get_category_link($category->term_id); ?>"><?php echo sanitize_text_field($category->name); ?></a>
                            </h4>
                            <p>
                                <?php echo esc_attr($category->description); ?>
                            </p>
                            <!--              <h6><a href="-->
                            <?php //echo get_category_link($category->term_id); ?><!--">Más información</a></h6>-->
                        </div>
                    </div>
                <?php } ?><?php } ?>

            </div>
        </section>
        <!-- Doctors -->
        <section>
            <div class="wrapperDoctors">
                <h4>Lo que dicen los profesionales sobre la microinmunoterapia</h4>
                <!--      <h4>Lo que los médicos han dicho sobre nosotros</h4>-->
            </div>
            <div class="mySwiper">
                <!-- Additional required wrapper -->
                <div class="swiper-wrapper">

                    <?php
                    $args = [
                        'posts_per_page' => 20,
                        'post_type'      => 'testimonials',
                    ];

                    $testimonials = get_posts($args);

                    if ($testimonials) {
                        foreach ($testimonials as $testimonial) {
                            $get_thumbnail = get_the_post_thumbnail_url($testimonial->ID, 'thumbnail');
                            $text_below_name = get_post_meta($testimonial->ID, '_text_below_name', true);
                            ?>
                            <div class="swiper-slide swiperslideCard">
                                <?php echo apply_filters('the_content', $testimonial->post_content); ?>
                                <h2>~ <?php echo sanitize_text_field($text_below_name); ?></h2>
                                <div class="swiperslideCardIcon">
                                    <svg width="40" height="40" viewBox="0 0 86 69" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path id="Vector" opacity="0.5" d="M7.3 9.88425C-0.808044 18.7163 0.00834656 30.0581 0.0341263 30.1875V64.6875C0.0341263 65.8312 0.486824 66.9281 1.29263 67.7369C2.09843 68.5456 3.19134 69 4.33092 69H30.1117C34.851 69 38.7052 65.1317 38.7052 60.375V30.1875C38.7052 29.0438 38.2525 27.9469 37.4467 27.1381C36.6409 26.3294 35.548 25.875 34.4084 25.875H21.1829C21.2737 23.743 21.9084 21.67 23.0262 19.8548C25.209 16.4004 29.321 14.0415 35.2549 12.8512L38.7052 12.1612V0H34.4084C22.4505 0 13.3284 3.32494 7.3 9.88425ZM54.5948 9.88425C46.4824 18.7163 47.3031 30.0581 47.3289 30.1875V64.6875C47.3289 65.8312 47.7816 66.9281 48.5874 67.7369C49.3932 68.5456 50.4861 69 51.6257 69H77.4064C82.1458 69 86 65.1317 86 60.375V30.1875C86 29.0438 85.5473 27.9469 84.7415 27.1381C83.9357 26.3294 82.8428 25.875 81.7032 25.875H68.4777C68.5685 23.743 69.2032 21.67 70.321 19.8548C72.5038 16.4004 76.6158 14.0415 82.5497 12.8512L86 12.1612V0H81.7032C69.7452 0 60.6232 3.32494 54.5948 9.88425Z" fill="#CCCCCC"/>
                                    </svg>
                                </div>
                            </div>
                        <?php } ?><?php } ?>

                </div>
                <!-- If we need pagination -->
                <div class="swiper-pagination"></div>
                <!-- If we need navigation buttons -->
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </section>
        <!-- footer -->
<style>
  .wrapperFooter {
    position: relative;
    background-image: url('https://prospace.aemi.es/wp-content/uploads/2025/03/BANNER-EXPERIENCIA-scaled.jpeg'); 
    background-size: cover;
    background-position: center;
    padding: 30px 20px;
    text-align: center;
    color: #fff;
    overflow: hidden;
  }
  /* Pseudo-elemento para el overlay blanco con opacidad */
  .wrapperFooter::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255,255,255,0.6);
    pointer-events: none;
    z-index: 1;
  }
  .wrapperFooter > * {
    position: relative;
    z-index: 2;
  }
  .wrapperFooter h3 {
    margin-bottom: 20px;
  }
  .footerButton {
    background-color: #006ab4;
    display: inline-flex;
    align-items: center;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
	margin-bottom: 50px;
  }
  .footerButton svg {
    fill: white;
  }
  .footerButton h4 {
    color: white;
    margin: 0 0 0 10px;
    font-size: 16px;
    transition: color 0.3s ease;
  }
  /* Al hacer hover, cambia el color del texto a #006ab4 */
  .footerButton:hover h4 {
    color: #006ab4;
  }
</style>

<section class="wrapperFooter">
  <h3>¡Nos encantaría conocer tu experiencia!</h3>
  <a href="https://www.aemi.es/contacto/comparta-su-historia-y-experiencia/" target="_blank" class="footerButton btn-primary">
    <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
      <g id="mdi:heart-outline">
        <path id="Vector" d="M18.5625 3.375C16.605 3.375 14.7262 4.28625 13.5 5.715C12.2738 4.28625 10.395 3.375 8.4375 3.375C4.9725 3.375 2.25 6.08625 2.25 9.5625C2.25 13.8038 6.075 17.28 11.8688 22.5338L13.5 24.0187L15.1312 22.5338C20.925 17.28 24.75 13.8038 24.75 9.5625C24.75 6.08625 22.0275 3.375 18.5625 3.375Z" fill="white"/>
      </g>
    </svg>
    <h4 id="expBtn">Comparte tu experiencia</h4>
  </a>
</section>


        <!--Modal: Cancel Start
        <div class="scoreLevel1" id="experienceModal" style="display: none">
            <div class="mainScoreU">
                <div class="updInfo">
                    <p class="firParaU">Comparte tu experiencia</p>
                    <img id="expClose" src="<?php echo get_aemi_url_assets_imgs('new-layout-v1/icon/close.png'); ?>" alt="close"/>
                </div>
                <div class="SpecifyIns" id="spcfrm">
                    <form action="#" method="post">
                        <?php echo AemiCPTTestimonials::render_nonce_aemi_insert_new_testimonial(); ?>
                        <div class="formField">
                            <textarea type="text" id="testimonial_message" name="testimonial_message" placeholder=""></textarea>
                        </div>
                        <div class="modelBtn">
                            <button class="btn-primary" type="submit">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        Modal: Cancel End -->
    </main>
    <script>
        const swipers = new Swiper(".swiper", {
            // Optional parameters
            direction: "horizontal",
            loop: true,

            // Navigation arrows
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });

        var swiper = new Swiper(".mySwiper", {
            loop: true,

            // Navigation arrows
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },

            slidesPerView: 1,
            spaceBetween: 10,
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 40,
                },
                1024: {
                    slidesPerView: 2.7,
                    spaceBetween: 50,
                },
            },
        });
    </script>
    <script>

        jQuery(document).ready(function ($) {
            let sending = false;
            $('#experienceModal form').submit(function (e) {
                e.preventDefault();
                $('#experienceModal form button').prop('disabled', true);
                $('#experienceModal form button').attr('disabled', true);

                if (!sending) {
                    sending = true;
                    $(this).addClass('loading-send');

                    let testimonialTextarea = $('#experienceModal form textarea[name="testimonial_message"]').val();
                    let testimonialNonce = $('#experienceModal form input[type="hidden"]');

                    let formDataJSON = {
                        'action': 'aemi_insert_new_testimonial',
                        'mensagem': testimonialTextarea
                    };

                    formDataJSON[testimonialNonce.prop('name')] = testimonialNonce.val();
                    let alert = $('.alerts');

                    $.ajax({
                        type: 'POST',
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        data: formDataJSON,
                        success: function (response) {
                            if (response && response.data) {
                                if (response.data.success) {
                                    alert.addClass('push-alert alert-type-success');
                                    alert.find('.alert-content').html(response.data.message);

                                    $('#experienceModal #expClose').click();
                                    $('#experienceModal form textarea[name="testimonial_message"]').val('');

                                } else {
                                    alert.addClass('push-alert alert-type-failed');
                                    alert.find('.alert-content').html(response.data.message);
                                }

                                setTimeout(function () {
                                    alert.removeClass('push-alert alert-type-success alert-type-failed');
                                    alert.find('.alert-content').html('');
                                }, 3000);

                                sending = false;
                                $('#experienceModal form button').removeProp('disabled', true);
                                $('#experienceModal form button').removeAttr('disabled', true);
                            }
                        }
                    });
                }

            });
        });
    </script>
<?php get_footer(); ?>