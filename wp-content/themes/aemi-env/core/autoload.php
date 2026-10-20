<?php

// Defines
require 'defines.php';

// Ajax
require 'ajax/autoload.php';

// CPT
require 'register-custom-post-types/autoload.php';
// TAXONOMIES
require 'register-custom-taxonomies/autoload.php';

// Others
require 'general-functions.php';
require 'general-functions-premium-user.php';
require 'admin-functions-and-helpers-metaboxs/autoload.php';
require 'custom-login.php';
require 'custom-admin-panel-appearance.php';
require 'disable-updates-wp.php';
require 'page-views.php';
require 'home-admin-fields-and-settings.php';
require 'pagination.php';

require 'redirect-users-not-logged-to-login.php';

// Auto redirect template pages
require 'template-redirect.php';

// Set auto locale by user acess
require 'auto-locale.php';

// Load templates contents
require 'templates/autoload.php';

// others actions
require 'first-access-login-steps-modal.php';
require 'custom-user-profile-image.php';
require 'favorites/autoload.php';
require 'customize-yoast_seo.php';
require 'shortcodes.php';

require 'interests/autoload.php';
require 'subscriptions/autoload.php';

require 'gamification-and-content-level/autoload.php';
require 'woocommerce/autoload.php';

//require 'import-users-google-spreadsheets/autoload.php';
require 'import-users-microsoft-excel/autoload.php';

require 'remove_gutemberg_default_metabox.php';

require 'custom-activecampaign-integration/autoload.php';

require 'custom-hubspot-integration/autoload.php';

// customize plugin freshdesk
require 'help-desk-with-freshdesk-functions.php';

require 'modal-reclaim-instruction-points.php';

require 'custom-updates-info-my-account-page.php';
require 'wikimi-page-general-actions-and-functions.php';

require 'custom-field-autor.php';
require 'user_last_access_update.php';