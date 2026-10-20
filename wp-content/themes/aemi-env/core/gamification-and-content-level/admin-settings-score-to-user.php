<?php 

class AemiAdminSettingsScoreToUsers extends AemiLevelUsers {

    private $getUserViewedPageRelatedToScores = false;
    private $getUserTaxonomiesScores = false;
    private $getUserScore = false;
    private $getUserScoreCurrentLevel = false;
    private $getUserTotalRealScoreBasedOnCategoryScoresAndCurrentLevel = false;
    private $nonce_action = 'view_score_user';
    private $nonce_name = 'score_action';
    private $nonce_update_action = 'update_view_score_user';
    private $nonce_update_name = 'update_score_action';

	function __construct(){
        add_action( 'load-user-edit.php', array( $this, 'custom_page_edit_level_user' ), 1 );
        add_action( 'personal_options', array( $this, 'score_options_action_on_edit' ), 999, 1 );
        add_filter( 'user_row_actions', array( $this, 'custom_button_in_row_list_user'), 999, 2 );
    }

    /**
     * Adds a summary of the information given on the user's profile page and a button to go to the more information page
     * 
	 * @param WP_User  $profileuser WP_User object for the currently listed user.
     */
    public function score_options_action_on_edit( $profileuser ){
        ?>
        <tr>
            <th scope="row"><?php echo __( 'Total score', AEMI_TEXT_DOMAIN ); ?></th>
            <td>
                <span style="display: flex;align-items: center;">
                    <span style="display: flex;flex-direction: column;">
                        <span><?php echo __( 'Score', AEMI_TEXT_DOMAIN ); ?>:</span>
                        <span style="font-size: 23px;font-weight: bold;"><?php echo $this->getUserScore( $profileuser->ID ); ?></span>
                    </span>
                    <span style="border-left: 1px solid #eee;display: flex;flex-direction: column;padding: 10px;margin: 0 20px;border-right: 1px solid #eee;">
                        <span><?php echo __( 'Level', AEMI_TEXT_DOMAIN ); ?>:</span>
                        <span style="background: #f39910;color: #fff;padding: 7px 20px;border-radius: 25px;text-align: center;margin-top: 8px;font-size: 12px;"><?php echo $this->getUserScoreCurrentLevel( $profileuser->ID, true )->name; ?></span>
                    </span>
                    <span>
                        <a target="_blank" href="<?php echo $this->get_view_points_admin_url( $profileuser->ID ); ?>" title="<?php echo __( 'View scores', AEMI_TEXT_DOMAIN ); ?>" class="button button-primary"><?php echo __( 'View scores', AEMI_TEXT_DOMAIN ); ?></a>
                    </span>
                </span>
            </td>
        </tr>
        <?php
    }

    /**
     * Returns the url to view the user's score information
     * 
     * @param Int $user_id
     * @param Boolean $url_decode
     * @return String
     */
    protected function get_view_points_admin_url( $user_id, $custom_args = array(), $url_decode = false ){

        $custom_url = '';

        if( $custom_args ){
            foreach ( $custom_args as $key => $value ) {
                $custom_url .= ( $custom_url ? '&' : '' ) . $key  . '=' . $value;
            }
        }

        $url = wp_nonce_url( "user-edit.php?action=view_user_score" . ( $custom_url ? '&' . $custom_url : '' ) . "&user_id=" . $user_id, $this->nonce_action, $this->nonce_name );
        if( $url_decode ) $url = strtr( $url, array( '&amp;' => '&') );

        return $url;
    }

    /**
     * Filters the action links displayed under each user in the Users list table.
     * 
     * @link https://github.com/WordPress/WordPress/blob/master/wp-admin/includes/class-wp-users-list-table.php
     * 
	 * @param string[] $actions     An array of action links to be displayed.
	 * @param WP_User  $user_object WP_User object for the currently listed user.
     */
    public function custom_button_in_row_list_user( $actions, $user_object ){
		$actions['aemi_view_user_score'] = "<a class='aemi_view_user_score' href='" . $this->get_view_points_admin_url( $user_object->ID ) . "'>" . __( 'View scores', AEMI_TEXT_DOMAIN ) . '</a>';
        return $actions;
    }

    /**
     * This method will save the information if the data is saved after a form submission
     * @param Object $profile_user - Current user to view
     */
    public function detect_and_save_custom_options( $profile_user ){
        if ( isset( $_REQUEST[$this->nonce_update_name] ) && wp_verify_nonce( $_REQUEST[$this->nonce_update_name], $this->nonce_update_action ) ) {

            if( isset( $_POST['aemi_manually_points_level_subtaxonomy'] ) && $_POST['aemi_manually_points_level_subtaxonomy'] ){

                $custom_taxonomy_manually = $_POST['aemi_manually_points_level_subtaxonomy'];
                $old_taxonomy_values_manually = $this->getUserManuallyTaxonomiesScores( $profile_user->ID );

                if( $custom_taxonomy_manually != $old_taxonomy_values_manually ){
                    $this->updateUserManuallyTaxonomiesScores( $profile_user->ID, $custom_taxonomy_manually);

                    
                    $message_log = '%s Manually changed the scores for the subcategories of the %s levels.';
                    $type = 'manually_taxonomy_level__success';
                    $type_log = 'success';
                    $this->addUserManuallyLogChanges( $profile_user->ID, $message_log, $type, $type_log, array(
                        'new_tax' => $custom_taxonomy_manually
                    ) );
                }
            }

            /**
             * Level change based on a select, when using this option the system will automatically calculate how many manual points should be added or removed from the user
             */
            if( isset( $_POST['aemi_manually_select_level_taxonomy'] ) && $_POST['aemi_manually_select_level_taxonomy'] ){
                $get_levels = $this->get_levels( array(), true );
                $current_selected_id = $_POST['aemi_manually_select_level_taxonomy'];

                if( isset( $get_levels[$current_selected_id] ) && $get_levels[$current_selected_id] ){

                    if( $this->getUserScoreCurrentLevel( $profile_user->ID )->term_id != $current_selected_id ){
                        $this->forcesUserLevelByChangingTheContentViewed( $profile_user->ID, $current_selected_id );

                        $message_log = '%s changed the user to level %s manually.';
                        $args_redirect = array( 'updated' => true );
                        $type = 'manually_selected_level__success';
                        $type_log = 'success';
                        $others = array(
                            'sprintf' => array(
                                'admin_user' => get_current_user_id(),
                                'custom_new_level' => $current_selected_id
                            )
                        );

                        /**
                         * If the level id is different from the desired level, 
                         * it is because there are not enough posts to raise the desired user level. 
                         * Therefore, the possible level based on the score has been updated.
                         */
                        $curren_level_term_after_updated_id = $this->getUserScoreCurrentLevel( $profile_user->ID )->term_id;
                        if( $curren_level_term_after_updated_id != $current_selected_id ){
                            $current_tax_selected = get_term( $current_selected_id );
                            $curren_level_term_after_updated = get_term( $curren_level_term_after_updated_id );

                            $args_redirect = array( 'error' => sprintf( __( 'The user could not be upgraded to the %s level because there was not enough point content. Could upload user to %s', AEMI_TEXT_DOMAIN ), '<code>' . $curren_level_term_after_updated->name . '</code>', '<code>' . $current_tax_selected->name . '</code>' ) );

                            $message_log = 'The %s tried to change the user to level %s manually, but the desired level could not be changed. The level that could be changed was to %s';
                            $type = 'manually_selected_level__error';
                            $others['sprintf']['possible_level_to_change'] = $curren_level_term_after_updated_id;
                            $type_log = 'warning';
                        }
                        
                        //generate log
                        $this->addUserManuallyLogChanges( $profile_user->ID, $message_log, $type, $type_log, $others );

                        wp_redirect( $this->get_view_points_admin_url( $profile_user->ID, $args_redirect, true ) );
                        exit();
                    } else {
                        $this->userRecalculateAndUpdateTotalScore( $profile_user->ID );
                        wp_redirect( $this->get_view_points_admin_url( $profile_user->ID, $args_redirect, true ) );
                        exit();
                    }
                }

                /**
                 * Here is when the point is manually added by the input field, enabled only in special cases using the special parameter in the url
                 */
            } else if ( isset( $_POST[$this->getUserMetaName__UserScoreManuallyAddedPoints()] ) ){
                /**
                 * Temporarily disabled for not using it, but the code works
                 */
                // $score_manually = $_POST[$this->getUserMetaName__UserScoreManuallyAddedPoints()];
                // $score_manually = ( $score_manually === '' || !is_numeric($score_manually) ? 0 : $score_manually );

                // $this->updateUserScoreManuallyAddedPoints( $profile_user->ID, $score_manually );
                
                // wp_redirect( $this->get_view_points_admin_url( $profile_user->ID, array( 'updated' => true ), true ) );
                // exit();
            }
        }
    }

    /**
     * Create and validate custom page to edit and view scores for a specific user
     */
    public function custom_page_edit_level_user(){
        if( 
            is_admin() && 
            isset( $_GET['action'] ) && $_GET['action'] == 'view_user_score' &&
            get_current_screen()->id == 'user-edit' && 
            isset( $_GET['user_id'] ) && $_GET['user_id'] && is_numeric( $_GET['user_id'] ) 
        ){

            if ( isset( $_REQUEST[$this->nonce_name] ) && wp_verify_nonce( $_REQUEST[$this->nonce_name], $this->nonce_action ) ) {

                /**
                 * checks if the user trying to edit has the necessary permission
                 */
                if( current_user_can( 'edit_users' ) ){

                    /**
                     * The following validations are based on wordpress user edit.php file
                     * @link https://github.com/WordPress/WordPress/blob/master/wp-admin/user-edit.php
                     */

                    
                    /** WordPress Administration Bootstrap */
                    require_once ABSPATH . 'wp-admin/admin.php';

                    wp_reset_vars( array( 'action', 'user_id', 'wp_http_referer' ) );

                    $user_id = (int) $_GET['user_id'];

                    $user = get_user_by( 'id', $user_id );
                    
                    if ( !$user ) {
                        wp_die( __( 'Invalid user ID.' ) );
                    }

                    
                    $profile_user = get_user_to_edit( $user_id );

                    if ( ! current_user_can( 'edit_user', $user_id ) ) {
                        wp_die( __( 'Sorry, you are not allowed to edit this user.' ) );
                    }

                    //save meta after update form
                    $this->detect_and_save_custom_options( $profile_user );

                    $title_page = sprintf( __( 'View %s Score', AEMI_TEXT_DOMAIN ), $profile_user->display_name );
                    
                    global $title;
                    //Modify default title page admin WordPress
                    $title = $title_page;

                    require_once ABSPATH . 'wp-admin/admin-header.php';

                    $this->getUserViewedPageRelatedToScores = $this->getUserViewedPageRelatedToScores( $profile_user->ID, true ); 
                    $this->getUserTaxonomiesScores = $this->getUserTaxonomiesScores( $profile_user->ID ); 
                    $this->getUserScore = $this->getUserScore( $profile_user->ID ); 
                    $this->getUserScoreCurrentLevel = $this->getUserScoreCurrentLevel( $profile_user->ID, true ); 
                    $this->getUserTotalRealScoreBasedOnCategoryScoresAndCurrentLevel = $this->getUserTotalRealScoreBasedOnCategoryScoresAndCurrentLevel( $profile_user->ID, true ); 
                    $this->getUserManuallyTaxonomiesScores = $this->getUserManuallyTaxonomiesScores( $profile_user->ID ); 
                    $this->levels = $this->get_levels();

                    //add temporary method to get custom style page
                    $this->custom_style_for_admin_settings();

                    if ( isset( $_GET['updated'] ) ) : ?>
                        <div id="message" class="updated notice is-dismissible">
                            <p><strong><?php _e( 'Updated data', AEMI_TEXT_DOMAIN ); ?></strong></p>
                        </div>
                    <?php endif;

                    if ( isset( $_GET['error'] ) ) : ?>
                        <div id="message" class="error notice is-dismissible">
                            <p><strong><?php echo $_GET['error']; ?></strong></p>
                        </div>
                    <?php endif;

                    echo '<div class="wrap" id="profile-page">';
                        echo '<h1 class="wp-heading-inline">' . esc_html( $title_page ) . '</h1>';
                        echo '<form id="your-profile" action="' . $this->get_view_points_admin_url( $profile_user->ID ) . '" method="POST">';
				            wp_nonce_field( $this->nonce_update_action, $this->nonce_update_name ); 
                            $this->main_page( $profile_user );
                        echo '</form>';
                    echo '</div>';

                    //add temporary method to get custom script page
                    $this->custom_script_for_admin_settings();
                    
                    require_once ABSPATH . 'wp-admin/admin-footer.php';
                    exit();
                }
            } else {
                wp_die(__( 'Invalid user ID.' ) . '²' );
            }
        }
    }

    /**
     * Custom script manually added inline
     */
    public function custom_script_for_admin_settings(){
        ?>
        <script>
            var taxLevelInfo = <?php echo json_encode( $this->levels ); ?>;
            var currentUserLevel = <?php echo json_encode( $this->getUserScoreCurrentLevel ); ?>;
            var currentTotalPoints = <?php echo $this->getUserScore; ?>;

            window.addEventListener('load', function(){
                (function($){

                    function aemiRecalculateSubTax(){
                        var reCountContainer = $('.aemi-admin-settings-score-to-users__form-score-items');
                        var blockNextPoints = false;
                        var blockSumTempScore = false;
                        var aemiTotalCurrentPoints = 0;

                        reCountContainer.each(function(indexA){
                            var reCountContainerPercents = $($(reCountContainer[indexA]).find('.aemi-subtax-item-count-percent'));
                            var titleLevel = $(reCountContainer[indexA]).parent().parent().find('.aemi-admin-settings-score-to-users__form-title').text();

                            reCountContainerPercents.each(function(index){
                                var recountContainer = $(reCountContainerPercents[index]);
                                var recountInputManully = recountContainer.find('.aemi_manually_points_level_subtaxonomy').val();
                                var totaldefaultlimitpoints = Number( recountContainer.data('totaldefaultlimitpoints') );
                                var totalpointsminusmanualpoints = Number( recountContainer.data('totalpointsminusmanualpoints') );
                                var innerText = recountContainer.find('.aemi-admin-settings-score-to-users__form-score-info__title').text();

                                recountInputManully = ( !isNaN( Number( recountInputManully ) ) ? Number( recountInputManully ) : 0 );
                                var finalPoints = ( totalpointsminusmanualpoints > totaldefaultlimitpoints ? totaldefaultlimitpoints : totalpointsminusmanualpoints );
                                finalPoints = ( finalPoints + recountInputManully );
                                finalPoints = ( finalPoints > totaldefaultlimitpoints ? totaldefaultlimitpoints : ( finalPoints < 0 ? 0 : finalPoints ) );

                                var currentPercentCaculated = ( ( finalPoints * 100 ) / totaldefaultlimitpoints );
                                currentPercentCaculated = ( currentPercentCaculated > 100 ? 100 : currentPercentCaculated );

                                recountContainer.find('.aemi-bar-percent-content___bar').css('width', currentPercentCaculated +'%');
                                recountContainer.find('.aemi-bar-percent-content___bar-text-percent').css('left', currentPercentCaculated +'%');
                                recountContainer.find('.aemi-bar-percent-content___bar-text-percent__points').text(finalPoints);

                                recountContainer.find('.aemi-bar-percent-content__end').removeClass('aemi-bar-percent-content__active');
                                if( currentPercentCaculated >= 100){
                                    recountContainer.find('.aemi-bar-percent-content__end').addClass('aemi-bar-percent-content__active');
                                }

                                recountContainer.removeClass('aemi-bar-percent-content___bar-text-percent-hide');
                                if( currentPercentCaculated <= 0 || currentPercentCaculated >= 100 ){
                                    recountContainer.addClass('aemi-bar-percent-content___bar-text-percent-hide');
                                }

                                if( currentPercentCaculated < 100 && blockNextPoints == false){
                                    blockNextPoints = true;
                                }

                                if( blockSumTempScore == false ){
                                    aemiTotalCurrentPoints += finalPoints;
                                }
                            });

                            if( reCountContainerPercents.length > 0 ){

                                var barParent = $(reCountContainer[indexA]).find('.aemi-bar-percent-content___bar-parent');
                                var barParentInfo = $(barParent).closest('.aemi-bar-percent-content');
                                var barParentStart = barParentInfo.find('> .aemi-bar-percent-content__start');
                                var barParentInnerText = barParentInfo.find('.aemi-bar-percent-content___bar-text-percent');
                                var barParentInnerTextPointsText = barParentInfo.find('.aemi-bar-percent-content___bar-text-percent__points');
                                var barParentEnd = barParentInfo.find('> .aemi-bar-percent-content__end');
                                var startlevelat = barParentInfo.data('startlevelat');
                                var endlevelat = barParentInfo.data('endlevelat');


                                barParent.css('width', '0%');
                                barParentInnerText.css('left', currentPercentCaculatedInner + '%');
                                barParentStart.removeClass('aemi-bar-percent-content__active');
                                barParentEnd.removeClass('aemi-bar-percent-content__active');

                                if( aemiTotalCurrentPoints >= startlevelat && aemiTotalCurrentPoints <= endlevelat ){
                                    var currentPercentCaculatedInner = ( ( ( aemiTotalCurrentPoints - startlevelat ) * 100 ) / ( endlevelat - startlevelat ) );
                                    currentPercentCaculatedInner = ( currentPercentCaculatedInner > 100 ? 100 : currentPercentCaculatedInner );
                                    
                                    if( aemiTotalCurrentPoints >= startlevelat ){
                                        barParentStart.addClass('aemi-bar-percent-content__active');
                                    }

                                    if( currentPercentCaculatedInner >= 100 ){
                                        barParentEnd.addClass('aemi-bar-percent-content__active');
                                    }

                                    if( currentPercentCaculatedInner <= 0 || currentPercentCaculatedInner >= 100 ){
                                        barParentInfo.addClass('aemi-bar-percent-content___bar-text-percent-hide');
                                    } else {
                                        barParentInfo.removeClass('aemi-bar-percent-content___bar-text-percent-hide');
                                    }

                                    barParent.css('width', currentPercentCaculatedInner + '%');
                                    barParentInnerText.css('left', currentPercentCaculatedInner + '%');

                                    barParentInnerTextPointsText.text(aemiTotalCurrentPoints);
                                }

                                if( blockNextPoints ){
                                    blockSumTempScore = true;
                                } 
                            }
                        });
                    }
                    
                    $('.aemi_manually_points_level_subtaxonomy').on('input', function(){
                        aemiRecalculateSubTax();
                    });
                })(jQuery);
            });
        </script>
        <?php
    }

    /**
     * Adds an inline style to the custom header for the user's score view page
     */
    public function custom_style_for_admin_settings(){
        ?>
        <style>
            form#your-profile{
                padding: 25px 20px;
            }

            input[name="aemi_current_tab_active"]{
                display: none;
            }

            .aemi-admin-settings-score-to-users__header,
            .aemi-admin-settings-score-to-users__header ul{
                margin: 0;
                padding: 0;
            }

            .aemi-admin-settings-score-to-users__header ul{
                display: flex;
            }

            .aemi-admin-settings-score-to-users__header ul label{
                background: #eee;
                border: 1px solid #ccc;
                padding: 13px 20px;
                border-radius: 5px 5px 0 0;
                display: table;
            }

            .aemi-admin-settings-score-to-users__header ul li{
                margin: 0 0 -1px;
                padding: 0;
            }
            
            .aemi-admin-settings-score-to-users__header ul li + li{
                margin-left: 5px;
            }

            .aemi-admin-settings-score-to-users__main{
                border: 1px solid #ccc;
                padding: 25px;
                border-radius: 0 5px 5px 5px;
            }

            .aemi-admin-settings-score-to-users__main-tab{
                display: none;
            }

            .aemi-admin-settings-score-to-users__form-row{
                display: flex
            }

            .aemi-admin-settings-score-to-users__form-row + .aemi-admin-settings-score-to-users__form-row{
                padding: 25px 0;
                border-top: 1px solid #eee;
                margin-top: 35px;
            }

            .aemi-admin-settings-score-to-users__form-title{
                width: 300px;
                font-weight: bold;
            }

            .aemi-admin-settings-score-to-users__form-inputs{
                flex: 1;
                width: calc( 100% - 300px );
            }

            .aemi-admin-settings-score-to-users__form-input{
                width: 100%;
            }

            .aemi-admin-settings-score-to-users__form-score-info{
                padding: 0;
                margin: 0;
            }

            .aemi-admin-settings-score-to-users__form-score-info ul{
                padding: 15px;
            }

            .aemi-admin-settings-score-to-users__form-score-info li{
                border: 1px solid #eee;
                padding: 15px;
                margin: 0;
                background: #fff;
            }
            
            .aemi-admin-settings-score-to-users__form-score-info li:nth-child(odd){
                background: #f7f7f7;
            }
            
            .aemi-admin-settings-score-to-users__form-score-info li + li{
                border-top: 0;
            }

            .aemi-admin-settings-score-to-users__form-score-info__title{
                font-weight: bold;
            }

            .aemi-admin-settings-score-to-users__footer{
                padding: 25px 0;
                border-top: 1px solid #eee;
                margin-top: 35px;
                text-align: right;
            }

            #aemi-admin-settings-score-to-users__form-total-user-score{
                display: inline-flex;
            }

            .aemi-admin-settings-score-to-users__form-manually-points-info{
                display: flex;
                flex-direction: column;
                font-size: 12px;
                background: #fff;
                padding: 10px;
                border: 1px solid #eee;
                margin: 0 0 0 12px;
                border-radius: 15px;
                box-shadow: 0 0 7px -5px rgb(0 0 0 / 20%);
            }

            .aemi-admin-settings-score-to-users__form-manually-points-info span + span{
                background: #f39910;
                color: #fff;
                padding: 2px 20px;
                border-radius: 25px;
                width: 78px;
                text-align: center;
                margin-top: 8px;
            }

            .aemi-admin-settings-score-to-users__no-content{
                background: #f7f7f7;
                border: 1px solid #ddd;
                padding: 30px;
                font-weight: bold;
            }

            .aemi-bar-percent-content{
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex:1;
            }
            
            .aemi-bar-percent-content__end,
            .aemi-bar-percent-content__start{
                background: #fff;
                border: #eee 1px solid;
                width: 40px;
                text-align: center;
                height: 40px;
                border-radius: 50%;
                line-height: 41px;
                box-shadow: 0 0 6px -3px rgb(0 0 0 / 20%);
                font-weight: bold;
                color: #666;
                font-size: 13px;
            }
            
            .aemi-bar-percent-content__end span,
            .aemi-bar-percent-content__start span{
                font-size: 10px;
                color: #666;
                margin-top: -15px;
                display: block;
            }

            .aemi-bar-percent-content__content_bar{
                flex: 1;
                height: 18px;
                width: 100%;
                background: #eee;
                border-top: 1px solid #ddd;
                left: -2px;
                position: relative;
            }

            .aemi-bar-percent-content___bar{
                background: #f68321;
                height: 100%;
                display: flex;
                box-shadow: 1px 0 27px -5px rgb(0 0 0 / 45%);
                justify-content: flex-end;
            }
            
            .aemi-bar-percent-content___bar-text-percent{
                color: #fff;
                font-weight: bold;
                font-size: 8px;
                line-height: 17px;
                display: table;
                background: #333;
                padding: 0;
                text-align: center;
                align-items: center;
                position: absolute;
                top: 24px;
                border-radius: 50px;
                width: 90px;
                margin-left: -45px;
                z-index: 5;
            }
        
            .aemi-bar-percent-content___bar-text-percent span{
                position: relative;
            }

            .aemi-bar-percent-content___bar-text-percent span:before{
                content: "";
                display: block;
                width: 0;
                height: 0;
                border-left: 5px solid transparent;
                border-right: 5px solid transparent;
                border-bottom: 5px solid #333;
                margin: -5px auto 0;
            }

            .aemi-bar-percent-content___bar-text-percent-hide .aemi-bar-percent-content___bar-text-percent{
                display: none;
            }

            .aemi-bar-percent-content__end{
                left: -12px;
                position: relative;
            }
            .aemi-bar-percent-content__active{
                background: #f68321;
                color: #fff;
                border-color: #f68321;
                z-index: 5;
            }

            .aemi-bar-percent-content__manual-pontuation{
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
            }

            .aemi-bar-percent-content__manual-pontuation input{}
            .aemi-bar-percent-content__manual-pontuation span{
                font-size: 10px;
                color: #666;
                margin-top: -3px;
                display: block;
                font-weight: bold;
                margin-bottom: -12px;
            }


            input#aemi-current-tab-active-1:checked ~ header nav ul li label[for="aemi-current-tab-active-1"],
            input#aemi-current-tab-active-2:checked ~ header nav ul li label[for="aemi-current-tab-active-2"],
            input#aemi-current-tab-active-3:checked ~ header nav ul li label[for="aemi-current-tab-active-3"],
            input#aemi-current-tab-active-4:checked ~ header nav ul li label[for="aemi-current-tab-active-4"]{
                background: #2271b1;
                border-color: #2271b1;
                color: #fff;
                text-decoration: none;
                text-shadow: none;
                z-index: 65;
                position: relative;
            }
            
            input#aemi-current-tab-active-1:checked ~ main .aemi-admin-settings-score-to-users__main-tab[data-id="aemi-current-tab-active-1"],
            input#aemi-current-tab-active-2:checked ~ main .aemi-admin-settings-score-to-users__main-tab[data-id="aemi-current-tab-active-2"],
            input#aemi-current-tab-active-3:checked ~ main .aemi-admin-settings-score-to-users__main-tab[data-id="aemi-current-tab-active-3"],
            input#aemi-current-tab-active-4:checked ~ main .aemi-admin-settings-score-to-users__main-tab[data-id="aemi-current-tab-active-4"]{
                display: block;
            }

        </style>
        <?php
    }

    public function settings_calculate_percentage_of_value( $valor, $total ) {
        return ( $valor * 100 ) / $total;
    }

    /**
     * Shows current main page
     * @param Object $profile_user - Current user to view
     */
    public function main_page( $profile_user ){
        ?>
            <input type="radio" value="1" name="aemi_current_tab_active" checked="true" id="aemi-current-tab-active-1" />
            <input type="radio" value="2" name="aemi_current_tab_active" id="aemi-current-tab-active-2" />
            <input type="radio" value="3" name="aemi_current_tab_active" id="aemi-current-tab-active-3" />

            <header class="aemi-admin-settings-score-to-users__header">
                <nav>
                    <ul>
                        <li>
                            <label for="aemi-current-tab-active-1"><?php echo __( 'Score summary', AEMI_TEXT_DOMAIN ); ?></label>
                        </li>
                        <li>
                            <label for="aemi-current-tab-active-2"><?php echo __( 'Scoring by level and sub-rating', AEMI_TEXT_DOMAIN ); ?></label>
                        </li>
                        <li>
                            <label for="aemi-current-tab-active-3"><?php echo __( 'Content scoring information', AEMI_TEXT_DOMAIN ); ?></label>
                        </li>
                    </ul>
                </nav>                
            </header>
            
            <main class="aemi-admin-settings-score-to-users__main">
                <div data-id="aemi-current-tab-active-1" class="aemi-admin-settings-score-to-users__main-tab"><?php $this->main_tab_total_score_and_current_level( $profile_user ); ?></div>
                <div data-id="aemi-current-tab-active-2" class="aemi-admin-settings-score-to-users__main-tab"><?php $this->main_tab_tax_score( $profile_user ); ?></div>
                <div data-id="aemi-current-tab-active-3" class="aemi-admin-settings-score-to-users__main-tab"><?php $this->main_tab_posts_score( $profile_user ); ?></div>
            </main>

            <footer class="aemi-admin-settings-score-to-users__footer">
                <button type="submit" class="button button-primary"><?php echo __('Save', AEMI_TEXT_DOMAIN ); ?></button>
            </footer>
        <?php 
    }
    
    public function main_tab_posts_score( $profile_user ){
        $posts = $this->getUserViewedPageRelatedToScores;
        
        if( $posts ){
            foreach( $posts as $current_post ):
            ?>
            <div class="aemi-admin-settings-score-to-users__form-row">
                <div class="aemi-admin-settings-score-to-users__form-title">
                    <?php echo $current_post['post_info']->post_title; ?>
                    <a href="<?php echo get_admin_url(); ?>post.php?post=<?php echo $current_post['post_info']->ID; ?>&action=edit" title="<?php echo __( 'View post', AEMI_TEXT_DOMAIN ); ?>" target="_blank" style="text-decoration: none;background: #2271b1;border-radius: 60px;color: #fff;width: 30px;height: 14px;display: inline-table;text-align: center;line-height: 23px;padding-top: 3px;">
                        <span class="dashicons dashicons-edit"></span>
                    </a>
                </div>
                <div class="aemi-admin-settings-score-to-users__form-inputs">
                    <ul class="aemi-admin-settings-score-to-users__form-score-info">
                        <li>
                            <span class="aemi-admin-settings-score-to-users__form-score-info__title"><?php echo __( 'Date' ); ?>:</span> 
                            <span><?php echo date( get_option('date_format') . ' ' . get_option('time_format'), strtotime( $current_post['date'] ) ); ?></span>
                        </li>
                        <li>
                            <span class="aemi-admin-settings-score-to-users__form-score-info__title"><?php echo __( 'Total score', AEMI_TEXT_DOMAIN ); ?>:</span> 
                            <span><?php echo $current_post['score_classification_total']; ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            <?php
            endforeach;
        } else {
            echo '<div class="aemi-admin-settings-score-to-users__no-content">' . __('No data at the moment.', AEMI_TEXT_DOMAIN ) . '</div>';
        }
    }
    
    public function main_tab_tax_score( $profile_user ){
        $taxonomy = $this->getUserTotalScoreByTaxonomiesScores( $profile_user->ID );

        if( $taxonomy ){
            foreach ( $taxonomy as $tax ) :
                //if( $tax->_score_limit_current_point_total_with_limit > 0 ):
                ?>
                <div class="aemi-admin-settings-score-to-users__form-row">
                    <div class="aemi-admin-settings-score-to-users__form-title"><?php echo $tax->name; ?>:</div>
                    <div class="aemi-admin-settings-score-to-users__form-inputs">
                        <ul class="aemi-admin-settings-score-to-users__form-score-info aemi-admin-settings-score-to-users__form-score-items">
                            <?php if( isset( $_GET['view_more_options'] ) && $_GET['view_more_options'] ): ?>
                            <li>
                                <span class="aemi-admin-settings-score-to-users__form-score-info__title"><?php echo __( 'Max level score', AEMI_TEXT_DOMAIN ); ?>:</span> 
                                <span><?php echo $tax->_max_level_points_in; ?></span>
                            </li>
                            <li>
                                <span class="aemi-admin-settings-score-to-users__form-score-info__title"><?php echo __( 'Scoring with point limit', AEMI_TEXT_DOMAIN ); ?>:</span> 
                                <span><?php echo $tax->_score_limit_current_point_total_with_limit; ?></span>
                            </li>
                            <li>
                                <span class="aemi-admin-settings-score-to-users__form-score-info__title"><?php echo __( 'Scoring without point limit', AEMI_TEXT_DOMAIN ); ?>:</span> 
                                <span><?php echo $tax->_score_limit_actual_total_points; ?></span>
                            </li>
                            <?php else: ?>
                            <li>
                                <?php $total_percent = $this->settings_calculate_percentage_of_value( $tax->_score_limit_current_point_total_with_limit, $tax->_max_level_points_in ); ?>
                                <span class="aemi-bar-percent-content <?php echo ( $total_percent < 100 && $total_percent > 0 ? '' : ' aemi-bar-percent-content___bar-text-percent-hide ' ); ?>" data-startlevelat="<?php echo $tax->_start_level_at; ?>" data-endlevelat="<?php echo ($tax->_start_level_at + $tax->_max_level_points_in); ?>">
                                    <span class="aemi-bar-percent-content__start <?php echo ( $this->getUserScore >= $tax->_start_level_at ? 'aemi-bar-percent-content__active' : '' ); ?>"><?php echo $tax->_start_level_at; ?></span>
                                    <span class="aemi-bar-percent-content__content_bar <?php echo ( $total_percent < 7 ? 'aemi-bar-percent-content__content_bar--small' : '' ); ?>">
                                        <span class="aemi-bar-percent-content___bar aemi-bar-percent-content___bar-parent" style="width: <?php echo $total_percent; ?>%"></span>
                                            <span class="aemi-bar-percent-content___bar-text-percent" style="left: <?php echo $total_percent; ?>%">
                                                <span>
                                                    <span class="aemi-bar-percent-content___bar-text-percent__points" >
                                                        <?php echo ($tax->_start_level_at + $tax->_score_limit_current_point_total_with_limit); ?>
                                                    </span>
                                                    <?php echo __( 'Scores', AEMI_TEXT_DOMAIN ); ?>
                                                </span>
                                            </span>
                                    </span>
                                    <span class="aemi-bar-percent-content__end <?php echo ( $total_percent == 100 ? 'aemi-bar-percent-content__active' : '' ); ?>">
                                        <?php echo $tax->_start_level_at + $tax->_max_level_points_in; ?>
                                        <span><?php echo __( 'Limit', AEMI_TEXT_DOMAIN ); ?></span>
                                    </span>
                                </span>
                            </li>
                            <?php endif; ?>

                            <?php if( $tax->classification ): ?>
                                <li>
                                    <span class="aemi-admin-settings-score-to-users__form-score-info__title"><?php echo __( 'Skills', AEMI_TEXT_DOMAIN  ); ?>:</span>
                                    <ul>
                                    <?php foreach( $tax->classification as $subtax ): ?>
                                        <?php if( isset( $_GET['view_more_options'] ) && $_GET['view_more_options'] ): ?>
                                        <li>
                                            <span class="aemi-admin-settings-score-to-users__form-score-info__title"><?php echo $subtax->name; ?>:</span> 
                                            <span>
                                                <ul>
                                                    <li>
                                                        <span class="aemi-admin-settings-score-to-users__form-score-info__title"><?php echo __( 'Score limite', AEMI_TEXT_DOMAIN ); ?>:</span> 
                                                        <span><?php echo $subtax->_score_limit; ?></span>
                                                    </li>
                                                    <li>
                                                        <span class="aemi-admin-settings-score-to-users__form-score-info__title"><?php echo __( 'Scoring with point limit', AEMI_TEXT_DOMAIN ); ?>:</span> 
                                                        <span><?php echo $subtax->_score_limit_current_point_total_with_limit; ?></span>
                                                    </li>
                                                    <li>
                                                        <span class="aemi-admin-settings-score-to-users__form-score-info__title"><?php echo __( 'Scoring without point limit', AEMI_TEXT_DOMAIN ); ?>:</span> 
                                                        <span><?php echo $subtax->_score_limit_actual_total_points; ?></span>
                                                    </li>
                                                </ul>
                                            </span>
                                        </li>
                                        <?php else: ?>
                                        <?php $total_percent = $this->settings_calculate_percentage_of_value( $subtax->_score_limit_current_point_total_with_limit, $subtax->_score_limit ); ?>
                                        <li class="aemi-subtax-item-count-percent <?php echo ( $total_percent < 100 && $total_percent > 0 ? '' : ' aemi-bar-percent-content___bar-text-percent-hide ' ); ?>" style="display: flex; align-items: center" data-totaldefaultlimitpoints="<?php echo $subtax->_score_limit; ?>" data-totaldefaultpoints="<?php echo $subtax->_score_limit_actual_total_points_minus_manual_points; ?>" data-scorelimitcurrentpointtotalwithlimit="<?php echo $subtax->_score_limit_current_point_total_with_limit; ?>"  data-totalpointsminusmanualpoints="<?php echo $subtax->_score_limit_actual_total_points_minus_manual_points; ?>">
                                            <span class="aemi-admin-settings-score-to-users__form-score-info__title" style="padding-right: 15px;"><?php echo $subtax->name; ?>:</span> 
                                            <span class="aemi-bar-percent-content">
                                                <span class="aemi-bar-percent-content__start aemi-bar-percent-content__active">0</span>
                                                <span class="aemi-bar-percent-content__content_bar <?php echo ( $total_percent < 7 ? 'aemi-bar-percent-content__content_bar--small' : '' ); ?>">
                                                    <span class="aemi-bar-percent-content___bar" style="width: <?php echo $total_percent; ?>%"></span>
                                                    <span class="aemi-bar-percent-content___bar-text-percent" style="left: <?php echo $total_percent; ?>%">
                                                        <span>
                                                            <span class="aemi-bar-percent-content___bar-text-percent__points" >
                                                                <?php echo ($subtax->_score_limit_current_point_total_with_limit); ?> 
                                                            </span>
                                                            <?php echo __( 'Scores', AEMI_TEXT_DOMAIN ); ?>
                                                        </span>
                                                    </span>
                                                </span>
                                                <span class="aemi-bar-percent-content__end <?php echo ( $total_percent == 100 ? 'aemi-bar-percent-content__active' : '' ); ?>">
                                                    <?php echo $subtax->_score_limit; ?>
                                                    <span><?php echo __( 'Limit', AEMI_TEXT_DOMAIN ); ?></span>
                                                </span>
                                            </span>

                                            <span class="aemi-bar-percent-content__manual-pontuation">
                                                <input type="number" class="aemi_manually_points_level_subtaxonomy" name="aemi_manually_points_level_subtaxonomy[<?php echo $subtax->term_id; ?>]" value="<?php echo ( isset( $this->getUserManuallyTaxonomiesScores[$subtax->term_id] ) && $this->getUserManuallyTaxonomiesScores[$subtax->term_id] ? $this->getUserManuallyTaxonomiesScores[$subtax->term_id] : '0' ); ?>" />
                                                <span><?php echo __( 'Manual scoring', AEMI_TEXT_DOMAIN ); ?>.</span>
                                            </span>

                                        </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <?php
                //endif;
                
                //Block content to not display data higher than user level
                // if( $tax->term_id == $this->getUserScoreCurrentLevel->term_id ){
                //     break;
                // }
            endforeach;
        } else {
            echo '<div class="aemi-admin-settings-score-to-users__no-content">' . __('No data at the moment.', AEMI_TEXT_DOMAIN ) . '</div>';
        }
    }

    /**
     * Shows current score and current level
     * @param Object $profile_user - Current user to view
     */
    public function main_tab_total_score_and_current_level( $profile_user ){
        ?>
        <div class="aemi-admin-settings-score-to-users__form-row">
            <div class="aemi-admin-settings-score-to-users__form-title"><?php echo __( 'Current level', AEMI_TEXT_DOMAIN ); ?>:</div>
            <div class="aemi-admin-settings-score-to-users__form-inputs">
                <?php if( isset( $_GET['view_more_options'] ) && $_GET['view_more_options'] ): ?>
                    <input class="aemi-admin-settings-score-to-users__form-input" type="text" readonly="true" value="<?php echo $this->getUserScoreCurrentLevel->name; ?>" />
                <?php else: ?>
                    <select name="aemi_manually_select_level_taxonomy" class="aemi-admin-settings-score-to-users__form-input">
                        <?php
                            if( $this->levels ){
                                foreach( $this->levels as $level ){
                                    echo '<option ' . ( $this->getUserScoreCurrentLevel->term_id == $level->term_id ? 'selected=""' : '' ) .  ' value="' . $level->term_id . '">' . $level->name . '</option>';
                                }
                            }
                        ?>
                    </select>
                    <br />
                    <span style="color: red;font-size: 12px;margin-top: 10px;display: block;line-height: 16px;width: 400px;opacity: 0.5;">
                        Importante: Al cambiar el nivel manualmente, los datos adquiridos por el usuario se borrarán y el sistema asignará automáticamente el contenido visto para que el usuario avance o disminuya el nivel.
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="aemi-admin-settings-score-to-users__form-row">
            <div class="aemi-admin-settings-score-to-users__form-title"><?php echo __( 'Score information', AEMI_TEXT_DOMAIN ); ?>:</div>
            <div class="aemi-admin-settings-score-to-users__form-inputs">
                <strong style="font-size: 30px;font-weight: bold;"><?php echo $this->getUserScore; ?></strong>
            
                <?php if( isset( $_GET['view_more_options'] ) && $_GET['view_more_options'] ): ?>
                
                    <ul class="aemi-admin-settings-score-to-users__form-score-info">
                        <li>
                            <span class="aemi-admin-settings-score-to-users__form-score-info__title"><?php echo __( 'Points added manually', AEMI_TEXT_DOMAIN ); ?>:</span> 
                            <span>
                                <input id="aemi-admin-settings-score-to-users__form-manually-points" type="number" value="<?php echo $this->getUserScoreManuallyAddedPoints( $profile_user->ID ); ?>" name="<?php echo $this->getUserMetaName__UserScoreManuallyAddedPoints(); ?>" />
                            </span>
                        </li>
                        <li>
                            <span class="aemi-admin-settings-score-to-users__form-score-info__title"><?php echo __( 'Points for viewing content', AEMI_TEXT_DOMAIN ); ?>:</span> 
                            <span id="aemi-admin-settings-score-to-users__form-real-score-points"><?php echo $this->getUserTotalRealScoreBasedOnCategoryScoresAndCurrentLevel['points_based_on_limits']; ?></span>
                        </li>
                        <li>
                            <span class="aemi-admin-settings-score-to-users__form-score-info__title"><?php echo __( 'Total score', AEMI_TEXT_DOMAIN ); ?>:</span> 
                            <span id="aemi-admin-settings-score-to-users__form-total-user-score" data-totalscorewithlimit="<?php echo $this->getUserTotalRealScoreBasedOnCategoryScoresAndCurrentLevel['points_based_on_limits']; ?>"><?php echo $this->getUserScore; ?></span>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <?php
            $getUserManuallyLogChangesHTML = $this->getUserManuallyLogChangesHTML( $profile_user->ID );

            if( $getUserManuallyLogChangesHTML ):
        ?>
        <div class="aemi-admin-settings-score-to-users__form-row">
            <div class="aemi-admin-settings-score-to-users__form-title"><?php echo __( 'Manual change log', AEMI_TEXT_DOMAIN ); ?>:</div>
            <div class="aemi-admin-settings-score-to-users__form-inputs"><?php echo $getUserManuallyLogChangesHTML; ?></div>
        </div>
        <?php
            endif;
    }
}

new AemiAdminSettingsScoreToUsers();