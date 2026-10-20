<?php 

if( !class_exists('AemiFilterPostsBasedOnUserLevel') ){
    class AemiFilterPostsBasedOnUserLevel extends AemiLevelUsers {
        
        function __construct(){
            //add_action( 'pre_get_posts', array( $this, 'ShowOnlyUserLevelRelatedContent' ), 999, 1 );
        }

        /**
         * Filter
         */
        public function ShowOnlyUserLevelRelatedContent( $query ){
            if( !is_admin() && is_user_logged_in() ){
                if( $query->get('post_type') == 'post' || $query->get('post_type') == '' ){
                    if( !current_user_can('administrator') ){
                        $tax_terms = $this->returnIDUserLevelRelatedTaxonomiesAndSubTaxonomies( get_current_user_id(), false, true );
    
                        if( $tax_terms ){
                            $tax_query = (array) $query->get( 'tax_query' );
                            $tax_query[] = array(
                                'taxonomy' => $this->taxonomy_name,
                                'field' => 'term_id',
                                'terms' => $tax_terms,
                                'include_children' => false,
                                'operator' => 'IN'
                            );
    
                            $query->set( 'tax_query', $tax_query );
                        }
                    }
                }
            }
        }

        public static function getQueryTaxArgsShowOnlyUserLevelRelatedContent(){
            global $aemiHelpLevelUsersClassLoaded;
            $tax_terms = $aemiHelpLevelUsersClassLoaded->returnIDUserLevelRelatedTaxonomiesAndSubTaxonomies( get_current_user_id(), false, true );

            return array(
                'taxonomy' => $aemiHelpLevelUsersClassLoaded->taxonomy_name,
                'field' => 'term_id',
                'terms' => $tax_terms,
                'include_children' => false,
                'operator' => 'IN'
            );;
        }
    }

    new AemiFilterPostsBasedOnUserLevel();
}