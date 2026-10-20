<?php
/**
 * Template Name: Libro - Métodos de diagnóstico
 * Description: Página de venta del libro "Métodos de diagnóstico en medicina integrativa" con botones Stripe.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

/**
 * CONFIGURACIÓN
 */

// IDs de niveles en Simple WP Membership
$socio_level_id    = 3; // Socio
$no_socio_level_id = 2; // No socio

// Payment Links de Stripe
$stripe_socios_1 = 'https://buy.stripe.com/00w28s4R470M9oq3Gz8og0K';
$stripe_socios_2 = 'https://buy.stripe.com/00w00k4R42KwcACb918og0L';

$stripe_no_socios_1 = 'https://buy.stripe.com/28E9AU3N0eteasu90T8og0N';
$stripe_no_socios_2 = 'https://buy.stripe.com/8x2aEY97kcl69oq4KD8og0M';

// Valores por defecto
$member_level = null;
$member_id    = null;
$email        = '';
$full_name    = '';

/**
 * OBTENER DATOS DEL MIEMBRO (Simple WP Membership)
 */
if ( class_exists( 'SwpmMemberUtils' ) && method_exists( 'SwpmMemberUtils', 'is_member_logged_in' ) ) {
    if ( SwpmMemberUtils::is_member_logged_in() ) {
        $member_level = SwpmMemberUtils::get_logged_in_members_level();
        $member_id    = SwpmMemberUtils::get_logged_in_members_id();

        // Email, nombre, apellidos desde el perfil del miembro
        $email       = SwpmMemberUtils::get_member_field_by_id( $member_id, 'email' );
        $first_name  = SwpmMemberUtils::get_member_field_by_id( $member_id, 'first_name' );
        $last_name   = SwpmMemberUtils::get_member_field_by_id( $member_id, 'last_name' );
        $full_name   = trim( $first_name . ' ' . $last_name );
    }
}

// Determinar tipo de usuario
$user_type = 'none'; // none | socio | no_socio
if ( $member_level == $socio_level_id ) {
    $user_type = 'socio';
} elseif ( $member_level == $no_socio_level_id ) {
    $user_type = 'no_socio';
}

// Sanitizar para atributos data-*
$data_email     = esc_attr( $email );
$data_member_id = esc_attr( $member_id );
$data_full_name = esc_attr( $full_name );
?>

<style>
/* === ESTILOS BÁSICOS PARA LA PÁGINA DEL LIBRO === */

.aemi-book-page {
    max-width: 960px;
    margin: 0 auto;
    padding: 40px 20px 60px;
    font-size: 16px;
    line-height: 1.7;
}

.aemi-book-hero {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(0, 1.2fr);
    gap: 40px;
    align-items: start;
    margin-bottom: 50px;
}

@media (max-width: 768px) {
    .aemi-book-hero {
        grid-template-columns: 1fr;
    }
}

.aemi-book-title {
    font-size: 32px;
    margin-bottom: 10px;
}

.aemi-book-subtitle {
    font-size: 22px;
    margin-bottom: 20px;
    color: #555;
}

.aemi-book-cover img {
    max-width: 100%;
    height: auto;
    display: block;
    padding-left:60px;
    padding-right:60px;
}

/* Bloque de precio y CTA */
.aemi-book-price-box {
    background: #f5f5f5;
    padding: 24px 24px 28px;
    border-radius: 8px;
    margin-bottom: 30px;
}

.aemi-book-price-box h2 {
    margin-top: 0;
    margin-bottom: 16px;
}

.aemi-book-price-list {
    margin: 0 0 12px;
    padding: 0;
    list-style: none;
}

.aemi-book-price-list li + li {
    margin-top: 4px;
}

.aemi-book-notice {
    margin-top: 15px;
    font-size: 14px;
    color: #777;
}

/* Botones Stripe */
.aemi-book-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 16px;
}

.aemi-btn-stripe {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 22px;
    border-radius: 4px;
    font-size: 15px;
    font-weight: 600;
    background: #0073aa;      
    color: #ffffff;
    text-decoration: none;
    border: 1px solid #0073aa;
    cursor: pointer;
    transition: all 0.25s ease;
}

.aemi-btn-stripe:hover {
    background: none;      /* azul más oscuro */
    border-color: #0073aa;
    color: #0073aa;
}

.aemi-btn-stripe:active {
    transform: scale(0.97);
}

.btn-naranja-aemi {
    display:inline-block;
    background:#ef8933;
    color:#fff;
    padding:12px 24px;
    border-radius:4px;
    text-decoration:none;
    font-weight:600;
    font-size:16px;
    transition:0.25s;
}
.btn-naranja-aemi:hover {
    background:#e86d00;
    color:#fff;
}

/* Contenido extra */
.aemi-book-section {
    margin-bottom: 40px;
}

.aemi-book-section h2 {
    font-size: 24px;
    margin-bottom: 16px;
}

.aemi-book-section h3 {
    font-size: 18px;
    margin-top: 18px;
    margin-bottom: 6px;
}
</style>

<div class="aemi-book-page">

    <section class="aemi-book-hero">
        <div class="aemi-book-intro">
            <h1 class="aemi-book-title">Métodos de diagnóstico en medicina integrativa</h1>
            <h2 class="aemi-book-subtitle">Experiencia clínica de microinmunoterapeutas y médicos integrativos</h2>

            <p>
                Un libro que reúne la experiencia clínica de 19 médicos a nivel europeo en torno a su visión global de los principales métodos de diagnóstico utilizados en el campo de la medicina integrativa, en general, y de la microinmunoterapia, en particular. 
            </p><br>

            <h2>Sinopsis</h2>
           <br> <p>
                Como sanitario integrativo, ¿te limitas a evaluar los síntomas de tu paciente o indagas en las causas que le han conducido a su enfermedad?
<br><br>
Cada sanitario cuenta con toda una serie de métodos, técnicas y herramientas para valorar el estado de salud de su paciente, y establece distintos abordajes clínicos según su experiencia acumulada.
<br><br>
En esta obra, 19 médicos a nivel europeo presentan una visión global de los métodos de diagnóstico más utilizados en el campo de la medicina integrativa, en general, y de la microinmunoterapia, en particular. En cada capítulo describen brevemente los fundamentos teóricos de cada uno de los métodos ilustrados con ejemplos de la práctica clínica cotidiana de cada autor.
<br><br>
Descubre otras maneras de abordar a tu paciente y profundiza en aquellas que conoces, para optimizar tu estrategia terapéutica.
<br><br>
            </p>
            <p><strong>Idioma:</strong> Castellano (disponible en alemán y francés). <br><strong>ISBN:</strong> 2-9521523-9-X.</p>
      <br> <a class="btn-naranja-aemi" href="https://aemi.es/metodos-de-diagnostico/">
    Más información
</a> </div>

        <div class="aemi-book-side">
            <div class="aemi-book-cover">
                <?php
                ?>
                <img src="https://aemi.es/wp-content/uploads/2025/11/Metodos-de-diagnostico-en-medicina-integrativa.png"
                     alt="Portada del libro Métodos de diagnóstico en medicina integrativa">
            </div>

            <div class="aemi-book-price-box">
                <h2>Precio</h2>
                <ul class="aemi-book-price-list">
                    <li><strong>Precio Socios:</strong> 46,80€ (IVA incluido)</li>
                    <li><strong>Precio no Socios:</strong> 58,50€ (IVA incluido)</li>
                    <li><strong>Gastos de envío:</strong> 8,50€ (IVA incluido) – Península y Baleares</li>
                </ul>
                <p class="aemi-book-notice">
                    Para pedidos fuera de España o pedidos superiores a dos libros, ponte en contacto con nosotros
                    enviando un correo electrónico a <a href="mailto:info@aemi.es">info@aemi.es</a>.
                </p>
                <p class="aemi-book-notice">
                    
                </p>

                <?php if ( $user_type === 'socio' || $user_type === 'no_socio' ) : ?>
                    <div class="aemi-book-buttons">
                        <?php if ( $user_type === 'socio' ) : ?>
                            <a href="#"
                               class="aemi-btn-stripe js-stripe-link"
                               data-base-url="<?php echo esc_url( $stripe_socios_1 ); ?>"
                               data-email="<?php echo $data_email; ?>"
                               data-member-id="<?php echo $data_member_id; ?>"
                               data-full-name="<?php echo $data_full_name; ?>">
                                Comprar 1 unidad
                            </a>

                            <a href="#"
                               class="aemi-btn-stripe js-stripe-link"
                               data-base-url="<?php echo esc_url( $stripe_socios_2 ); ?>"
                               data-email="<?php echo $data_email; ?>"
                               data-member-id="<?php echo $data_member_id; ?>"
                               data-full-name="<?php echo $data_full_name; ?>">
                                Comprar 2 unidades
                            </a>
                        <?php elseif ( $user_type === 'no_socio' ) : ?>
                            <a href="#"
                               class="aemi-btn-stripe js-stripe-link"
                               data-base-url="<?php echo esc_url( $stripe_no_socios_1 ); ?>"
                               data-email="<?php echo $data_email; ?>"
                               data-member-id="<?php echo $data_member_id; ?>"
                               data-full-name="<?php echo $data_full_name; ?>">
                                Comprar 1 unidad 
                            </a>

                            <a href="#"
                               class="aemi-btn-stripe js-stripe-link"
                               data-base-url="<?php echo esc_url( $stripe_no_socios_2 ); ?>"
                               data-email="<?php echo $data_email; ?>"
                               data-member-id="<?php echo $data_member_id; ?>"
                               data-full-name="<?php echo $data_full_name; ?>">
                                Comprar 2 unidades
                            </a>
                        <?php endif; ?>
                    </div>
                    <p class="aemi-book-notice">
                        Al hacer clic en “Comprar” te llevaremos a Stripe. El email se pre-rellenará automáticamente
                        en la pasarela de pago si estás correctamente identificado.
                    </p>
                <?php else : ?>
                    <p class="aemi-book-notice">
                        Por favor, inicia sesión en el espacio profesional.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

   

</div><!-- .aemi-book-page -->

<script>
/**
 * JS: Construir la URL de Stripe en el momento del clic.
 * - Añade ?prefilled_email= al Payment Link.
 */
document.addEventListener('DOMContentLoaded', function () {
    var buttons = document.querySelectorAll('.js-stripe-link');

    buttons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            var baseUrl   = this.getAttribute('data-base-url');
            var email     = this.getAttribute('data-email') || '';
            var memberId  = this.getAttribute('data-member-id') || '';

            if (!baseUrl) {
                return;
            }

            try {
                var url = new URL(baseUrl);

                if (email) {
                    url.searchParams.set('prefilled_email', email);
                }

                if (memberId) {
                    // útil para reconciliar pagos vía webhooks
                    url.searchParams.set('client_reference_id', 'swpm-' + memberId);
                }

                window.location.href = url.toString();
            } catch (err) {
                // Fallback si URL() falla en algún navegador muy antiguo
                var finalUrl = baseUrl;
                var params = [];

                if (email) {
                    params.push('prefilled_email=' + encodeURIComponent(email));
                }
                if (memberId) {
                    params.push('client_reference_id=' + encodeURIComponent('swpm-' + memberId));
                }

                if (params.length) {
                    finalUrl += (baseUrl.indexOf('?') === -1 ? '?' : '&') + params.join('&');
                }

                window.location.href = finalUrl;
            }
        });
    });
});
</script>

<?php
get_footer();
