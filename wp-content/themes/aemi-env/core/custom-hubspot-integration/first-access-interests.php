<?php
/**
 * Envío a HubSpot de los intereses seleccionados en el modal de primer acceso
 * (paso 2, checkboxes de la taxonomía `aemi_tags`).
 *
 * Por qué un snippet aparte y no un mapeo más del plugin hubspot-mapper:
 * ese formulario no lleva campo email (el usuario ya está logueado cuando lo
 * rellena), y sus valores son IDs numéricos de término que no coinciden con
 * los valores internos (en inglés) de la propiedad de HubSpot. El plugin
 * genérico espera un email en el propio envío y no sabe traducir valores,
 * así que aquí se resuelve el email desde el usuario logueado y se traducen
 * los IDs antes de enviarlos, directamente contra MGMIT_HS_Contacts.
 *
 * Engancha `activecampaign_update_insterest`, la acción que ya dispara
 * AEMIFirstAccessLoginStepsModal::save_first_steps_information() al guardar
 * el paso 2 (core/first-access-login-steps-modal.php).
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('AEMI_HS_INTEREST_PROP')) {
    define('AEMI_HS_INTEREST_PROP', 'areas_of_interest');
}

/**
 * Traduce el id de término `aemi_tags` al valor interno esperado por la
 * propiedad de HubSpot. Decidido junto al cliente; ids sin mapeo se ignoran.
 *
 * @return array [term_id => valor interno HubSpot]
 */
function aemi_hs_interest_value_map() {
    return array(
        92  => 'Dermatology',
        104 => 'Clinical Cases',
        106 => 'Portuguese contents',
        105 => 'Content for pharmacies',
        96  => 'Respiratory Diseases',
        95  => 'Rheumatology',
        102 => 'Neurology',
        97  => 'Oncology',
        100 => 'Gastroenterology',
        99  => 'Paediatrics',
        98  => 'Cardiometabolic Health',
        101 => 'Mental health',
        91  => 'Viral reactivation',
        93  => 'Gynaecology',
        90  => 'Infectious diseases', // COVID-19
        94  => 'Infectious diseases', // Enfermedades de transmisión sexual
        // 107 (Otras categorías) sin mapeo: HubSpot no tiene esa opción en
        // `areas_of_interest` y rechaza toda la petición (HTTP 400) si se
        // envía un valor no permitido, aunque el resto sean válidos.
    );
}

/**
 * Envía a HubSpot los intereses del usuario tras guardar el paso 2 del modal.
 *
 * @param int $user_id
 * @return void
 */
function aemi_hs_send_first_access_interests($user_id) {
    $user_id = (int) $user_id;
    if ($user_id <= 0 || !class_exists('MGMIT_HS_Contacts')) {
        return;
    }

    $user = get_userdata($user_id);
    if (!$user || empty($user->user_email) || !is_email($user->user_email)) {
        return;
    }

    $term_ids = get_user_meta($user_id, AEMIFirstAccessLoginStepsModal::$user_meta_user_interest, true);
    if (!is_array($term_ids) || empty($term_ids)) {
        return;
    }

    $value_map = aemi_hs_interest_value_map();
    $hs_values = array();

    foreach ($term_ids as $term_id) {
        $term_id = (int) $term_id;
        if (isset($value_map[$term_id])) {
            $hs_values[] = $value_map[$term_id];
        }
    }

    $hs_values = array_values(array_unique($hs_values));
    if (empty($hs_values)) {
        return;
    }

    $ok = MGMIT_HS_Contacts::upsert($user->user_email, array(
        AEMI_HS_INTEREST_PROP => implode(';', $hs_values),
    ));

    // Log propio: MGMIT_HS_Contacts::log() solo escribe si WP_DEBUG está
    // activo (desactivado en este entorno), así que sin esto los fallos de
    // la API de HubSpot (token, propiedad inexistente, valor no permitido...)
    // pasan totalmente desapercibidos.
    if (!$ok) {
        error_log('[aemi-hs-interests] upsert falló para user_id=' . $user_id . ' email=' . $user->user_email . ' prop=' . AEMI_HS_INTEREST_PROP . ' values=' . implode(';', $hs_values));
    }
}
add_action('activecampaign_update_insterest', 'aemi_hs_send_first_access_interests', 20, 1);
