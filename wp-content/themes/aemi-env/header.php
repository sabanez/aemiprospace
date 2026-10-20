<!DOCTYPE html>

<html <?php language_attributes(); ?>>

	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" >
	
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
		<?php wp_head(); ?>
<?php
if ( is_user_logged_in() ) : // Verificamos si el usuario está logueado
    $current_user = wp_get_current_user();
    $user_email = $current_user->user_email; // Obtener el email del usuario

    // Verificamos si estamos en una página singular de tipo 'aemi_goto_webinar'
    if ( is_singular('aemi_goto_webinar') ) {
        // Obtener el ID del post actual (webinar)
        $post_id = get_the_ID();
        
        // Obtener el metadato del ID del webinar desde el post (reemplaza 'webinar_id_meta_key' con la clave correcta)
        $webinar_post_id = get_post_meta($post_id, 'webinar_id_meta_key', true); // Cambia 'webinar_id_meta_key' por la clave real del metadato

        // Solo ejecutamos si tenemos tanto el email del usuario como el ID del webinar
        if ($webinar_post_id && $user_email) {
            ?>
            <script type="text/javascript">
                document.addEventListener('DOMContentLoaded', function() {
                    // Buscar los enlaces de Stripe que contengan "prefilled_email="
                    var stripeLinks = document.querySelectorAll('a[href*="prefilled_email="]');
                    
                    // Reemplazar el prefilled_email vacío con el email real del usuario logueado y agregar el ID del webinar
                    stripeLinks.forEach(function(link) {
                        var originalHref = link.getAttribute('href');
                        // Añadir el email del usuario y el ID del webinar a la URL de Stripe
                        var newHref = originalHref + '<?php echo urlencode($user_email); ?>' + '&metadata[webinar_post_id]=<?php echo urlencode($webinar_post_id); ?>';
                        link.setAttribute('href', newHref);
                    });
                });
            </script>
            <?php
        } else {
            // Mensaje de error si no tenemos los datos
            echo '<!-- Error: No se pudo obtener el email del usuario o el ID del webinar -->';
        }
    }
endif; // Fin del bloque de PHP
?>



	</head>

	<body <?php body_class(); ?>>

		<?php wp_body_open(); ?>
		<?php require_once 'includes/header.php'; ?>
		<?php
            if (!is_account_page()) {
                require_once 'includes/title-and-breadcrumbpages.php';
            }
?>
 
        <?php /*if (!is_account_page()) { ?>
        <div class="aemi-arrow-home-down">
            <a href="#" onclick="window.scrollBy(0, window.innerHeight);">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="25.145" height="30.33" viewBox="0 0 25.145 30.33">
                    <defs>
                        <clipPath id="clip-path">
                        <rect width="18.923" height="11.734" transform="translate(0 -2)" fill="none"/>
                        </clipPath>
                        <clipPath id="clip-path-2">
                        <rect width="23.011" height="13.837" transform="translate(0 -2)" fill="none"/>
                        </clipPath>
                        <clipPath id="clip-path-3">
                        <rect width="25.145" height="14.935" transform="translate(0 -2)" fill="none"/>
                        </clipPath>
                    </defs>
                    <g transform="translate(1889 1019.8)">
                        <g transform="translate(-1889 -1017.8)">
                            <g transform="translate(0 0)" clip-path="url(#clip-path-3)">
                                <path d="M25.037-1.9l-.1-.1L12.79,10.1a.323.323,0,0,1-.482,0L.206-2l-.1.1A7.113,7.113,0,0,0,.11.58l12.2,12.246a.321.321,0,0,0,.482,0L25.037.58a7.151,7.151,0,0,0,0-2.483" transform="translate(0 0)" fill="#333" fill-rule="evenodd"/>
                            </g>
                        </g>
                        <g transform="translate(-1888 -1008.174)">
                            <g transform="translate(0 0)" clip-path="url(#clip-path-2)">
                                <path d="M22.912-1.911,22.822-2,11.7,9.077a.3.3,0,0,1-.441,0L.188-2,.1-1.911A7.511,7.511,0,0,0,.1.531L11.264,11.737a.294.294,0,0,0,.441,0L22.912.531a7.551,7.551,0,0,0,0-2.442" transform="translate(0 0)" fill="#333" fill-rule="evenodd"/>
                            </g>
                        </g>
                        <g transform="translate(-1886 -999.204)">
                            <g transform="translate(0 0)" clip-path="url(#clip-path)">
                                <path d="M18.842-1.927,18.767-2,9.625,7.109a.243.243,0,0,1-.363,0L.155-2l-.072.073a8.54,8.54,0,0,0,0,2.363l9.18,9.216a.241.241,0,0,0,.363,0L18.842.436a8.586,8.586,0,0,0,0-2.363" transform="translate(0 0)" fill="#333" fill-rule="evenodd"/>
                            </g>
                        </g>
                    </g>
                </svg>

            </a>
        </div>
        <?php } ?>
*/ ?>
    <?php get_template_part('template-parts/content', 'alerts');

		