<?php 

if( !class_exists('AemiCustomActiveCampaignIntegrationBase') ){
    class AemiCustomActiveCampaignIntegrationBase
    {
        public $input_prefix = '__aemi-c-a-c-i__';
        public $interest__prefix = 'interest__';

        protected function get_data(){
            return get_option( $this->input_prefix, array() );
        }
    }
}