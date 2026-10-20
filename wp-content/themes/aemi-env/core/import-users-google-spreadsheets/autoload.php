<?php

if (strpos($_SERVER['REQUEST_URI'], '/wp-json/aemi-google-sheets-import-user/v1/import-users/') !== false) {

    // DEFINA ISSO NO WP-CONFIG SE QUISER HABILITAR A OPÇÃO DE IMPORTAR USUÁRIOS
    // define( 'AEMI_GOOGLE_SHEETS_IMPORT_USER_ENABLED', true );
    if (defined('AEMI_GOOGLE_SHEETS_IMPORT_USER_ENABLED') && AEMI_GOOGLE_SHEETS_IMPORT_USER_ENABLED) {
        require 'vendor/autoload.php';
    }
}
require 'general-class-google-sheets-to-extends.php';
require 'admin-page.php';
require 'admin-user-profile.php';
require 'rest-api.php';
 