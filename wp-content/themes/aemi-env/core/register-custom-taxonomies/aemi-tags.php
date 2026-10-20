<?php 

if( !class_exists('AemiRegisterCustomTags') ){
    class aemiRegisterCustomTags {
        private $term_slug = 'aemi_tags';
        public static $term_slug_static = 'aemi_tags';

        function __construct(){
            add_action( 'init', array( $this, 'register_taxonomy' ) );
            add_action( 'admin_head', array( $this, 'admin_head' ) );

            add_action( $this->term_slug . '_edit_form_fields', array( $this, 'custom_meta_box' ) );
            add_action('created_' . $this->term_slug, array( $this, 'save_meta_data' ));
            add_action('edited_' . $this->term_slug, array( $this, 'save_meta_data' ));
            //add_action( 'pre_get_posts', array( $this, 'ShowOnlyUserLevelContentByTags' ), 1000, 1 );

            //
    		add_action( 'save_post', array( $this, 'relate_posts_to_categories_based_on_tags' ) );
        }

        public function relate_posts_to_categories_based_on_tags( $postID ){

            $tags = get_the_terms( $postID, $this->term_slug );

            $category_related = array();
            if( $tags ){
                foreach ( $tags as $tag ) {
                    $get_categories_related = aemi_get_categories_related_by_tag( $tag->term_id );
                    
                    if( $get_categories_related ){
                        $category_related = array_merge( $category_related, $get_categories_related );
                    }
                }
            }

            //Se taxonomy level
            wp_set_post_terms( $postID, $category_related, 'category', false );
        }

        public static function returnCustomizedArgsToQuery( $query = false ){
            $tax_query = array();
            $tags = array();

            //quando for uma categoria vamos retornar os conteudos baseado na categoria/tags e no usuário
            if(  $query && $query->is_category ){
                $category = get_queried_object();
                if( $category && $category->term_id ){
                    //return posts by related tags
                    $tags = aemi_get_tags_by_user_and_category( get_current_user_id(), $category->term_id );
                }
            } else {
                //quando não for uma categoria e sim uma query geral dos posts vamos 
                //retornar apenas conteúdos relacionados aos interesses dos usuário
                $tags = aemi_get_interests_by_user( get_current_user_id() );
            }

            if( $tags ){
                $tax_query = ( $query ? (array) $query->get( 'tax_query' ) : array() );
                $tax_query[] = array(
                    'taxonomy' => self::$term_slug_static,
                    'field' => 'term_id',
                    'terms' => $tags,
                    'include_children' => false,
                    'operator' => 'IN'
                );
            } else {
                //HFN|P.O.G
                $tax_query = ( $query ? (array) $query->get( 'tax_query' ) : array() );
                $tax_query[] = array(
                    'taxonomy' => self::$term_slug_static . '_hide',
                    'field' => 'slug',
                    'terms' => 'no_related_category',
                    'include_children' => false,
                    'operator' => 'IN'
                );
            }
            
            return $tax_query;
        }

        /**
         * Filter
         */
        public function ShowOnlyUserLevelContentByTags( $query ){
            if( !is_admin() && is_user_logged_in() ){
                if( $query->get('post_type') == 'post' || $query->get('post_type') == '' ){
                    $tax_query = self::returnCustomizedArgsToQuery( $query );
                    $query->set( 'tax_query', $tax_query );
                }
            }
        }

        public function admin_head(){
            ?>
                <style>
                    .components-panel .components-panel__body .editor-post-taxonomies__hierarchical-terms-list[aria-label="Tags"] ~ form .components-base-control:nth-child(2) {
                        display: none;
                    }
                </style>
            <?php 
        }

        /**
         * Register custom taxonomy
         */
        public function register_taxonomy() {

            $labels = array(
                'name'                       => _x( 'Tags', 'Taxonomy General Name', AEMI_TEXT_DOMAIN ),
                'singular_name'              => _x( 'Tag', 'Taxonomy Singular Name', AEMI_TEXT_DOMAIN ),
                'menu_name'                  => __( 'Tags', AEMI_TEXT_DOMAIN ),
                'all_items'                  => __( 'All tags', AEMI_TEXT_DOMAIN ),
                'parent_item'                => __( 'Parent level', AEMI_TEXT_DOMAIN ),
                'parent_item_colon'          => __( 'Parent level:', AEMI_TEXT_DOMAIN ),
                'new_item_name'              => __( 'New tag name', AEMI_TEXT_DOMAIN ),
                'add_new_item'               => __( 'Add new tag', AEMI_TEXT_DOMAIN ),
                'edit_item'                  => __( 'Edit tag', AEMI_TEXT_DOMAIN ),
                'update_item'                => __( 'Update tag', AEMI_TEXT_DOMAIN ),
                'view_item'                  => __( 'View tag', AEMI_TEXT_DOMAIN ),
                'separate_items_with_commas' => __( 'Separate tags with commas', AEMI_TEXT_DOMAIN ),
                'add_or_remove_items'        => __( 'Add or remove tag', AEMI_TEXT_DOMAIN ),
                'choose_from_most_used'      => __( 'Choose from the most used', AEMI_TEXT_DOMAIN ),
                'popular_items'              => __( 'Popular tags', AEMI_TEXT_DOMAIN ),
                'search_items'               => __( 'Search tags', AEMI_TEXT_DOMAIN ),
                'not_found'                  => __( 'Not found', AEMI_TEXT_DOMAIN ),
                'no_terms'                   => __( 'No tags', AEMI_TEXT_DOMAIN ),
                'items_list'                 => __( 'Tags list', AEMI_TEXT_DOMAIN ),
                'items_list_navigation'      => __( 'Tags list navigation', AEMI_TEXT_DOMAIN ),
            );

            $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => false,
            'show_in_rest'               => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            );
            
            //A temporary code to disable hierarchical
            if( is_admin() && isset( $_SERVER['REQUEST_URI'] ) && $_SERVER['REQUEST_URI'] ){
                $temp = explode( '/wp-admin/', $_SERVER['REQUEST_URI'] );

                if( isset($temp[1]) && strpos( $temp[1],'edit-tags.php?taxonomy=' . $this->term_slug ) !== -1 ){
                    $args['hierarchical'] = false;
                }
            }

            register_taxonomy( $this->term_slug, array( 'post' ), $args );

        }

        public function custom_meta_box( $term ){
            wp_nonce_field( $this->term_slug, $this->term_slug . '_meta_nonce' );
            
            $related_categories = get_term_meta( $term->term_id, '__related_categories', true );
            $related_categories = ( $related_categories ? $related_categories : array() );
            $common_tag_for_all_users = get_term_meta( $term->term_id, '__common_tag_for_all_users', true );

            $get_categories = get_categories( array(
                'hide_empty' => false,
                'numberint' => 0,
                'parent' => 0
            ) );

            if( $get_categories ){
                foreach ( $get_categories as $tax_key => $tax_value ) {
                    $get_categories[$tax_key]->sub = array();

                    $get_categories_inner = get_categories( array(
                        'hide_empty' => false,
                        'numberint' => 0,
                        'parent' => $tax_value->term_id
                    ) );

                    if( $get_categories_inner ){
                        $get_categories[$tax_key]->sub = $get_categories_inner;
                    }
                }
            }

            ?>
            <tr class="form-field">
              <th>
                <label for="_content_editor"><?php echo __( 'Therapeutic areas', AEMI_TEXT_DOMAIN ); ?>:</label>
              </th>
              <td>
                <div style="border-radius: 10px;box-shadow: 0px 0px 8px -6px rgb(0 0 0 / 54%);padding: 10px 30px;box-sizing: border-box;background: #fff;width: calc( 100% - 30px );">
                    <ul>
                        <?php if( $get_categories ): ?>
                            <?php foreach ( $get_categories as $list_category ) : ?>
                                <?php if( $list_category->sub ): ?>
                                    <li style="margin-bottom: 25px;">
                                        <span>
                                            <strong><?php echo esc_attr( $list_category->name ) ; ?></strong>
                                        </span>

                                        <ul style="background: #f7f7f7;padding: 13px 20px;margin-top: 10px;margin-bottom: 10px;">
                                            <?php foreach ( $list_category->sub as $list_sub ) : ?>
                                                <li style="padding: 5px 0;">
                                                    <label>
                                                        <input type="checkbox" name="__related_categories[]" value="<?php echo esc_attr( $list_sub->term_id ) ; ?>" <?php echo ( in_array( $list_sub->term_id, $related_categories ) ? ' checked="true" ' : '' ); ?> />
                                                        <?php echo esc_attr( $list_sub->name ) ; ?>
                                                    </label>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>

                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
              </td>
            </tr>
            <tr class="form-field">
              <th>
                <label for="_content_editor"><?php echo __( 'This is a common tag for all users', AEMI_TEXT_DOMAIN ); ?>:</label>
              </th>
              <td>
                <div style="background: #fff;display: flex;width: calc( 100% - 70px );padding: 20px;flex-direction: column;border-radius: 10px;box-shadow: 0px 0px 8px -6px rgb(0 0 0 / 54%);">
                    <div style="display: flex; margin-bottom: 15px;">
                        <span style="border-radius: 190px; box-shadow: 0px 0px 4px 0px rgb(255 255 255 / 63%);">
                            <label class="aemi-painel-checkbox" style="margin-top: 15px;">
                                <input <?php echo ( $common_tag_for_all_users ? ' checked="true"' : '' ); ?> type="checkbox" value="true" name="__common_tag_for_all_users" id="common_tag_for_all_users">
                                <span></span>
                            </label>
                        </span>
                    </div>

                    <p class="description">
                        <?php echo __( 'Common tags are used to display content to all users.', AEMI_TEXT_DOMAIN ); ?><br /><br />
                        <?php echo __( 'This type of tag is not displayed for the user to select as interest because it is common content.', AEMI_TEXT_DOMAIN ); ?>
                    </p>
                </div>
              </td>
            </tr>
            <?php
        }
        
        /**
         * Save custom meta data term
         * 
         * @param INT $term_id Return current term id
         */
        public function save_meta_data( $term_id ){
            
            // Check nonce exist
            if ( ! isset( $_POST[$this->term_slug . '_meta_nonce'] ) ) {
                return $term_id;
            }
            
            $get_nonce_value = $_POST[$this->term_slug . '_meta_nonce'];

            // Verify that the nonce is valid.
            if ( ! wp_verify_nonce( $get_nonce_value, $this->term_slug ) ) {
                return $term_id;
            }

            //Case is auto save, not process saved parameter
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return $term_id;
            }
            
            //VERIFY is post submit is taxonomy equals a testimonials
            if( !isset( $_POST['taxonomy'] ) && isset( $_POST['taxonomy'] ) && $_POST['taxonomy'] != $this->term_slug ){
                return $term_id;
            }
            
            //verify user contains permission of edit page or edit post
            if( !current_user_can('edit_posts') ){
                return $term_id;
            }

            //thumnail id
            $related_categories = ( isset( $_POST['__related_categories'] ) && $_POST['__related_categories'] ? $_POST['__related_categories'] : array() );
            update_term_meta( $term_id, '__related_categories', $related_categories );

            //thumnail id
            $common_tag_for_all_users = ( isset( $_POST['__common_tag_for_all_users'] ) && $_POST['__common_tag_for_all_users'] ? true : false );
            update_term_meta( $term_id, '__common_tag_for_all_users', $common_tag_for_all_users );
        
        }
    }

    new aemiRegisterCustomTags();
}

if( !function_exists('aemi_get_tags_by_category') ){

    function aemi_get_tags_by_category( $term_id = false, $return_object = true ){
        $preper_query = 's:' . strlen($term_id) . ':"' . $term_id . '"';

        $tags = get_categories( array(
            'taxonomy' => 'aemi_tags',
            'hide_empty' => false,
            'meta_query' => array(
              array(
                 'key'       => '__related_categories',
                 'value'     => $preper_query,
                 'compare'   => 'LIKE'
              )
            )
        ) );

        if( $return_object == false ){
            $new_tags = array();

            if( $tags ){
                foreach ( $tags as $key => $value) {
                    $new_tags[] = $value->term_id;
                }
            }

            $tags = $new_tags;
        }

        return $tags;
    }
}

if( !function_exists('aemi_get_categories_related_by_tag') ){

    function aemi_get_categories_related_by_tag( $term_id = false, $return_object = false ){
        $related_tags = [];
        $categories = get_categories( array(
            'taxonomy' => 'category',
            'hide_empty' => false,
            'number' => 0,
        ) );

        if( $categories ){
            foreach ( $categories as $category ) {
                $taxonomies = aemi_get_tags_by_category( $category->term_id, false );
                if( $taxonomies && in_array( $term_id, $taxonomies ) ){
                    $related_tags[] = ( $return_object ? $category : $category->term_id );
                }
            }
        }

        return $related_tags;
    }
}

if( !function_exists('aemi_get_tags_in_common') ){

    function aemi_get_tags_in_common(){

        $tags = get_categories( array(
            'taxonomy' => 'aemi_tags',
            'hide_empty' => false,
            'number' => 0,
            'meta_query' => array(
              array(
                 'key'       => '__common_tag_for_all_users',
                 'value'     => true,
                 'compare'   => '='
              )
            )
        ) );

        return $tags;
    }
}

if( !function_exists('aemi_get_tags_by_user_and_category') ){

    function aemi_get_tags_by_user_and_category( $user_id = false, $term_id = false, $return_object = false ){
        $returnData = array();
        $aemi_get_interests_by_user = aemi_get_interests_by_user( $user_id );

        $preper_query = 's:' . strlen($term_id) . ':"' . $term_id . '"';

        $tags = get_categories( array(
            'taxonomy' => 'aemi_tags',
            'hide_empty' => false,
            'number' => 0,
            'meta_query' => array(
              array(
                 'key'       => '__related_categories',
                 'value'     => $preper_query,
                 'compare'   => 'LIKE'
              )
            )
        ) );

        if( $tags && $aemi_get_interests_by_user ){
            foreach ( $tags as $current_tag ) {
                if( in_array( $current_tag->term_id, $aemi_get_interests_by_user ) ){
                    $returnData[] = ( $return_object ? $current_tag : $current_tag->term_id );
                }
            }
        }

        return $returnData;
    }
}


if( !function_exists('aemi_get_interests_by_user') ){
    function aemi_get_interests_by_user( $user_id = false, $not_return_tag_common = false ){
        $data = get_user_meta($user_id, '_current_user_step_insterest', true);
        $data = ( $data ? $data : array() );

        if( $not_return_tag_common == false ){
            $tag_common = aemi_get_tags_in_common();

            if( $tag_common ){
                foreach ( $tag_common as $key => $value ) {
                    $data[] = $value->term_id;
                }
            }
        }

        return $data;
    }
}