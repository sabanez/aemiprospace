<?php 

if( !class_exists( 'AemiCustomActiveCampaignIntegration' ) ){
    class AemiCustomActiveCampaignIntegration extends AemiCustomActiveCampaignIntegrationBase
    {
        public $menu_slug = 'aemi-custom-active-campaign-integration';
        public $tabs_and_filter = array();
        public $lists_in_activecampaing = array();
        static $nonce_action = 'lwpDvPhiEIt6EyMIJRG7';
        static $nonce_name = 'TTbDq4I0FaW0d0mksXgn';

        public function __construct(){
    		add_action( 'admin_menu', array( $this, 'register_admin_page' ) );
        }

        private function get_level_swpm_membership(){
            global $wpdb;
            $query  = 'SELECT alias, id FROM ' . $wpdb->prefix . 'swpm_membership_tbl WHERE id != 1';
            $levels = $wpdb->get_results( $query );

            return $levels;
        }

        public function register_admin_page(){
            add_submenu_page( 
                'options-general.php', __( 'ActiveCampaign', AEMI_TEXT_DOMAIN ), __( 'ActiveCampaign', AEMI_TEXT_DOMAIN ), 'manage_options',  $this->menu_slug, array( $this, 'page')
            );
        }

        /**
         * Detect and save custom field user admin
         * 
         * @param Int $user_id
         */
        public function save(){
    
          if( isset( $_POST[self::$nonce_name] ) && wp_verify_nonce( $_POST[self::$nonce_name], self::$nonce_action ) ){
            if( isset( $_POST[$this->input_prefix] ) && $_POST[$this->input_prefix] ){
                foreach( $_POST[$this->input_prefix] as $tab_key => $tab_value ){
                    if( isset( $_POST[$this->input_prefix][$tab_key] ) ){
                        foreach ( $_POST[$this->input_prefix][$tab_key] as $key => $value) {
                            if( isset( $_POST[$this->input_prefix][$tab_key][$key]['tags_add'] ) && $_POST[$this->input_prefix][$tab_key][$key]['tags_add'] ){

                                if( function_exists('mb_strtolower') ){
                                    $_POST[$this->input_prefix][$tab_key][$key]['tags_add'] = mb_strtolower( trim( $_POST[$this->input_prefix][$tab_key][$key]['tags_add'] ) );
                                } else{
                                    $_POST[$this->input_prefix][$tab_key][$key]['tags_add'] = strtolower( trim( $_POST[$this->input_prefix][$tab_key][$key]['tags_add'] ) );
                                }
                            }
                        }
                    }
                }
                update_option( $this->input_prefix, $_POST[$this->input_prefix] );
                echo '<div id="message" class="notice notice-success"><p>' . __( 'Data updated successfully!', AEMI_TEXT_DOMAIN ) . '</p></div>';
            }
          }
        }    

        public function page(){

            $this->tabs_and_filter = array(
                'user-registration' => array(
                    array(
                        'legend' => 'When registering the user',
                        'action' => 'user_register'
                    ),
                    array(
                        'legend' => 'By registering the user and being a pharmacist',
                        'action' => 'user_register__being_farmaceutico'
                    ),
                    array(
                        'legend' => 'When registering user and being a doctor',
                        'action' => 'user_register__being_medico'
                    ),
                    
                ),
                'plan-upgrade' => array(
                    array(
                        'legend' => 'Plan canceled or expired',
                        'action' => 'activecampaign_swpm_cancel_plan'
                    ),
                ),
                'update-user-level' => array(),
                'mis-suscripciones' => array()
            );

            global $aemiHelpLevelUsersClassLoaded;
            $getLevels = $aemiHelpLevelUsersClassLoaded->getLevels();

            foreach ( $getLevels as $levels ) {
                $this->tabs_and_filter['update-user-level'][] = array(
                    'legend' =>  __( 'When leveling up to:', AEMI_TEXT_DOMAIN ) . ' ' . $levels->name,
                    'action' => 'update_user_level_' . $levels->term_id
                );
            }

            $get_level_swpm_membership = $this->get_level_swpm_membership();

            if( $get_level_swpm_membership ){
                foreach ( $get_level_swpm_membership as $levels ) {
                    $this->tabs_and_filter['plan-upgrade'][] = array(
                        'legend' =>  __( 'User when selecting the plan:', AEMI_TEXT_DOMAIN ) . ' ' . $levels->alias,
                        'action' => 'activecampaign_swpm_update_plan_' . $levels->id
                    );
                }
    
            }

            $aemi_subscriptions_options = aemi_subscriptions_options();

            if( $aemi_subscriptions_options ){
                foreach ( $aemi_subscriptions_options as $sub_id => $sub_options ) {
                    $this->tabs_and_filter['mis-suscripciones'][] = array(
                        'legend' =>  __( 'When selecting:', AEMI_TEXT_DOMAIN ) . ' ' . $sub_options['name'],
                        'action' => 'aemi_customer_subscriptions__'.$sub_id.'_active'
                    );
                    $this->tabs_and_filter['mis-suscripciones'][] = array(
                        'legend' =>  __( 'Unmarking:', AEMI_TEXT_DOMAIN ) . ' ' . $sub_options['name'],
                        'action' => 'aemi_customer_subscriptions__'.$sub_id.'_cancel'
                    );
                }
    
            }

            $this->lists_in_activecampaing = AemiActiveCampaignIntegration::getAllListsActiveCampaign();


            ?>
                <style>
                    .active-wrap{
                        margin-top: 30px;
                        border-radius: 15px;
                        border: 1px solid #eee;
                        background: #fff;
                        padding: 25px;
                        width: 100%;
                        box-sizing: border-box;
                    }

                    .active-wrap__description{
                        margin-bottom: 25px;
                        font-size: 18px;
                        text-align: center;
                        color: #666;
                    }

                    .active-wrap__header {
                        display: flex;
                        width: 100%;
                    }

                    .active-wrap__header ul{
                        display: flex;
                        width: 100%;
                        margin: 0;
                        padding: 0;
                    }

                    .active-wrap__header li{
                        display: flex;
                        margin: 0;
                        padding: 0;
                    }
                    
                    .active-wrap__header li label {
                        display: flex;
                        padding: 20px;
                        border-radius: 10px 10px 0 0;
                        background: #f7f7f7;
                        margin-right: 5px;
                        border: 1px solid #eee;
                    }

                    .tab-active-section{
                        display: none!important;
                    }

                    .active-wrap__section-container{
                        display: none!important;
                        border: 1px solid #eee;
                        padding: 15px;
                    }
                    .tab-active-section[data-id="user-registration"]:checked ~  .active-wrap__sections .active-wrap__section-container[data-id="user-registration"],
                    .tab-active-section[data-id="plan-upgrade"]:checked ~  .active-wrap__sections .active-wrap__section-container[data-id="plan-upgrade"],
                    .tab-active-section[data-id="update-user-level"]:checked ~  .active-wrap__sections .active-wrap__section-container[data-id="update-user-level"],
                    .tab-active-section[data-id="mis-suscripciones"]:checked ~  .active-wrap__sections .active-wrap__section-container[data-id="mis-suscripciones"],
                    .tab-active-section[data-id="insterest"]:checked ~  .active-wrap__sections .active-wrap__section-container[data-id="insterest"],
                    .tab-active-section[data-id="last_access_date_notification"]:checked ~  .active-wrap__sections .active-wrap__section-container[data-id="last_access_date_notification"]{
                        display: block!important;
                    }
                    
                    .tab-active-section[data-id="user-registration"]:checked ~  .active-wrap__header label[for="user-registration"],
                    .tab-active-section[data-id="plan-upgrade"]:checked ~  .active-wrap__header label[for="plan-upgrade"],
                    .tab-active-section[data-id="update-user-level"]:checked ~  .active-wrap__header label[for="update-user-level"],
                    .tab-active-section[data-id="mis-suscripciones"]:checked ~  .active-wrap__header label[for="mis-suscripciones"],
                    .tab-active-section[data-id="insterest"]:checked ~  .active-wrap__header label[for="insterest"],
                    .tab-active-section[data-id="last_access_date_notification"]:checked ~ .active-wrap__header label[for="last_access_date_notification"]{
                        background: var( --default-blue-color );
                        color: #fff;
                    }

                    .active-wrap__section-box{
                        border: 1px solid #f7f7f7;
                        padding: 15px;
                    }

                    .active-wrap__section-box + .active-wrap__section-box{
                        border-top: none;
                    }

                    .active-wrap__section-box:nth-child(2n+1){
                        background: #fafcfd;
                    }

                    .active-wrap__section-box__title{
                        font-weight: bold;
                        font-size: 16px;
                        margin: 0 0 15px;
                    }

                    .active-wrap__section-box-inner{
                        background: #eee;
                        padding: 15px 0;
                        display: flex;
                    }

                    .active-wrap__section-box-inner-b{
                        flex: 1;
                        margin: 0 15px;
                        background: #fff;
                        box-shadow: 0 0 13px -10px rgb(0 0 0 / 50%);
                        padding: 15px;
                        border-radius: 5px;
                        display: flex;
                        width: 25%;
                        flex-direction: column;
                    }

                    .active-wrap__section-box-inner-b__title{
                        margin-top: 0;
                        font-weight: bold;
                    }

                    .active-wrap__section-box-inner-b__list{
                        background: #f7f7f7;
                        padding: 2px 10px;
                        border: 1px solid #eee;
                    }

                    .active-wrap__section-box-inner-b__tag{
                        background: #f7f7f7;
                        padding: 10px 10px;
                        border: 1px solid #eee;
                        flex: 1;
                    }

                    .active-wrap__section-box-inner-b__list ul{
                        padding: 0;
                        margin: 0;
                    }

                    .active-wrap__section-box-inner-b__list ul li{
                        padding: 8px 0;
                        margin: 0;
                        display: flex;
                    }

                    .active-wrap__section-box-inner-b__list ul li + li{
                        border-top: 1px solid #eee;
                    }

                    /**
                     * The MIT License (MIT)
                     * Copyright (c) 2014 Jason Miller
                     * https://github.com/developit/tags-input/
                    */
                    .tags-input{display:inline-block;padding:0 2px;background:#FFF;border:1px solid #CCC;width:16em;border-radius:2px;box-shadow:inset 0 1px 2px rgba(0,0,0,.1)}.tags-input .tag{display:inline-block;background:#EEE;color:#444;padding:0 4px;margin:2px;border:1px solid #CCC;border-radius:2px;font:inherit;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;cursor:pointer;transition:all .1s ease}.tags-input .tag.selected{background-color:#777;border-color:#777;color:#EEE}.tags-input .tag.dupe{-webkit-transform:scale3d(1.2,1.2,1.2);transform:scale3d(1.2,1.2,1.2);background-color:#FCC;border-color:#700}.tags-input input{-webkit-appearance:none!important;-moz-appearance:none!important;appearance:none!important;display:inline-block!important;padding:3px;margin:0!important;background:0 0!important;border:none!important;box-shadow:none!important;font:inherit!important;font-size:100%!important;outline:0!important}.tags-input .selected~input{opacity:.3}

                    .tags-input{
                        width: 100%;
                    }

                    .tags-input .tag{
                        background: #045884;
                        color: #fff;
                        border-radius: 5px;
                        border: none;
                        padding: 5px 7px;
                    }
                    
                    .tags-input * {
                        text-transform: lowercase;
                    }

                    .active-wrap__section-container[data-id="insterest"] table {
                        border-collapse: collapse;
                    }
                    
                    .active-wrap__section-container[data-id="insterest"] table td,
                    .active-wrap__section-container[data-id="insterest"] table th{
                        padding: 15px;
                        border: 1px solid #ddd;
                    }

                    input[type="tags"]{
                        text-transform: lowercase; 
                    }
                </style>

                <script>
                    window.addEventListener('load', function(){
                        /**
                         * The MIT License (MIT)
                         * Copyright (c) 2014 Jason Miller
                         * https://github.com/developit/tags-input/
                         */
                        (function(global,factory){if(typeof define === 'function' && define.amd){define(['exports','module'],factory);}else if(typeof exports !== 'undefined' && typeof module !== 'undefined'){factory(exports,module);}else {var mod={exports:{}};factory(mod.exports,mod);global.tagsInput = mod.exports;}})(this,function(exports,module){'use strict';module.exports = tagsInput;var BACKSPACE=8,TAB=9,ENTER=13,LEFT=37,RIGHT=39,DELETE=46,COMMA=188;var SEPERATOR=',';var COPY_PROPS='placeholder pattern spellcheck autocomplete autocapitalize autofocus accessKey accept lang minLength maxLength required'.split(' ');function tagsInput(input){function createElement(type,name,text,attributes){var el=document.createElement(type);if(name)el.className = name;if(text)el.textContent = text;for(var key in attributes) {el.setAttribute('data-' + key,attributes[key]);}return el;}function $(selector,all){return all === true?Array.prototype.slice.call(base.querySelectorAll(selector)):base.querySelector(selector);}function getValue(){return $('.tag',true).map(function(tag){return tag.textContent;}).concat(base.input.value || []).join(SEPERATOR);}function setValue(value){$('.tag',true).forEach(function(t){return base.removeChild(t);});savePartialInput(value);}function save(){input.value = getValue();input.dispatchEvent(new Event('change'));}function addTag(text){if(~text.indexOf(SEPERATOR))text = text.split(SEPERATOR);if(Array.isArray(text))return text.forEach(addTag);var tag=text && text.trim();if(!tag)return false;if(!input.getAttribute('duplicates')){var _ret=(function(){var exisingTag=$('[data-tag="' + tag + '"]');if(exisingTag){exisingTag.classList.add('dupe');setTimeout(function(){return exisingTag.classList.remove('dupe');},100);return {v:false};}})();if(typeof _ret === 'object')return _ret.v;}base.insertBefore(createElement('span','tag',tag,{tag:tag}),base.input);}function select(el){var sel=$('.selected');if(sel)sel.classList.remove('selected');if(el)el.classList.add('selected');}function setInputWidth(){var last=$('.tag',true).pop(),w=base.offsetWidth;if(!w)return;base.input.style.width = Math.max(w - (last?last.offsetLeft + last.offsetWidth:5) - 5,w / 4) + 'px';}function savePartialInput(value){if(typeof value !== 'string' && !Array.isArray(value)){value = base.input.value;}if(addTag(value) !== false){base.input.value = '';save();setInputWidth();}}function refocus(e){if(e.target.classList.contains('tag'))select(e.target);if(e.target === base.input)return select();base.input.focus();e.preventDefault();return false;}var base=createElement('div','tags-input'),sib=input.nextSibling;input.parentNode[sib?'insertBefore':'appendChild'](base,sib);input.style.cssText = 'position:absolute;left:0;top:-99px;width:1px;height:1px;opacity:0.01;';input.tabIndex = -1;base.input = createElement('input');base.input.setAttribute('type','text');COPY_PROPS.forEach(function(prop){if(input[prop] !== base.input[prop]){base.input[prop] = input[prop];try{delete input[prop];}catch(e) {}}});base.appendChild(base.input);input.addEventListener('focus',function(){base.input.focus();});base.input.addEventListener('focus',function(){base.classList.add('focus');select();});base.input.addEventListener('blur',function(){base.classList.remove('focus');select();savePartialInput();});base.input.addEventListener('keydown',function(e){var el=base.input,key=e.keyCode || e.which,selectedTag=$('.tag.selected'),pos=el.selectionStart === el.selectionEnd && el.selectionStart,last=$('.tag',true).pop();setInputWidth();if(key === ENTER || key === COMMA || key === TAB){if(!el.value && key !== COMMA)return;savePartialInput();}else if(key === DELETE && selectedTag){if(selectedTag.nextSibling !== base.input)select(selectedTag.nextSibling);base.removeChild(selectedTag);setInputWidth();save();}else if(key === BACKSPACE){if(selectedTag){select(selectedTag.previousSibling);base.removeChild(selectedTag);setInputWidth();save();}else if(last && pos === 0){select(last);}else {return;}}else if(key === LEFT){if(selectedTag){if(selectedTag.previousSibling){select(selectedTag.previousSibling);}}else if(pos !== 0){return;}else {select(last);}}else if(key === RIGHT){if(!selectedTag)return;select(selectedTag.nextSibling);}else {return select();}e.preventDefault();return false;});base.input.addEventListener('input',function(){input.value = getValue();input.dispatchEvent(new Event('input'));});base.input.addEventListener('paste',function(){return setTimeout(savePartialInput,0);});base.addEventListener('mousedown',refocus);base.addEventListener('touchstart',refocus);base.setValue = setValue;base.getValue = getValue;savePartialInput(input.value);}tagsInput.enhance = tagsInput.tagsInput = tagsInput;});

                        
                        [].forEach.call(document.querySelectorAll('input[type="tags"]'), tagsInput);
                    });
                </script>
                <div class="wrap">
                    <?php
                        $this->save();
                        $data = $this->get_data();
                    ?>
                    <h2><?php echo  __( 'Settings ActiveCampaign', AEMI_TEXT_DOMAIN ); ?></h2>

                    <form class="active-wrap" method="POST">
                        <?php
                            echo wp_nonce_field( self::$nonce_action, self::$nonce_name );
                        ?>
                        <p class="active-wrap__description">
                            <?php echo  __( 'Configure how you want user actions on the system to behave with the ActiveCampaign integration.', AEMI_TEXT_DOMAIN ); ?>
                        </p>

                        <input name="<?php echo $this->input_prefix; ?>tab_active_section" type="radio" id="user-registration" data-id="user-registration" checked="" class="tab-active-section" />
                        <input name="<?php echo $this->input_prefix; ?>tab_active_section" type="radio" id="plan-upgrade" data-id="plan-upgrade" class="tab-active-section" />
                        <input name="<?php echo $this->input_prefix; ?>tab_active_section" type="radio" id="update-user-level" data-id="update-user-level" class="tab-active-section" />
                        <input name="<?php echo $this->input_prefix; ?>tab_active_section" type="radio" id="mis-suscripciones" data-id="mis-suscripciones" class="tab-active-section" />
                        <input name="<?php echo $this->input_prefix; ?>tab_active_section" type="radio" id="insterest" data-id="insterest" class="tab-active-section" />
                        <input name="<?php echo $this->input_prefix; ?>tab_active_section" type="radio" id="last_access_date_notification" data-id="last_access_date_notification" class="tab-active-section" />

                        <div class="active-wrap__header">
                            <ul>
                                <li>
                                    <label for="user-registration"><?php echo  __( 'User registration', AEMI_TEXT_DOMAIN ); ?></label>
                                </li>
                                <li>
                                    <label for="plan-upgrade"><?php echo  __( 'Plan upgrade', AEMI_TEXT_DOMAIN ); ?></label>
                                </li>
                                <li>
                                    <label for="update-user-level"><?php echo  __( 'Update user level', AEMI_TEXT_DOMAIN ); ?></label>
                                </li>
                                <li>
                                    <label for="mis-suscripciones"><?php echo  __( 'Mis suscripciones (Opciones)', AEMI_TEXT_DOMAIN ); ?></label>
                                </li>
                                <li>
                                    <label for="insterest"><?php echo  __( 'Interest', AEMI_TEXT_DOMAIN ); ?></label>
                                </li>
                                <li>
                                    <label for="last_access_date_notification"><?php echo  __( 'Last access date notification', AEMI_TEXT_DOMAIN ); ?></label>
                                </li>
                            </ul>
                        </div>

                        <div class="active-wrap__sections">
                            <?php foreach ( $this->tabs_and_filter as $tab_key => $tabs ): ?>
                                <div class="active-wrap__section-container" data-id="<?php echo esc_attr( $tab_key ); ?>">

                                    <?php foreach ( $tabs as $key => $value ): ?>
                                        <div class="active-wrap__section-box">
                                            <p class="active-wrap__section-box__title"><?php echo __($value['legend'], AEMI_TEXT_DOMAIN ); ?></p>

                                            <div class="active-wrap__section-box-inner">
                                                <div class="active-wrap__section-box-inner-b">
                                                    <p class="active-wrap__section-box-inner-b__title"><?php echo __( 'Which list to add the user:', AEMI_TEXT_DOMAIN ); ?></p>

                                                    <div class="active-wrap__section-box-inner-b__list">
                                                        <ul>
                                                        <?php foreach( $this->lists_in_activecampaing as $list ): ?>
                                                            <li>
                                                                <label>
                                                                    <input
                                                                    <?php
                                                                        if( isset( $data[$tab_key][$value['action']]['list_add'] ) && in_array( $list['id'], $data[$tab_key][$value['action']]['list_add'] ) ){
                                                                            echo 'checked="" ';
                                                                        }
                                                                    ?>
                                                                    type="checkbox" name="<?php echo $this->input_prefix; ?>[<?php echo $tab_key; ?>][<?php echo $value['action']; ?>][list_add][]" value="<?php echo $list['id']; ?>" />
                                                                    <span><?php echo $list['name']; ?></span>
                                                                </label>
                                                            </li>
                                                        <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="active-wrap__section-box-inner-b">
                                                    <p class="active-wrap__section-box-inner-b__title"><?php echo __( 'Which list to remove user:', AEMI_TEXT_DOMAIN ); ?></p>
                                                    
                                                    <div class="active-wrap__section-box-inner-b__list">
                                                        <ul>
                                                        <?php foreach( $this->lists_in_activecampaing as $list ): ?>
                                                            <li>
                                                                <label>
                                                                    <input
                                                                    <?php
                                                                        if( isset( $data[$tab_key][$value['action']]['list_remove'] ) && in_array( $list['id'], $data[$tab_key][$value['action']]['list_remove'] ) ){
                                                                            echo 'checked="" ';
                                                                        }
                                                                    ?>
                                                                    type="checkbox" name="<?php echo $this->input_prefix; ?>[<?php echo $tab_key; ?>][<?php echo $value['action']; ?>][list_remove][]" value="<?php echo $list['id']; ?>" />
                                                                    <span><?php echo $list['name']; ?></span>
                                                                </label>
                                                            </li>
                                                        <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="active-wrap__section-box-inner-b">
                                                    <p class="active-wrap__section-box-inner-b__title"><?php echo __( 'Add tags to user:', AEMI_TEXT_DOMAIN ); ?></p>

                                                    <div class="active-wrap__section-box-inner-b__tag">
                                                        <input type="tags" name="<?php echo $this->input_prefix; ?>[<?php echo $tab_key; ?>][<?php echo $value['action']; ?>][tags_add]" value="<?php
                                                            if( isset( $data[$tab_key][$value['action']]['tags_add'] ) ){
                                                                echo esc_attr( $data[$tab_key][$value['action']]['tags_add'] );
                                                            }
                                                        ?>" />
                                                    </div>
                                                </div>
                                                <div class="active-wrap__section-box-inner-b">
                                                    <p class="active-wrap__section-box-inner-b__title"><?php echo __( 'Remove User Tags:', AEMI_TEXT_DOMAIN ); ?></p>
                                                    
                                                    <div class="active-wrap__section-box-inner-b__tag">
                                                        <input type="tags" name="<?php echo $this->input_prefix; ?>[<?php echo $tab_key; ?>][<?php echo $value['action']; ?>][tags_remove]" value="<?php
                                                            if( isset( $data[$tab_key][$value['action']]['tags_remove'] ) ){
                                                                echo esc_attr( $data[$tab_key][$value['action']]['tags_remove'] );
                                                            }
                                                        ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>

                            <div class="active-wrap__section-container" data-id="insterest">
                                <div class="active-wrap__section-box">
                                    <p><?php echo __( 'Interests are automatically added and removed via tags whenever the user refreshes.', AEMI_TEXT_DOMAIN ) ?></p>
                                    <p><?php echo __( 'Check below the name and tag that will be added to the user when selecting interests:', AEMI_TEXT_DOMAIN ) ?></p>

                                    <?php  
                                        $interests = get_interest_list_for_users( [], ['slug']);
                                        echo '<table>';
                                        echo '<thead>';
                                            echo '<tr>
                                                <th>' . __( 'Interest', AEMI_TEXT_DOMAIN ) . '</th>
                                                <th>' . __( 'Tag', AEMI_TEXT_DOMAIN ) . '</th>
                                            </tr>';
                                        echo '</thead>';
                                        echo '<tbody>';
                                        foreach ( $interests as $interest_key => $interest_value ) {
                                            echo '<tr><td>' . $interest_value['title']. '</td><td><span class="tags-input"><span class="tag">' . $this->interest__prefix . $interest_value['slug']. '</span></span></td></tr>';
                                        }
                                        echo '</tbody>';
                                        echo '</table>';
                                    ?>
                                </div>
                            </div>

                            <div class="active-wrap__section-container" data-id="last_access_date_notification">
                                <div class="active-wrap__section-box">
                                    <p>
                                        <?php echo __( 'Please fill in the field below with the number of days you would like the inactive user marking flow to run.', AEMI_TEXT_DOMAIN ) ?> 
                                    </p>

                                    <div style="align-items: center; display: flex">
                                        <label style="margin-right: 15px;">
                                            <strong><?php echo __( 'Number of days:', AEMI_TEXT_DOMAIN ) ?>:</strong>
                                        </label>
                                        <div>
                                            <input type="number" value="<?php
                                                if( isset( $data['last_access_date_notification']['number_days_user_considered_inactive'] ) ){
                                                    echo esc_attr( $data['last_access_date_notification']['number_days_user_considered_inactive'] );
                                                }
                                            ?>" name="<?php echo $this->input_prefix; ?>[<?php echo 'last_access_date_notification'; ?>][number_days_user_considered_inactive]" />
                                        </div>
                                    </div>

                                    <div style="align-items: center; display: flex; margin-top: 30px;">
                                        <label style="margin-right: 15px;">
                                            <strong><?php echo __( 'Nombre de la etiqueta que se utilizará:', AEMI_TEXT_DOMAIN ) ?>:</strong>
                                        </label>
                                        <div>
                                            <input type="text" value="<?php
                                                if( isset( $data['last_access_date_notification']['tag_name'] ) ){
                                                    echo esc_attr( $data['last_access_date_notification']['tag_name'] );
                                                } else {
                                                    echo 'last_access_date';
                                                }
                                            ?>" name="<?php echo $this->input_prefix; ?>[<?php echo 'last_access_date_notification'; ?>][tag_name]" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr style="margin: 40px 0; opacity: 0.3;" />

                        <button type="submit" class="button button-primary"><?php echo __( 'Save', AEMI_TEXT_DOMAIN ); ?></button>
                    </form>
                </div>
            <?php
        }
    }
    
    new AemiCustomActiveCampaignIntegration();
}