<?php 

class AemiHelpLevelUsersClass extends AemiLevelUsers {
    
    /**
     * Return current user level
     */
    public function getCurrentUserLevel( $user_id, $extra = false ){
        return $this->getUserScoreCurrentLevel( $user_id, $extra );
    }

    /**
     * Get current level percent of to limit
     */
    public function getPercentageLevelToLevelUp( $user_id ){
        $currentLevel = $this->getCurrentUserLevel( $user_id, true );
        $getUserScore = $this->getUserScore( $user_id ) - $currentLevel->_start_level_at;

        $total_percent = $this->calculate_percentage_of_value( $getUserScore, ( $currentLevel->_starting_level_up_to+1 - $currentLevel->_start_level_at) );
        return round( $total_percent );
    }

    public function calculate_percentage_of_value( $valor, $total ) {
        return ( $valor * 100 ) / $total;
    }
    
    public function getSpecificUserScore( $user_id ){
        return $this->getUserScore( $user_id );
    }

    public function getSpecificUserViewedPageRelatedToScores( $user_id = false, $return_posts_info = false ){
        return $this->getUserViewedPageRelatedToScores( $user_id, $return_posts_info );
    }

    public function getSpecificUserTotalScoreByTaxonomiesScores( $user_id ){
        return $this->getUserTotalScoreByTaxonomiesScores( $user_id );
    }

    public function getLevels(){
        return $this->get_levels();
    }

    /**
     * Relacionado aos conteúdos que estão associados a outros conteúdos
     */
    public function addIgnoreWikimiPointsAlreadyEarnedForRelatedContent( $post_id_to_ignore, $post_id_to_ignore_related, $user_id ){
        return $this->add_ignore_wikimi_points_already_earned_for_related_content( $post_id_to_ignore, $post_id_to_ignore_related, $user_id );
    }

    public function getignoreWikimiPointsAlreadyEarnedForRelatedContent( $user_id ){
        return $this->get_ignore_wikimi_points_already_earned_for_related_content( $user_id );
    }

    public function addToInUserViewedPageIDAndSumScore( $user_id = false, $post_id = false ){
        return $this->insertInUserViewedPageIDAndSumScore( $user_id, $post_id );
    }
}

/**
 * Load the class instance to get information from the gamification
 */
$aemiHelpLevelUsersClassLoaded = new AemiHelpLevelUsersClass();
