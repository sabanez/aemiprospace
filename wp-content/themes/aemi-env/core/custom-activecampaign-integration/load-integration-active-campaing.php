<?php 


$ACTIVE_CAMPAIGN_LOADED = []; 

function aemi_register_active_campaign_loaded() {
    require_once("activecampaign/ActiveCampaign.class.php");

    global $ACTIVE_CAMPAIGN_LOADED;

    if( defined('ACTIVECAMPAIGN_URL') && defined('ACTIVECAMPAIGN_API_KEY') ){
        $ac = new ActiveCampaign(ACTIVECAMPAIGN_URL, ACTIVECAMPAIGN_API_KEY);
        $ACTIVE_CAMPAIGN_LOADED[0] = $ac;
    } else {
        $ACTIVE_CAMPAIGN_LOADED[0] = false;
    }
}

function aemi_get_instance_active_campaign() {
    global $ACTIVE_CAMPAIGN_LOADED;

    if( !isset( $ACTIVE_CAMPAIGN_LOADED[0] ) ){
        aemi_register_active_campaign_loaded();
    }

    return $ACTIVE_CAMPAIGN_LOADED[0];
}
