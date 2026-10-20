<?php

const AEMI_TEXT_DOMAIN = 'aemi';
require 'third-party/autoload.php';
require 'core/autoload.php';

/**
 * Add configs and theme support.
 */
function aemi_theme_support()
{
    // Add theme support
    add_theme_support('post-thumbnails');

    add_image_size('wikimi-thumbnail', 305, 205, true);

    add_theme_support(
        'custom-logo',
        [
            'height' => 120,
            'width' => 80,
            'flex-height' => true,
            'flex-width' => true,
        ]
    );

    add_theme_support('title-tag');

    add_theme_support(
        'html5',
        [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'script',
            'style',
            'navigation-widgets',
        ]
    );

    load_theme_textdomain(AEMI_TEXT_DOMAIN, get_template_directory().'/languages');
}

add_action('after_setup_theme', 'aemi_theme_support');

/**
 * Register navigation menus.
 */
function aemi_register_menu()
{
    register_nav_menus([
        'primary' => __('Top Menu', AEMI_TEXT_DOMAIN),
        'footer' => __('Footer Menu', AEMI_TEXT_DOMAIN),
        'footer_user_not_logged_in' => __('Footer Menu - User not logged in', AEMI_TEXT_DOMAIN),
        'mobile' => __('Mobile', AEMI_TEXT_DOMAIN),
    ]);
}

add_action('init', 'aemi_register_menu');

/**
 * Register styles and scripts.
 */
function aemi_register_styles_and_scripts()
{
    /*
     * Register Owl slider
     */
    wp_register_script('owl-carousel-min-js', get_aemi_url_assets_third_party_plugins('owl-carousel/owl.carousel.min.js'));
    wp_enqueue_script('owl-carousel-min-js');

    wp_register_style('owl-carousel-min-style', get_aemi_url_assets_third_party_plugins('owl-carousel/assets/owl.carousel.min.css'));
    wp_register_style('owl-carousel-min-default-style', get_aemi_url_assets_third_party_plugins('owl-carousel/assets/owl.theme.default.min.css'), ['owl-carousel-min-style']);
    wp_enqueue_style('owl-carousel-min-default-style');

    /*
     * SCRIPTS
     */
    wp_register_script('aemi_scripts', get_aemi_url_assets_js('functions.js'));
    wp_register_script('aemi_scripts', get_aemi_url_assets_js('common.min.js'));
    wp_enqueue_script('aemi_scripts');

    wp_enqueue_script('new-layout-v1-index', get_aemi_url_assets_js('../new-assets-v1/jquery/index.js'), ['jquery']);

    /*
     * STYLES
     */
    //wp_register_style('aemi_style', get_aemi_url_assets_css('style.min.css'));
    wp_register_style('aemi_theme_style', get_stylesheet_directory_uri().'/style.css');
    wp_register_style('aemi_style', get_aemi_url_assets_css('styles.css'));	
    wp_enqueue_style('aemi_style');
	wp_enqueue_style('aemi_theme_style');

    if (is_home() || is_front_page()) {
        wp_register_style('swiper-10-min-css', get_aemi_url_assets_third_party_plugins('swiper@10/swiper-bundle.min.css'), []);
        wp_enqueue_style('swiper-10-min-css');

        wp_register_script('swiper-10-min-js', get_aemi_url_assets_third_party_plugins('swiper@10/swiper-bundle.min.js'));
        wp_enqueue_script('swiper-10-min-js');
    }
}

add_action('wp_enqueue_scripts', 'aemi_register_styles_and_scripts');

/*
 * Admin Scripts
 */
add_filter('get_the_archive_title_prefix', '__return_empty_string');

function themename_post_formats_setup()
{
    add_theme_support('post-formats', ['video', 'audio', 'link', 'aside', 'quote']);
}
add_action('after_setup_theme', 'themename_post_formats_setup');

function aemi_set_post_view_user()
{
    $post_id = get_queried_object_id();
    $user = get_current_user_id();
    $meta_key = '_aemi_post_viewed';
    $meta = get_user_meta($user, $meta_key);

    if (is_singular('post') && is_user_logged_in()) {
        if (is_countable($meta) && count($meta) <= 0) {
            update_user_meta($user, $meta_key, serialize([]));
        }

        $unserialized = unserialize(get_user_meta($user, $meta_key)[0]);

        if (!in_array($post_id, $unserialized)) {
            $unserialized[] = $post_id;
        }

        $serialized = serialize($unserialized);

        update_user_meta($user, $meta_key, $serialized);
    }
}
add_action('template_redirect', 'aemi_set_post_view_user');

/*
 * Disable Autosave Posts Wordpress
 */
add_action('admin_init', function () {
    wp_deregister_script('autosave');
});

add_filter('show_admin_bar', '__return_false');

function aemi_remove_pages_from_search_page($query)
{
    if ($query->is_search) {
        $query->set('post_type', 'post');
    }

    return $query;
}
add_filter('pre_get_posts', 'aemi_remove_pages_from_search_page');

add_action( 'the_content', function( $content ){
    $content = strtr( $content, [ '//localhost/aemi_producao/' => '//prospace.aemi.es/']);
    return $content;
});

/*
 * Endpoint Zapier
 */
add_action('rest_api_init', function () {
    register_rest_route('goto-webinar/v1', '/register/', array(
        'methods' => 'POST',
        'callback' => 'register_user_for_webinar',
        'permission_callback' => '__return_true', // O usa validación más estricta si es necesario
    ));
});

function register_user_for_webinar(WP_REST_Request $request) {
    // Obtener los parámetros enviados por Zapier
    $email = sanitize_email($request->get_param('email'));
    $post_id = intval($request->get_param('webinar_post_id'));
    $webinar_id = intval($request->get_param('webinar_id'));
    $registration_data = $request->get_param('registration_data'); // Puede incluir el joinUrl, por ejemplo

    // Verificar si el usuario existe en WordPress
    $user = get_user_by('email', $email);
    if (!$user) {
        return new WP_REST_Response('Usuario no encontrado', 404);
    }

    // Obtener el ID del usuario
    $user_id = $user->ID;

    // Insertar la inscripción en los metadatos del usuario
    GotoUserWordPress::insertWebinarsRegisteredUser($user_id, $post_id, $webinar_id, $registration_data);

    return new WP_REST_Response('Inscripción completada', 200);
}

// DITS Integración ActiveCampaign a través de Webhook con Zapier
// Hook para nuevos registros de usuario
add_action('user_register', 'schedule_send_user_metadata_to_zapier', 10, 1);

// Hook para actualizaciones de usuario
add_action('profile_update', 'send_selected_user_metadata_to_zapier', 10, 1);

/**
 * Programar el envío de metadatos después del registro con un pequeño retraso.
 */
function schedule_send_user_metadata_to_zapier($user_id) {
    // Programar el envío con un retraso de 10 segundos
    wp_schedule_single_event(time() + 10, 'send_user_metadata_to_zapier_event', [$user_id]);
}

// Hook programado para enviar metadatos
add_action('send_user_metadata_to_zapier_event', 'send_selected_user_metadata_to_zapier');

/**
 * Enviar metadatos seleccionados a Zapier
 */
function send_selected_user_metadata_to_zapier($user_id) {
    $user = get_userdata($user_id);
    $meta = get_user_meta($user_id);

    // URL del webhook de Zapier 
    $zapier_webhook_url = 'https://hooks.zapier.com/hooks/catch/12898856/2sp3cl9/';

    // Seleccionar metadatos específicos para enviar
    $selected_meta = [
        'eres_sanitario'         => $meta['eres_sanitario_1'][0] ?? '-',
        'profesion'              => $meta['profesion_1'][0] ?? '',
		'especialidad'           => $meta['especialidad_del_medico_1'][0] ?? '',
        'cual_es_profesion'      => $meta['cual_es_profesion_1'][0] ?? '',
        'colegiado'              => $meta['colegiado_1'][0] ?? '-',
        'telefono'               => $meta['telefono_1'][0] ?? '-',
        'quiero_estar_informado' => $meta['quiero_estar_informado_1'][0] ?? '-',
        'pais'                   => $meta['pais_1'][0] ?? '-',
        'direccion_postal'       => $meta['direccion_postal_25'][0] ?? '',
    ];

$selected_meta = array_filter(
    $selected_meta,
    function($valor){
        return $valor !== '';
    }
);

    $data = [
        'ID'          => $user->ID,
        'user_email'  => $user->user_email,
        'first_name'  => $user->first_name,
        'last_name'   => $user->last_name,
        'role'        => implode(', ', $user->roles),
        'meta_data'   => $selected_meta,
    ];

    // Enviar datos al webhook de Zapier
    $response = wp_remote_post($zapier_webhook_url, [
        'body'    => json_encode($data),
        'headers' => ['Content-Type' => 'application/json'],
        'method'  => 'POST',
    ]);

    $log_file = WP_CONTENT_DIR . '/zapier-integration.log';

    if (is_wp_error($response)) {
        $error_message = '[' . date('Y-m-d H:i:s') . '] Error al enviar metadatos a Zapier: ' . $response->get_error_message() . PHP_EOL;
        error_log($error_message, 3, $log_file);
    } else {
        $success_message = '[' . date('Y-m-d H:i:s') . '] Metadatos enviados a Zapier para el usuario: ' . $user->user_email . PHP_EOL;
        error_log($success_message, 3, $log_file);
    }
}



function get_interest_label($interest_id) {
    $interest_map = [
        '92'  => 'interest__afecciones-dermatológicas',
        '104' => 'interest__casos-clinicos',
        '106' => 'interest__contenidos-en-portugues',
        '105' => 'interest__contenidos-para-farmacias',
        '90'  => 'interest__covid-19',
        '94'  => 'interest__enfermedades-de-transmision-sexual',
        '96'  => 'interest__enfermedades-respiratorias',
        '95'  => 'interest__enfermedades-reumaticas',
        '102' => 'interest__neurologia',
        '107' => 'interest__otras-categorias',
        '97'  => 'interest__paciente-oncologico',
        '100' => 'interest__patologia-digestiva',
        '99'  => 'interest__pediatria',
        '98'  => 'interest__salud-cardiometabolica',
        '101' => 'interest__salud-mental',
        '91'  => 'interest__trastornos-asociados-a-la-reactivacion-viral',
        '93'  => 'interest__trastornos-ginecologicos',
    ];
    return isset($interest_map[$interest_id]) ? $interest_map[$interest_id] : $interest_id;
}

/**
 * Hook para registrar el último acceso del usuario y enviar datos a Zapier al iniciar sesión.
 */
add_action('wp_login', 'send_last_access_to_zapier', 10, 2);

function send_last_access_to_zapier($user_login, $user) {
    global $wpdb, $aemiHelpLevelUsersClassLoaded;

    $last_access = current_time('mysql');
    update_user_meta($user->ID, 'last_access', $last_access);

    $membership = $wpdb->get_row($wpdb->prepare(
        "SELECT membership_level FROM {$wpdb->prefix}swpm_members_tbl WHERE email = %s",
        $user->user_email
    ));

    $membership_level = 'N/A';
    if ($membership && isset($membership->membership_level)) {
        $levels = [
            2 => 'Gratis',
            3 => 'Premium',
        ];
        $membership_level = isset($levels[$membership->membership_level]) ? $levels[$membership->membership_level] : 'Desconocido';
    }

    $interests_raw = get_user_meta($user->ID, '_current_user_step_insterest', true);
    $interests = 'N/A';
    if ($interests_raw) {
        $interests_data = maybe_unserialize($interests_raw);
        if (is_array($interests_data)) {
            $interest_labels = array_map('get_interest_label', $interests_data);
            $interests = implode(', ', $interest_labels);
        } else {
            $interests = get_interest_label($interests_data);
        }
    }

    $score = $aemiHelpLevelUsersClassLoaded->getSpecificUserScore($user->ID);
    $score = $score !== '' ? $score : '0';

    $zapier_webhook_url = 'https://hooks.zapier.com/hooks/catch/12898856/2sp3cl9/';

    $data = [
        'ID'               => $user->ID,
        'user_email'       => $user->user_email,
        'first_name'       => $user->first_name,
        'last_name'        => $user->last_name,
        'last_access'      => $last_access,
        'membership_level' => $membership_level,
        'interests'        => $interests,
        'score'            => $score,
    ];

    $response = wp_remote_post($zapier_webhook_url, [
        'body'    => json_encode($data),
        'headers' => ['Content-Type' => 'application/json'],
        'method'  => 'POST',
    ]);

    $log_file = WP_CONTENT_DIR . '/zapier-integration.log';
    if (is_wp_error($response)) {
        $error_message = '[' . date('Y-m-d H:i:s') . '] Error al enviar datos a Zapier: ' . $response->get_error_message() . PHP_EOL;
        error_log($error_message, 3, $log_file);
    } else {
        $response_body = wp_remote_retrieve_body($response);
        $success_message = '[' . date('Y-m-d H:i:s') . '] Datos enviados a Zapier para el usuario: ' . $user->user_email .
                           ' con nivel de membresía: ' . $membership_level .
                           ', intereses: ' . $interests .
                           ' y puntuación: ' . $score .
                           ' - Respuesta: ' . $response_body . PHP_EOL;
        error_log($success_message, 3, $log_file);
    }
}

// Redirección tras el login
add_action('wp_login', 'aemi_final_login_redirect', 10, 2);
function aemi_final_login_redirect($user_login, $user) {
	
    if (in_array('administrator', (array) $user->roles, true)) {
        return;
    }

    if (isset($_REQUEST['redirect_to']) && !empty($_REQUEST['redirect_to'])) {
        $redirect_url = esc_url_raw($_REQUEST['redirect_to']);
    } else {
        $referrer = wp_get_referer();

        if (
            !$referrer || 
            strpos($referrer, '/mi-cuenta') !== false || 
            trailingslashit($referrer) === trailingslashit(home_url('/'))
        ) {
            $redirect_url = home_url('/home');
        } else {
            $redirect_url = $referrer;
        }
    }

    wp_redirect($redirect_url);
    exit;
}

add_action( 'wppb_register_success', 'pb_force_redirect_after_registration', 10, 3 );
function pb_force_redirect_after_registration( $http_request, $form_args, $user_id ) {

    $redirect_url = home_url('/mi-cuenta/');

    wp_redirect( $redirect_url );
    exit;
}

add_filter( 'manage_users_columns', 'my_manage_users_columns' );
function my_manage_users_columns( $columns ) {
    $columns['registration_date'] = 'Registered';
    $columns['last_login_date'] = 'Last Logged In';
    return $columns;
}

add_filter( 'manage_users_custom_column', 'my_manage_users_custom_column', 10, 3 );
function my_manage_users_custom_column( $row_output, $column_id_attr, $user ) {
    $date_format = 'Y/m/d \a\t g:i a';
    $d1 = 0;
    switch ( $column_id_attr ) {
        case 'registration_date':
            $d1 = strtotime(get_userdata($user)->user_registered);
            break;
        case 'last_login_date':
            $session_tokens = get_user_meta( $user, 'session_tokens', true );
            if (!empty($session_tokens)) {
                $d1 = max(array_column(array_values($session_tokens),'login'));
            }
            break;
        default:
    }
    if ($d1 > 0) {
        $d2 = new DateTime("@$d1");
        return $d2->setTimezone(wp_timezone())->format($date_format);
    }
    return $row_output;
}

add_filter( 'manage_users_sortable_columns', 'my_manage_users_sortable_columns' );
function my_manage_users_sortable_columns( $columns ) {
    return wp_parse_args( array( 'registration_date' => 'registered' ), $columns );
}


add_action( 'wp_footer', function () {
    ?>
    <script>
    (function () {
        function init() {
            if (!document.body.classList.contains('page-template-template-page-register-php')) return;

            var form = document.getElementById('wppb-register-user');
            if (!form) return;

            var pharmacist = document.getElementById('pharmacist_27');
            if (!pharmacist) return;

            // Profile Builder (conditional logic) muestra/oculta y resetea especialidad_principal
            // al cambiar profesion_1, asi que marcamos tras esa logica, con reintentos.
            function check() {
                if (!pharmacist.checked) {
                    pharmacist.checked = true;
                    pharmacist.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }

            function sync() {
                var sel = form.querySelector('input[name="profesion_1"]:checked');
                if (!sel || sel.value !== 'Pharmacies') return;
                [0, 100, 300, 800].forEach(function (ms) { setTimeout(check, ms); });
            }

            form.addEventListener('change', function (e) {
                if (e.target.name === 'profesion_1') sync();
            });
            sync();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
    </script>
    <?php
} );
