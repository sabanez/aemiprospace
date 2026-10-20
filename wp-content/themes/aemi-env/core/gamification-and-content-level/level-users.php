<?php

/**
 * This class is used to use common methods and properties between gamification classes.
 */
abstract class AemiLevelUsers {

	protected $taxonomy_name = 'aemi_level'; 
	protected $related_posts_types = array('post', 'aemi_goto_webinar');

    protected $tax_meta_name__start_level_order = '_level_order';
    protected $tax_meta_name__start_level_at = '_start_level_at';
    protected $tax_meta_name__starting_level_up_to = '_starting_level_up_to';
    protected $tax_meta_name__score_limit = '_score_limit';
    protected $ignore_wikimi_points_already_earned_for_related_content = 'ignore_wikimi_points_already_earned_for_related_content';

    static function get_taxonomy_name(){
        return 'aemi_level';
    }

    /**
     * Returns categories and subcategories.
     * Parent categories are sorted by _start_level_at and sub-categories alphabetically
     * 
     * @param Boolean $return_key_with_term_id
     */
    protected function get_levels( $args = array(), $return_key_with_term_id = false ){
        $new_level = array();
        $old__start_level_at = 0;

        $levels = get_terms( array_merge( 
            array(
                'taxonomy'      => $this->taxonomy_name,
                'hide_empty'    => false,
                'parent'        => 0,
                'meta_key'      => $this->tax_meta_name__start_level_order,
                'orderby'       => 'meta_value_num', 
                'order'         => 'ASC',
            ), $args
        ) );

        if( !is_wp_error( $levels ) ){

            foreach ( $levels as $key => $value ) {
                $levels[$key]->_start_level_at = $old__start_level_at;
                $levels[$key]->_starting_level_up_to = $old__start_level_at;

                $sub_category = get_terms( array(
                    'taxonomy' => $this->taxonomy_name,
                    'hide_empty' => false,
                    'parent' => $value->term_id
                ) );

                if( $sub_category ){
                    foreach( $sub_category as $sc_key => $sc_value ){
                        $tax_meta_name__score_limit = get_term_meta( $sc_value->term_id, $this->tax_meta_name__score_limit, true );
                        $tax_meta_name__score_limit = ( is_numeric( $tax_meta_name__score_limit ) ? (int) $tax_meta_name__score_limit : 0 );
                        $sub_category[$sc_key]->{$this->tax_meta_name__score_limit} = $tax_meta_name__score_limit;

                        $levels[$key]->_starting_level_up_to += $tax_meta_name__score_limit;
                        $levels[$key]->_max_level_points_in += $tax_meta_name__score_limit;
                    }

                    $value->classification = $sub_category;
                    $new_level[] = $value;
                }

                $old__start_level_at = $levels[$key]->_starting_level_up_to;
                $levels[$key]->_starting_level_up_to -= 1;                
                
            }

            $levels = $new_level;

            if( $return_key_with_term_id ){
                $useTermIDbyKey = array();

                foreach ($levels as $key => $value) {
                    $useTermIDbyKey[$value->term_id] = $value;
                }

                $new_level = $useTermIDbyKey;
            }
        }
        
		return $new_level;
    }

    /**
     * 
     */
    protected function get_info_from_previous_level_by_current_level_ID( $tax_id = false ){
        $return_level = false;

        foreach ( $this->get_levels() as $key => $value ) {
            if( $return_level == false ){
                $return_level = $value;
            }

            if( $tax_id == $value->term_id){
                break;
            } else {
                $return_level = $value;
            }
        }

        return $return_level;
    }

    /**
     * Returns the name of the post_meta that is used to save the scores that a post gives in a subcategory
     */
    protected function getPostMetaName__ScoreClassification(){
        return '__' . $this->taxonomy_name . '_post_score';
    }

    /**
     * Returns the name of the goal that specifies whether or not scoring is enabled on a post
     */
    protected function getPostMetaName__ScoreClassificationEnabled(){
        return '__' . $this->taxonomy_name . '_enabled_pontuation_in_post';
    }

    /**
     * In this goal, the user's current score total will be saved
     */
    protected function getPostMetaName__UserScore(){
        return '__' . $this->taxonomy_name . '_user_score';
    }

    /**
     * This goal will save all the pages that the user has viewed and can be used to recalculate the scores again in the future. (This field can be changed by other functions and losing the real history)
     */
    protected function getUserMetaName__UserViewedPageRelatedToScores(){
        return '__' . $this->taxonomy_name . '_user_viewed_page_related_to_scores';
    }

    /**
     * This goal will save all the pages that the user has viewed and can be used to recalculate the scores again in the future (Original).
     */
    protected function getUserMetaName__UserViewedPageRelatedToScoresOriginal(){
        return '__' . $this->taxonomy_name . '_user_viewed_page_related_to_scores_original';
    }
    
    /**
     * Meta name where the user's score is saved separated by category
     */
    protected function getUserMetaName__UserTaxonomiesScores(){
        return '__' . $this->taxonomy_name . '_user_taxonomies_' . $this->taxonomy_name . '_scores';
    }
    
    /**
     * Meta name where the user's manually score is saved separated by category
     */
    protected function getUserMetaName__UserManuallyTaxonomiesScores(){
        return '__' . $this->taxonomy_name . '_user_taxonomies_' . $this->taxonomy_name . '_manually_scores';
    }

    /**
     * In this goal, the points manually added by an administrator to the user will be saved
     */
    protected function getUserMetaName__UserScorePointsManuallyAddedByAdmin(){
        return '__' . $this->taxonomy_name . '_user_score_points_manually_added_by_admin';
    }

    /**
     * Name of the meta user where the ID (Taxonomy) of the level the user is currently associated with is saved.
     */
    protected function getUserMetaName__UserScoreCurrentLevel(){
        return '__' . $this->taxonomy_name . '_user_score_current_taxonomy_level';
    }

    /**
     * Name of the meta user where the ID (Taxonomy) Current taxonomy level score for level update animation comparison.
     */
    protected function getUserMetaName__UserScoreCurrentOldLevelScoreForLevelUpdateAnimationComparison(){
        return '__' . $this->taxonomy_name . '_user_score_current_old_taxonomy_level_score_for_level_update_animation_comparison';
    }

    

    /**
     * Returns the total points manually added by the administrator
     */
    protected function getUserMetaName__UserScoreManuallyAddedPoints(){
        return '__' . $this->taxonomy_name . '_user_score_manually_added_points';
    }

    /**
     * Returns the meta name of log manual log changes
     */
    protected function getUserMetaName__UserScoreManuallyLog(){
        return '__' . $this->taxonomy_name . '_user_score_manually_log_changes';
    }

    /**
     * ==================================================================================
     *                                   Methods
     * ==================================================================================
     */

    /**
     * Add new log
     * 
     * @param Int $user_id
     * @param String $message
     * @param String $type
     * @param String $value
     * @param Array $others
     */
    protected function addUserManuallyLogChanges( $user_id, $message = '', $type = '', $type_log = '', $others = array() ){
        $log = $this->getUserManuallyLogChanges( $user_id );

        if( $user_id && $message && $type && $type_log && is_array( $others ) ){
            $log[] = array(
                'user_generated_log' => get_current_user_id(),
                'date'      => date('Y-m-d H:i:s'),
                'message'   => $message,
                'type'      => $type,
                'type_log'  => $type_log,
                'others'    => $others
            );
            update_user_meta( $user_id, $this->getUserMetaName__UserScoreManuallyLog(), $log );
            return true;
        }

        return false;
    }

    /**
     * Return log
     * 
     * @param Int $user_id
     * @return Array
     */
    protected function getUserManuallyLogChanges( $user_id ){
        $log = ( $user_id ? get_user_meta( $user_id, $this->getUserMetaName__UserScoreManuallyLog(), true ) : array() );
        if( !is_array( $log ) ) $log = array();

        return $log;
    }

    /**
     * Returns the formatted HTML of the log
     * 
     * @param Int $user_id
     * @return String
     */
    protected function getUserManuallyLogChangesHTML( $user_id ){
        $logs = array_reverse( $this->getUserManuallyLogChanges( $user_id ) ); 
        $get_levels = $this->get_levels(array(), true);
        $sub_levels = array();

        if( $get_levels ){
            foreach ( $get_levels as $level ) {
                foreach ( $level->classification as $classification ) {
                    $classification->name = $level->name . ' -> ' . $classification->name;
                    $sub_levels[$classification->term_id] = $classification;
                }
            }
        }

        $users_names = array();
        $view_user_permission = current_user_can('administrator');
        
        $html = '<style>
            .aemi-block-log__pre{
                background: #eee;
                border: 1px solid #ccc;
                padding: 15px;
                max-height: 500px;
                overflow: auto;
                border-radius: 15px;
            }
            
            .aemi-block-log__pre a{
                text-decoration: none;
            }
            .aemi-block-log{ padding: 10px 15px; }
            .aemi-block-log + .aemi-block-log{ border-top: 1px solid #ccc; }
            .aemi-block-log__message-content span:last-child{ padding-right: 15px;}
            .aemi-block-log__message-content span + span { margin-left: 7px; padding-left: 7px; border-left: 1px solid #ccc; }
            .aemi-block-log__message-content__type{
                font-weight: bold;
                color: #118720;
            }
            .aemi-block-log__message-content__type[data-type="warning"]{
                color: #977d00;
            }
            .aemi-block-log__message-content__type[data-type="info"]{
                color: #0c82bb;
            }
            .aemi-block-log__message-content__type[data-type="error"]{
                color: #af1111;
            }
        </style>';


        $html .= '<div class="aemi-block-log__element">';
            $html .= '<pre class="aemi-block-log__pre">';
            
            if( $logs ){
                foreach ( $logs as $log ) {

                    $html .= '<div class="aemi-block-log">';
                        $html .= '<div class="aemi-block-log__message-content">';
                            $html .= '<span class="aemi-block-log__message-content__date">' . date('Y-m-d H:i:s', strtotime( $log['date'] ) ) . '</span>';
                            $html .= '<span class="aemi-block-log__message-content__type" data-type="' . $log['type_log'] . '">' . strtoupper( __( $log['type_log'] ) ) . '</span>';
    
                            $message = __( $log['message'], AEMI_TEXT_DOMAIN );
    
                            if( !isset( $users_names[$log['user_generated_log']] ) ){
                                $datauser = get_userdata($log['user_generated_log']);
                                if( $datauser ){
                                    $users_names[$log['user_generated_log']] = $datauser->display_name;
                                }
                            }
    
                            if( !isset( $users_names[$user_id] ) ){
                                $datauser = get_userdata($user_id);
                                if( $datauser ){
                                    $users_names[$user_id] = $datauser->display_name;
                                }
                            }

                            if( $view_user_permission ){
                                $users_names[$log['user_generated_log']] = '<a href="' . get_admin_url() . 'user-edit.php?user_id=' . $log['user_generated_log'] . '" target="_blank">' . $users_names[$log['user_generated_log']] . '</a>';
                                $users_names[$user_id] = '<a href="' . get_admin_url() . 'user-edit.php?user_id=' . $user_id . '" target="_blank">' . $users_names[$user_id] . '</a>';
                            }
    
                            switch ( $log['type'] ) {
                                case 'manually_selected_level__error':
                                case 'manually_selected_level__success':
    
                                    if( isset( $log['others']['sprintf']['admin_user'] ) ){
                                        if( !isset( $users_names[$log['others']['sprintf']['admin_user']] ) ){
                                            $datauser = get_userdata($log['others']['sprintf']['admin_user']);
                                            if( $datauser ){
                                                $users_names[$log['others']['sprintf']['admin_user']] = $datauser->display_name;
                                            }
                                        }
    
                                        if( $view_user_permission ){
                                            $users_names[$log['others']['sprintf']['admin_user']] = '<a href="' . get_admin_url() . 'user-edit.php?user_id=' . $log['others']['sprintf']['admin_user'] . '" target="_blank">' . $users_names[$log['others']['sprintf']['admin_user']] . '</a>';
                                        }
    
                                        $log['others']['sprintf']['admin_user'] = '<code>' . $users_names[$log['others']['sprintf']['admin_user']] . '</code>';
                                    }
    
                                    if( isset( $log['others']['sprintf']['custom_new_level'] ) && isset( $get_levels[$log['others']['sprintf']['custom_new_level']] ) ){
                                        $log['others']['sprintf']['custom_new_level'] = '<code>' . $get_levels[$log['others']['sprintf']['custom_new_level']]->name . '</code>';
                                    }
    
                                    if( isset( $log['others']['sprintf']['possible_level_to_change'] ) && isset( $get_levels[$log['others']['sprintf']['possible_level_to_change']] ) ){
                                        $log['others']['sprintf']['possible_level_to_change'] = '<code>' . $get_levels[$log['others']['sprintf']['possible_level_to_change']]->name . '</code>';
                                    }
    
                                    break;

                                case 'manually_taxonomy_level__success':
                                    if( isset( $log['others']['new_tax'] ) && $log['others']['new_tax'] ){
                                        $new_tax =  $log['others']['new_tax'];
                                        $sub_html = '<ul>';
                                        foreach ( $new_tax as $tax_k => $tax_v ) {
                                            $sub_html .= '<li><strong>';
                                            
                                            if( isset( $sub_levels[$tax_k] ) ){
                                                $sub_html .= $sub_levels[$tax_k]->name;
                                            } else {
                                                $sub_html .= $tax_k;
                                            }

                                            $sub_html .= '</strong>: ' . $tax_v . '</li>';
                                        }
                                        $sub_html .= '</ul>';
                                    }
                                    $message = sprintf( __( $message, AEMI_TEXT_DOMAIN ) . $sub_html, '<code>' . $users_names[$log['user_generated_log']] . '</code>', '<code>' . $users_names[$user_id] . '</code>' );
                                    break;
                            }
    
                            if( isset( $log['others']['sprintf'] ) ){
                                sort($log['others']['sprintf']);
                                $message = sprintf( __( $message, AEMI_TEXT_DOMAIN ), ...$log['others']['sprintf'] );
                            }
    
                            $html .= '<span class="aemi-block-log__message-content__message" >' . $message . '</span>';
                            
                        $html .= '</div>';
                    $html .= '</div>';
                }
            } else {
                $html .= '<p>' . __( 'No logs at the moment', AEMI_TEXT_DOMAIN ) . '</p>';
            }
            
            $html .= '</pre>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Enter an array with the key using the category id and the value using the manual points
     */
    public function updateUserManuallyTaxonomiesScores( $user_id = false, $taxonomies = array() ){
        if( $user_id ){
            update_user_meta( $user_id, $this->getUserMetaName__UserManuallyTaxonomiesScores(), $taxonomies );
        }

        return false;
    }

    /***
     * return the category id and the value using the manual points
     */
    public function getUserManuallyTaxonomiesScores( $user_id = false ){
        if( $user_id ){
            $item = get_user_meta( $user_id, $this->getUserMetaName__UserManuallyTaxonomiesScores(), true );

            return ( $item ? $item : array() );
        }

        return array();
    }

    /**
      * Returns the total points manually added by the administrator
      *
      * @param $user_id
      * @return Int
      */
    protected function getUserScoreManuallyAddedPoints( $user_id = false ){
        $value = ( $user_id ? get_user_meta( $user_id, $this->getUserMetaName__UserScoreManuallyAddedPoints(), true ) : false );
        return ( $value ? $value :0 ); 
    }

    /**
      * Updates the total points added manually
      *
      * @param Int $user_id
      * @param Int $points
      * @return Boolean
     */
    protected function updateUserScoreManuallyAddedPoints( $user_id = false, $points = false ){
        if( $user_id && $points !== '' ){
            update_user_meta( $user_id, $this->getUserMetaName__UserScoreManuallyAddedPoints(), $points );

            //Recalculate points user
            $this->userRecalculateAndUpdateTotalScore( $user_id );
            return true;
        }
        return false;
    }

    /**
     * Returns the post categories related to gamification
     * 
     * @param Int $post_id
     * @param Int $return_specific_parent
     * 
     * @return array
     */
    public static function get_posts_aemi_content_level( $post_id, $return_specific_parent = 'false', $hierarchy = false ){
        $return_data = array();

        if( $post_id ){
            $level = get_the_terms( $post_id, AemiLevelUsers::get_taxonomy_name() );

            if( $return_specific_parent !== 'false' ){

                foreach ( $level as $key => $value ) {
                    if( $value->parent == $return_specific_parent ){
                        $return_data[ $value->term_id] = $value;
                        unset( $level[ $key ] );
                    }
                }
                
                if( $level && $hierarchy ){
                    foreach ( $level as $key => $value ) {
                        if( isset( $return_data[ $value->term_id ] ) ){
                            $return_data[ $value->term_id]['classification'][] = $value;
                        }
                    }
                }
                
            } else {
                if( $hierarchy ){

                    foreach ( $level as $key => $value ) {
                        if( $value->parent == 0 ){
                            $return_data[ $value->term_id] = $value;
                            unset( $level[ $key ] );
                        }
                    }
                    
                    if( $level ){
                        foreach ( $level as $key => $value ) {
                            if( isset( $return_data[ $value->term_id ] ) ){
                                $return_data[ $value->term_id]['classification'][] = $value;
                            }
                        }
                    }

                } else {
                    $return_data = $level;
                }
            }
        }

        return $return_data;
    }

    /**
     * Returns the post categories IDs related to gamification
     * 
     * @param Int $post_id
     * @param Int $return_specific_parent
     * 
     * @return array
     */
    public static function get_posts_aemi_content_level_ids( $post_id, $return_specific_parent = 'false', $hierarchy = false ){
        $level_post_access = self::get_posts_aemi_content_level( $post_id, $return_specific_parent, $hierarchy );
        $new = array();

        if( $level_post_access ){
            foreach ( $level_post_access as $key => $value ) {
                $new[$value->term_id] = $value->term_id;
            }
        }

        return $new;
    }

    /**
     * Check that the user is at the correct level to access content
     * 
     * @param Int $user_id
     * @param Int $post_id
     * 
     * @return Boolean
     */
    protected function checkThatTheUserIsAtTheCorrectLevelToAccessContent( $user_id, $post_id ){
        $levels_user_permission = $this->returnIDUserLevelRelatedTaxonomiesAndSubTaxonomies( $user_id, false, true );
        $level_post_access = self::get_posts_aemi_content_level_ids( $post_id, 'false', false );
        $return_data = false;

        if( $levels_user_permission && $level_post_access ){
            foreach ( $levels_user_permission as $level_id ) {
                if( in_array( $level_id, $level_post_access ) ){
                    $return_data = true;
                    break;
                }
            }
        }

        if( current_user_can('administrator') ){
            $return_data = true;
        }

        return $return_data;
    }

    /**
     * Return the taxonomy ID of levels related to current and previous level including subcategories
     * 
     * @param Int $user_id
     * @param Int/Boolean $custom_score
     * @return Array
     */
    protected function returnIDUserLevelRelatedTaxonomiesAndSubTaxonomies( $user_id = false, $custom_score = false, $ignore_sub_categories = false ){
        $return_data = array();

        if( $user_id ){

            $levels = $this->get_levels();
            $user_score = ( $custom_score !== false ? $custom_score : $this->getUserScore( $user_id ) );

            if( $levels ){
                foreach ( $levels as $level ) {
                    $return_data[] = $level->term_id;
                    
                    if( $ignore_sub_categories == false && $level->classification ){
                        foreach ( $level->classification as $sub ) {
                            $return_data[] = $sub->term_id;
                        }
                    }

                    //case true stop detect ids
                    if( $user_score >= $level->_start_level_at && $user_score <= $level->_starting_level_up_to ){
                        break;
                    }
                }
            }
        }

        return $return_data;
    }

    /**
     * Detect User Level based on score, it is possible to pass a score manually or it will use the user's total score
     * 
     * @param Int $user_id
     * @param Boolean $return_level_info
     * @param Int/Boolean $custom_score
     */
    protected function detectUserLevelBasedOnScore( $user_id, $return_level_info = false, $custom_score = false ){
        $current_user_level = false;

        if( $user_id ){
            
            $levels = $this->get_levels();
            $user_score = ( $custom_score !== false ? $custom_score : $this->getUserScore( $user_id ) );
    
            if( $levels ){
                foreach ( $levels as $level ) {
                    if( $user_score >= $level->_start_level_at && $user_score <= $level->_starting_level_up_to ){
                        $current_user_level = $level->term_id;
                        break;
                    }
                }
    
                if( $current_user_level == false ){
                    $use_level = $levels[0];
    
                    //if the user's score does not fit in any of the levels, he will use the last
                    if( $user_score > 0 ){
                        $use_level = end( $levels );    
                    }
    
                    $current_user_level = $use_level->term_id;
                }
            }
        }

        if( $return_level_info ){
            $current_user_level = get_term( $current_user_level, $this->taxonomy_name );

            if( $current_user_level ){
                $levels = $this->get_levels();

                if( $levels ){
                    foreach ( $levels as $key => $value ) {
                        if( $value->term_id == $current_user_level->term_id ){
                            $current_user_level = $value;
                            break;
                        }
                    }
                }
            }
        }

        return $current_user_level;
    }

     /**
      * Returns the level (Taxonomy) the user is currently at. If this information does not exist, 
      * the system will detect the user's level based on their score and will save this goal
      * so that this information already exists in the next request.
      *
      * @param $user_id
      * @return Object
      */
    protected function getUserScoreCurrentLevel( $user_id = false, $return_extra_information = false ){
        $current_user_level = ( $user_id ? get_user_meta( $user_id, $this->getUserMetaName__UserScoreCurrentLevel(), true ) : false );
        
        //If the user does not have a related level, but detects and saves this data in the meta
        if( !$current_user_level ){
            
            $current_user_level = $this->detectUserLevelBasedOnScore( $user_id );

            if( $current_user_level ){
                $this->updateUserScoreCurrentLevel( $user_id, $current_user_level );
                //As it is the first time the user is checking and registering the level, let's add the comparison level for the update notification to the same, so in the next level the user will be notified with an animation
                $this->updateUserScoreOldLevelScoreForLevelUpdateAnimationComparison( $user_id, $current_user_level );
            }
        }

        if( $current_user_level ){
            $current_user_level = get_term( $current_user_level, $this->taxonomy_name );

            if( $return_extra_information && $current_user_level ){
                $levels = $this->get_levels();

                if( $levels ){
                    foreach ( $levels as $key => $value ) {
                        if( $value->term_id == $current_user_level->term_id ){
                            $current_user_level = $value;
                            break;
                        }
                    }
                }
            }
        }

        return ( $current_user_level ? $current_user_level : false );
    }

    /**
     * Get Old taxonomy level score for level update animation comparison
     * 
     * @param Int $user_id
     * @return Object
     */
    protected function getUserScoreCurrentOldLevelScoreForLevelUpdateAnimationComparison( $user_id = false, $return_extra_information = false ){
        $current_user_old_level = ( $user_id ? get_user_meta( $user_id, $this->getUserMetaName__UserScoreCurrentOldLevelScoreForLevelUpdateAnimationComparison(), true ) : false );
      
        //If the user does not have a related level, but detects and saves this data in the meta
        if( !$current_user_old_level ){
            //Generate scores
            $this->getUserScoreCurrentLevel( $user_id );
            
            //get old score
            $current_user_old_level = ( $user_id ? get_user_meta( $user_id, $this->getUserMetaName__UserScoreCurrentOldLevelScoreForLevelUpdateAnimationComparison(), true ) : false );
        }

        if( $current_user_old_level ){
            $current_user_old_level = get_term( $current_user_old_level, $this->taxonomy_name );

            if( $return_extra_information && $current_user_old_level ){
                $levels = $this->get_levels();

                if( $levels ){
                    foreach ( $levels as $key => $value ) {
                        if( $value->term_id == $current_user_old_level->term_id ){
                            $current_user_old_level = $value;
                            break;
                        }
                    }
                }
            }
        }

        return ( $current_user_old_level ? $current_user_old_level : false );
    }

    /**
     * Update User ScoreCurrent old level score for level update animation comparison with current level
     * 
     * @param Int $user_id
     * 
     * @return Boolean
     */
    protected function updateUserScoreCurrentOldLevelScoreForLevelUpdateAnimationComparisonWithCurrentLevel( $user_id ){
        $current_user_level = $this->getUserScoreCurrentLevel( $user_id );
        $this->updateUserScoreOldLevelScoreForLevelUpdateAnimationComparison( $user_id, $current_user_level );

        return true;
    }

    /**
     * Check if there has been any level upgrade on the user.
     * 
     * Usually this is used to know if there has been a level change and a new level 
     * animation has not yet been notified to the user in the frontend.
     * 
     * @param Int $user_id
     * 
     * @return Boolean
     */
    protected function checkIfThereWasAnyLevelUpgradeOnTheUser( $user_id ){
        $current_user_level     = $this->getUserScoreCurrentLevel( $user_id );
        $current_user_old_level = $this->getUserScoreCurrentOldLevelScoreForLevelUpdateAnimationComparison( $user_id );
        
        return ( $current_user_level != $current_user_old_level ? true : false );
    }

    /**
     * Update user level by saving corresponding taxonomy id
     */
    protected function updateUserScoreCurrentLevel( $user_id = false, $current_level_id = false ){

        if( $user_id && $current_level_id ){
            $current_user_level = get_term( $current_level_id, $this->taxonomy_name );

            if( $current_user_level ){
                update_user_meta( $user_id, $this->getUserMetaName__UserScoreCurrentLevel(), $current_level_id );
                return true;
            }
        }

        return false;
    }

    /**
     * Update Old taxonomy level score for level update animation comparison
     */
    protected function updateUserScoreOldLevelScoreForLevelUpdateAnimationComparison( $user_id = false, $current_level_id = false ){

        if( $user_id && $current_level_id ){
            $current_user_level = get_term( $current_level_id, $this->taxonomy_name );

            if( $current_user_level ){
                update_user_meta( $user_id, $this->getUserMetaName__UserScoreCurrentOldLevelScoreForLevelUpdateAnimationComparison(), $current_level_id );
                return true;
            }
        }

        return false;
    }

    /**
     * This method should be used with great caution, as it will reset all contents viewed, after reset, 
     * all contents will be summed up to a certain level as seen, even if the user has not seen it.
     * 
     * It works this way to be able to manually change the user level.
     * 
     * @param Int $user_id
     * @param Int $level_id
     */
    protected function forcesUserLevelByChangingTheContentViewed( $user_id, $level_id ){
        //reset posts viewed
        update_user_meta( $user_id, $this->getUserMetaName__UserViewedPageRelatedToScores(), array() );

        $get_levels = $this->get_levels();

        if( $get_levels ){
            $taxs_terms = array();

            foreach ( $get_levels as $tax ) {
                if( $tax->term_id == $level_id ){
                    break;
                }
                $taxs_terms[] = $tax->term_id;
            }
            
            if( $taxs_terms ){
                $related_posts = get_posts( array(
                    'post_type' => 'post',
                    'posts_per_page' => 9999,
                    'tax_query' => array(
                        array(
                            'taxonomy' => $this->taxonomy_name,
                            'field' => 'term_id',
                            'terms' => $taxs_terms,
                            'include_children' => false,
                            'operator' => 'IN'
                        )
                    )
                ) );
    
                if( $related_posts ){
                    $posts_to_save = array();

                    foreach ( $related_posts as $loop_post ) {
                        $posts_to_save[$loop_post->ID] = $this->formatViewedPageIDAndSumScore( $loop_post->ID );
                    }

                    if( $posts_to_save ){
                        update_user_meta( $user_id, $this->getUserMetaName__UserViewedPageRelatedToScores(), $posts_to_save );
                    }
                }
            }
        }

        $this->removeRelatedCaseHasDeleted( $user_id );

        //Recalculate points user
        $this->updateUserTaxonomyLevelScore( $user_id );
        $this->userRecalculateAndUpdateTotalScore( $user_id );

        return true;
    }

     /**
      * Returns the pages viewed by the user
      *
      * @param $user_id
      */
    protected function getUserViewedPageRelatedToScores( $user_id = false, $return_posts_info = false ){
        $value = ( $user_id ? get_user_meta( $user_id, $this->getUserMetaName__UserViewedPageRelatedToScores(), true ) : false );

        if( $value && $return_posts_info ){
            $posts_ids = array_keys( $value );
            
            if( $posts_ids ){
                $get_posts_data = get_posts( array( 'post__in' => $posts_ids, 'posts_per_page' => -1, 'post_type' => $this->related_posts_types ) );

                if( $get_posts_data ){
                    foreach ( $get_posts_data as $current_post ) {
                        if( isset( $value[$current_post->ID] ) && $value[$current_post->ID] ){
                            $value[$current_post->ID]['post_info'] = $current_post;
                        }
                    }
                }
            }
        }

        return ( $value ? $value : array() );
    }

    /**
     * Returns the pages viewed by the user
     *
     * @param $user_id
     */
   protected function getUserViewedPageRelatedToScoresOriginal( $user_id = false, $return_posts_info = false ){
       $value = ( $user_id ? get_user_meta( $user_id, $this->getUserMetaName__UserViewedPageRelatedToScoresOriginal(), true ) : false );

       if( $value && $return_posts_info ){
           $posts_ids = array_keys( $value );
           
           if( $posts_ids ){
               $get_posts_data = get_posts( array( 'post__in' => $posts_ids, 'posts_per_page' => -1, 'post_type' => $this->related_posts_types ) );

               if( $get_posts_data ){
                   foreach ( $get_posts_data as $current_post ) {
                       if( isset( $value[$current_post->ID] ) && $value[$current_post->ID] ){
                           $value[$current_post->ID]['post_info'] = $current_post;
                       }
                   }
               }
           }
       }

       return ( $value ? $value : array() );
   }

    /**
     * Returns user score data based on categories
     */
    protected function getUserTaxonomiesScores( $user_id = false, $no_ignore_already_earned_for_related_content = true ){
        $value = ( $user_id ? get_user_meta( $user_id, $this->getUserMetaName__UserTaxonomiesScores(), true ) : false );

        //get_ignore_wikimi_points_already_earned_for_related_content
        return ( $value ? $value : array() );
    }

    /**
     * Returns the total score gained by categories
     * 
     * @param Int $user_id
     */
    protected function getUserTotalScoreByTaxonomiesScores( $user_id = false, $do_not_use_manual_taxonomy = false ){
        $return_data = false;

        if( $user_id ){
            $getUserTaxonomiesScores = $this->getUserTaxonomiesScores( $user_id );
            $getUserManuallyTaxonomiesScores = $this->getUserManuallyTaxonomiesScores( $user_id );

            $levels = $this->get_levels();
            
            $taxonomy = array();
            $subtaxonomy = array();

            //Creates attributes that we will use to calculate information
            foreach ( $levels as $key => $value ) {
                $taxonomy[$value->term_id] = $value;
                $taxonomy[$value->term_id]->_score_limit_actual_total_points = 0;
                $taxonomy[$value->term_id]->_score_limit_current_point_total_with_limit = 0;

                if( $value->classification ){
                    foreach ( $value->classification as $sub_key => $sub_value ) {
                        $manual_points_taxt = ( $do_not_use_manual_taxonomy == false && isset( $getUserManuallyTaxonomiesScores[$sub_value->term_id] ) &&  $getUserManuallyTaxonomiesScores[$sub_value->term_id] != '' && $getUserManuallyTaxonomiesScores[$sub_value->term_id] != 0 ? $getUserManuallyTaxonomiesScores[$sub_value->term_id] : 0 );
                        $subtaxonomy[$sub_value->term_id] = &$value->classification[$sub_key];
                        $subtaxonomy[$sub_value->term_id]->_score_manual_points_taxonomy = $manual_points_taxt;
                        $subtaxonomy[$sub_value->term_id]->_score_limit_actual_total_points_minus_manual_points = 0 ;
                        $subtaxonomy[$sub_value->term_id]->_score_limit_actual_total_points = 0 + $manual_points_taxt;
                        $subtaxonomy[$sub_value->term_id]->_score_limit_current_point_total_with_limit = 0;
                        
                        if( $subtaxonomy[$sub_value->term_id]->_score_limit_current_point_total_with_limit > $subtaxonomy[$sub_value->term_id]->_score_limit ){
                            $subtaxonomy[$sub_value->term_id]->_score_limit_current_point_total_with_limit = $subtaxonomy[$sub_value->term_id]->_score_limit;
                        }
                    }
                }
            }

            if( $getUserTaxonomiesScores ){
                //Blocking and summing the total points earned by subcategories
                foreach( $getUserTaxonomiesScores as $term_id => $info ){
                    if( isset( $subtaxonomy[$term_id] ) && $subtaxonomy[$term_id] ){
                        $subtaxonomy[$term_id]->_score_limit_actual_total_points += $info['score'];
                        $subtaxonomy[$term_id]->_score_limit_actual_total_points_minus_manual_points += $info['score'];
                        $subtaxonomy[$term_id]->_score_limit_current_point_total_with_limit += $info['score'];

                        if( $subtaxonomy[$term_id]->_score_limit_current_point_total_with_limit > $subtaxonomy[$term_id]->_score_limit ){
                            $subtaxonomy[$term_id]->_score_limit_current_point_total_with_limit = $subtaxonomy[$term_id]->_score_limit;
                        }
                    }
                }
            }


            // calculate manual points of category  
            if( $subtaxonomy ){
                foreach( $subtaxonomy as $term_id => $subtax ){

                    //additional_manual_points 
                    $subtaxonomy[$term_id]->_score_limit_current_point_total_with_limit = $subtaxonomy[$term_id]->_score_limit_current_point_total_with_limit + $subtaxonomy[$term_id]->_score_manual_points_taxonomy;
                    //reprevent limit
                    if( $subtaxonomy[$term_id]->_score_limit_current_point_total_with_limit > $subtaxonomy[$term_id]->_score_limit ){
                        $subtaxonomy[$term_id]->_score_limit_current_point_total_with_limit = $subtaxonomy[$term_id]->_score_limit;
                    } else if ( $subtaxonomy[$term_id]->_score_limit_current_point_total_with_limit < 0  ){
                        $subtaxonomy[$term_id]->_score_limit_current_point_total_with_limit = 0;
                    }
                }
            }

            //Calculating values of scores earned in categories based on sub
            foreach ( $taxonomy as $key => $tax ) {
                foreach ( $tax->classification as $sub_tax_key => $sub_tax ) {
                    $taxonomy[$key]->_score_limit_actual_total_points_minus_manual_points += $sub_tax->_score_limit_actual_total_points_minus_manual_points;
                    $taxonomy[$key]->_score_limit_actual_total_points += $sub_tax->_score_limit_actual_total_points;
                    $taxonomy[$key]->_score_limit_current_point_total_with_limit += $sub_tax->_score_limit_current_point_total_with_limit;

                    if(  $taxonomy[$key]->_score_limit_current_point_total_with_limit > $taxonomy[$key]->_max_level_points_in ){
                         $taxonomy[$key]->_score_limit_current_point_total_with_limit = $taxonomy[$key]->_max_level_points_in;
                    }
                }
            }

            $return_data = $taxonomy;
        }

        return $return_data;
    }

    /**
        * get real user score based on category scores and current level.
        * This method ignores the user's current category (level) scores.
        * This method does not return manually added scores
        *
        * @param Int $user_id
     */
    protected function getUserTotalRealScoreBasedOnCategoryScoresAndCurrentLevel( $user_id = false ){
        $return_data = array(
            'points_based_on_limits' => 0,
            'points_without_the_limit_lock' => 0
        );

        $tax_user_pontuations = $this->getUserTotalScoreByTaxonomiesScores( $user_id );

        if( $tax_user_pontuations ){
            $current_user_level = $this->getUserScoreCurrentLevel( $user_id, true );

            foreach ( $tax_user_pontuations as $tax ) {

                $return_data['points_based_on_limits'] += $tax->_score_limit_current_point_total_with_limit;
                $return_data['points_without_the_limit_lock'] += $tax->_score_limit_actual_total_points;

                if( $tax->term_id == $current_user_level->term_id ){
                    break;
                }
            }

        }

        return $return_data;
    }

    /**
     * Updates user level category by user
     */
    protected function updateUserTaxonomyLevelScore( $user_id = false ){
        if( $user_id ){
            $user_viewed_pages = $this->getUserViewedPageRelatedToScores( $user_id );
            $ignore_posts_to_update = $this->get_ignore_wikimi_points_already_earned_for_related_content( $user_id );

            $data = array();

            if( $user_viewed_pages ){

                foreach( $user_viewed_pages as $post_id => $info ){

                    if( $ignore_posts_to_update && is_array( $ignore_posts_to_update ) && isset( $ignore_posts_to_update[$post_id] ) ){
                        //ignorar pontos pois esse post está relacionado a outro conteúdo
                    } else {
                    
                        if( isset($info['score_classification']) ){

                            foreach( $info['score_classification'] as $tem_id => $score ){
    
                                if( isset( $data[$tem_id] ) && $data[$tem_id] ){
                                    
                                    $data[$tem_id]['score'] = ( $data[$tem_id]['score'] + $score );
                                    $data[$tem_id]['posts'][$post_id] = array(
                                        'post_id' => $post_id,
                                        'info' => ( isset( $info['date'] ) && $info['date'] ? $info['date'] : '' ),
                                        'score' => $score
                                    );
    
                                } else {
                                    
                                    $data[$tem_id] = array(
                                        'score' => $score,
                                        'posts' => array(
                                            $post_id => array(
                                                'post_id' => $post_id,
                                                'info' => ( isset( $info['date'] ) && $info['date'] ? $info['date'] : '' ),
                                                'score' => $score
                                            )
                                        )
                                    );
                                }
                            }
                        }
                    }
                }
            }
            
            update_user_meta( $user_id, $this->getUserMetaName__UserTaxonomiesScores(), $data );
            return true;
        }

        return false;
    }
    
    /**
     * format ID of page viewed and sum score.
     * Just to format the page data viewed
     * 
     * @param Int $post_id
     * @return Array
     */
    protected function formatViewedPageIDAndSumScore( $post_id ){
        $scores_of_post = $this->getPostScoreClassificationEnabledAndScoreByPost( $post_id );

        $args = array(
            'user_generated_record' => get_current_user_id(),
            'post_id' => $post_id,
            'date' => date('Y-m-d H:i:s'),
            'score_classification_total' => array_sum( $scores_of_post ),
            'score_classification' => $scores_of_post,
        );

        return $args;
    }

    /**
     * Check if the user has already earned points with this page
     */
    public function checkUserHasAlreadyEarnedPointsInPost( $user_id = false, $post_id = false ){
        if( $user_id && $post_id ){
            $user_viewed_pages = $this->getUserViewedPageRelatedToScores( $user_id );
            if( isset( $user_viewed_pages[$post_id] ) ){
                return true;
            } else {
                $data = $this->get_ignore_wikimi_points_already_earned_for_related_content( $user_id );

                if( isset( $data[$post_id] ) && $data[$post_id] ){
                    $is_valid = false;

                    foreach ( $data[$post_id] as $data_id ) {
                        if( array_key_exists( $data_id, $user_viewed_pages ) ){
                            $is_valid = true;
                            break;
                        }
                    }

                    if( $is_valid ){
                        return true;
                    }
                }
            }
        }
        
        return false;
    }

    /**
     * Add page as viewed in user and sum new score
     * 
     * This code already adds the page as viewed and does the sum of the score if the user has never viewed this post
     */
    protected function insertInUserViewedPageIDAndSumScore( $user_id = false, $post_id = false ){
        
        if( $user_id && $post_id ){
            $user_viewed_pages = $this->getUserViewedPageRelatedToScores( $user_id );
            $user_viewed_pages_real = $this->getUserViewedPageRelatedToScoresOriginal( $user_id );
            $get_current_user_score = $this->getUserScore( $user_id );

            if( !isset( $user_viewed_pages[$post_id] ) ){
                $args = $this->formatViewedPageIDAndSumScore( $post_id );

                $get_current_user_score = ( $args['score_classification_total'] > 0 ? $args['score_classification_total'] : 0 ) + $get_current_user_score;

                //Update data
                $user_viewed_pages[$post_id] = $args;

                /**
                 * The two updates are similar, but the first one is used to calculate the user's level among other things.
                 * 
                 * The second is to keep the actual history of user points and views.
                 * 
                 * The second option should not be changed unless it is really necessary.
                 */
                update_user_meta( $user_id, $this->getUserMetaName__UserViewedPageRelatedToScores(), $user_viewed_pages );
                update_user_meta( $user_id, $this->getUserMetaName__UserViewedPageRelatedToScoresOriginal(), $user_viewed_pages );

                $this->updateUserTaxonomyLevelScore( $user_id );

                //Recalculate points user
                $this->userRecalculateAndUpdateTotalScore( $user_id );
            }

            return $get_current_user_score;
        }

        return 0;
    }

    /**
     * Recalculate and update user total score based on user data and manual points
     * 
     * @param Int $user
     */
    protected function userRecalculateAndUpdateTotalScore( $user_id ){

        $score_info = $this->getUserTotalRealScoreBasedOnCategoryScoresAndCurrentLevel( $user_id );

        if( isset( $score_info['points_based_on_limits'] ) ){
            //Earned score based on posts
            $total_points = $score_info['points_based_on_limits'];

            //Manual points
            $points_manually = $this->getUserScoreManuallyAddedPoints( $user_id );

            if( $points_manually ){
                $total_points += $points_manually;
            }

            update_user_meta( $user_id, $this->getPostMetaName__UserScore(), $total_points );

            $new_level_id = $this->detectUserLevelBasedOnScore( $user_id );

            if( $new_level_id ){
                $this->updateUserScoreCurrentLevel( $user_id, $new_level_id );
            }
        }

        do_action( 'aemi_gamification_and_content_level_after_upate_user', $user_id );
    }

    /**
     * Returns the score manually added by the administrator
     *
     * @param $user_id
     */
    protected function getUserScorePointsManuallyAddedByAdmin( $user_id = false ){
        $value = ( $user_id ? get_user_meta( $user_id, $this->getUserMetaName__UserScorePointsManuallyAddedByAdmin(), true ) : false );
        return ( $value ? $value :0 ); 
    }

   /**
    * Returns the score of user
    *
    * @param $user_id
    */
    protected function getUserScore( $user_id = false ){
        $value = ( $user_id ? get_user_meta( $user_id, $this->getPostMetaName__UserScore(), true ) : false );
        return ( $value && $value > 0 ? $value : 0 ); 
    }

    /**
     * Returns whether a post's specific ratings are enabled or not
     */
    protected function getPostScoreClassificationEnabledByPost( $post_id ){
        $value = ( $post_id ? get_post_meta( $post_id, $this->getPostMetaName__ScoreClassificationEnabled(), true ) : false );
        return ( $value ? $value : array() ); 
    }

    /**
     * Returns your ratings and scores for each category
     */
    protected function getPostScoreClassificationByPost( $post_id ){
        $value = ( $post_id ? get_post_meta( $post_id, $this->getPostMetaName__ScoreClassification(), true ) : false );
        return ( $value ? $value : array() ); 
    }

    /**
     * Returns whether a post's specific ratings are enabled or not
     */
    protected function verifyIsLevelIsEnabledToPontuationByPost( $post_id, $level_id ){
        $value = $this->getPostScoreClassificationEnabledByPost( $post_id );
        return ( isset( $value[$level_id] ) ? true : false ); 
    }

    /**
     * Returns enabled subcategories and their scores
     */
    protected function getPostScoreClassificationEnabledAndScoreByPost( $post_id ){
        $score_tax_enabled = $this->getPostScoreClassificationEnabledByPost( $post_id );
		$score_classification = $this->getPostScoreClassificationByPost( $post_id );

        $return_value = array();

        foreach ( $score_classification as $key => $value) {
            if( in_array( $key, $score_tax_enabled ) ){
                $return_value[$key] = ( $value && $value > 0 ? $value : 0 );
            }
        }

        return $return_value;
    }

    /**
     * Adiciona um conteúdo como conteúdo relacionado a outro conteúdo, assim previne pontos duplicados pelo mesmo conteúdo
     * Foi necessário adicionar essa funcionalidade por causa do GoTo Webinars para previnir pontos duplicados
     */
    protected function add_ignore_wikimi_points_already_earned_for_related_content( $post_id_to_ignore, $post_id_to_ignore_related, $user_id ){
        $data = $this->get_ignore_wikimi_points_already_earned_for_related_content( $user_id );
        
        $post = get_post( $post_id_to_ignore );

        if( $post ){
            if( !isset( $data[$post->ID] )){
                $data[$post->ID] = [];
            }

            $data[$post->ID][$post_id_to_ignore_related] = $post_id_to_ignore_related;

            $this->update_ignore_wikimi_points_already_earned_for_related_content( $user_id, $data );
        }
    }

    protected function update_ignore_wikimi_points_already_earned_for_related_content( $user_id, $data ){
        update_user_meta( $user_id, $this->ignore_wikimi_points_already_earned_for_related_content, $data );
    }

    protected function get_ignore_wikimi_points_already_earned_for_related_content( $user_id ){
        $value = get_user_meta( $user_id, $this->ignore_wikimi_points_already_earned_for_related_content, true );
        return ( $value ? $value : [] );
    }
    
    protected function removeRelatedCaseHasDeleted( $user_id ){
        $user_viewed_pages = $this->getUserViewedPageRelatedToScores( $user_id );
        $ignore_posts_to_update = $this->get_ignore_wikimi_points_already_earned_for_related_content( $user_id );
        $updated = false;

        if( $user_viewed_pages ){
            foreach ( $ignore_posts_to_update as $element_id => $element ) {
                foreach ( $element as $sub_id => $sub_val ) {

                    if( !isset( $user_viewed_pages[$sub_id] ) ){
                        unset( $ignore_posts_to_update[$element_id][$sub_id] );
                        $updated = true;
                    }
                }

                if( !$ignore_posts_to_update[$element_id] ){
                    unset(  $ignore_posts_to_update[$element_id] );
                    $updated = true;
                }
            }
        }

        if( $updated ){
            $this->update_ignore_wikimi_points_already_earned_for_related_content( $user_id, $ignore_posts_to_update );
        }
    }
}
