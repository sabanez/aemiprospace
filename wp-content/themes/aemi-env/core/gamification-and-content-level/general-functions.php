<?php

/**
 * Check if the user has already earned points with this page
 */
if( !function_exists('check_user_has_already_earned_points_in_post') ){
    function check_user_has_already_earned_points_in_post( $user_id = false, $post_id = false ){
        global $aemiHelpLevelUsersClassLoaded;
        return $aemiHelpLevelUsersClassLoaded->checkUserHasAlreadyEarnedPointsInPost( $user_id, $post_id );
    }
}

/**
 * Return current user level
 */
if( !function_exists('get_aemi_current_user_score_current_level') ){
    function get_aemi_current_user_score_current_level(){
        global $aemiHelpLevelUsersClassLoaded;
        $current_level = $aemiHelpLevelUsersClassLoaded->getCurrentUserLevel(get_current_user_id(), true);
        if( $current_level ) return $current_level;
        return '';
    }
}

if( !function_exists('get_aemi_current_user_score') ){
    function get_aemi_current_user_score(){
        global $aemiHelpLevelUsersClassLoaded;
        $current_level = $aemiHelpLevelUsersClassLoaded->getSpecificUserScore( get_current_user_id() );
        if( $current_level ) return $current_level;
        return '';
    }
}

if( !function_exists('get_aemi_user_viewed_page_related_to_scores') ){
    function get_aemi_user_viewed_page_related_to_scores( $user_id = false, $return_posts_info = false ){
        global $aemiHelpLevelUsersClassLoaded;
        $current_level = $aemiHelpLevelUsersClassLoaded->getSpecificUserViewedPageRelatedToScores( $user_id, $return_posts_info );
        if( $current_level ) return $current_level;
        return '';
    }
}

if( !function_exists('get_aemi_user_total_score_by_taxonomies_scores') ){
    function get_aemi_user_total_score_by_taxonomies_scores( $user_id = false ){
        global $aemiHelpLevelUsersClassLoaded;
        $current_level = $aemiHelpLevelUsersClassLoaded->getSpecificUserTotalScoreByTaxonomiesScores( $user_id );
        if( $current_level ) return $current_level;
        return '';
    }
}

/**
 * Return current user level percent to new level up
 */
if( !function_exists('get_aemi_current_user_percentage_level_to_level_up') ){
    function get_aemi_current_user_percentage_level_to_level_up(){
        global $aemiHelpLevelUsersClassLoaded;
        $current_level = $aemiHelpLevelUsersClassLoaded->getPercentageLevelToLevelUp(get_current_user_id());
        if( $current_level ) return $current_level;
        return 0;
    }
}

if( !function_exists( 'get_posts_aemi_content_level' ) ){
    /**
     * Returns the post categories related to gamification
     * 
     * @param Int $post_id
     * @param Int $return_specific_parent
     * 
     * @return array
     */
    function get_posts_aemi_content_level( $post_id, $return_specific_parent = 'false', $hierarchy = false ){
        return AemiLevelUsers::get_posts_aemi_content_level( $post_id, $return_specific_parent, $hierarchy );
    }
}

if( !function_exists( 'get_aemi_help_level_users_get_levels' ) ){
    function get_aemi_help_level_users_get_levels(){
        global $aemiHelpLevelUsersClassLoaded;
        return $aemiHelpLevelUsersClassLoaded->getLevels();
    }
}

if( !function_exists( 'get_posts_aemi_content_level_names' ) ){
    
    /**
     * Returns only the titles of the levels
     * 
     * @param Int $post_id
     * 
     * @return Array
     */
    function get_posts_aemi_content_level_names( $post_id ){
        $return_data = array();
        $return_tax = get_posts_aemi_content_level( $post_id, 0, false );

        if( $return_tax ){
            foreach ( $return_tax as $key => $value ) {
                $return_data[] = $value->name;
            }
        }

        return $return_data;
    }
}

if( !function_exists( 'add_aemi_ignore_wikimi_points_already_earned_for_related_content' ) ){
    function add_aemi_ignore_wikimi_points_already_earned_for_related_content( $post_id_to_ignore, $post_id_to_ignore_related, $user_id ){
        global $aemiHelpLevelUsersClassLoaded;
        return $aemiHelpLevelUsersClassLoaded->addIgnoreWikimiPointsAlreadyEarnedForRelatedContent( $post_id_to_ignore, $post_id_to_ignore_related, $user_id );
    }
}

if( !function_exists( 'get_aemi_ignore_wikimi_points_already_earned_for_related_content' ) ){
    function get_aemi_ignore_wikimi_points_already_earned_for_related_content( $user_id ){
        global $aemiHelpLevelUsersClassLoaded;
        return $aemiHelpLevelUsersClassLoaded->getignoreWikimiPointsAlreadyEarnedForRelatedContent( $user_id );
    }
}

if( !function_exists( 'add_aemi_to_in_user_viewed_page_ID_and_sum_score' ) ){
    function add_aemi_to_in_user_viewed_page_ID_and_sum_score( $user_id = false, $post_id = false ){
        global $aemiHelpLevelUsersClassLoaded;
        return $aemiHelpLevelUsersClassLoaded->addToInUserViewedPageIDAndSumScore( $user_id, $post_id );
    }
}