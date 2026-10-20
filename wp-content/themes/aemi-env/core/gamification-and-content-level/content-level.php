<?php

class RegisterAemiTaxonomyLevelUsers extends AemiLevelUsers {


	function __construct(){


		add_action( 'init', array( $this, 'registerLevelTaxonomy' ) );
		add_filter( 'taxonomy_parent_dropdown_args', array( $this, 'disableChildTaxonomyInDropdownAdminEditTaxonomy' ), 99, 2 );
		/**
		 * A temporary code to enable the editing of taxonomies, 
		 * as the option to register new categories was temporarily disabled and 
		 * therefore the edit button disappears
		 */
		add_filter( $this->taxonomy_name . '_row_actions', array( $this, 'addBtnEditInRegisterLevelTaxonomy' ), 2, 999 );
		
		//others settings
		add_filter( 'admin_head', array( $this, 'addCustomStyleInAdminHead' ), 99, 2 );
		add_action( 'admin_menu', array( $this, 'registerSubMenuPages' ) );
		add_action( $this->taxonomy_name . '_edit_form_fields', array( $this, 'customMetaBox' ) );
		add_action( 'created_' . $this->taxonomy_name, array( $this, 'saveMetaData' ) );
		add_action( 'edited_' . $this->taxonomy_name, array( $this, 'saveMetaData' ) );

		add_filter( 'manage_edit-' . $this->taxonomy_name . '_columns', array( $this, 'customColumnTh'), 50 );
		add_filter( 'manage_' . $this->taxonomy_name . '_custom_column', array( $this, 'customColumnTdValue'), 50, 3) ;

		//Create relationship in posts types with taxonomy level
		add_action( 'add_meta_boxes', array( $this, 'addRelationshipInPostsTypesWithTaxonomyLevel' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );
	}

 
	/**
	 * ======================================================================================
	 * 							CODE METABOXS
	 * ======================================================================================
	 */
	/**
	 * Register metaboxs
	 */
	public function addRelationshipInPostsTypesWithTaxonomyLevel(){
		add_meta_box(
			$this->taxonomy_name . '_posts_relations',
			__( 'Content levels', AEMI_TEXT_DOMAIN ),
			array( $this, 'renderMetaBoxRelatedTaxonomyLevels' ),
			$this->related_posts_types,
			'side',
			'high'
		);
	}

	/**
	 * Save data os post metabox
	 * 
	 * @param WP_Post $postID Return post object
	 */
	public function save_meta_box( $postID ){
	  
	  // Check nonce exist
	  if ( ! isset( $_POST[$this->getPostMetaName__ScoreClassificationEnabled() . '_meta_nonce'] ) ) {
		return $postID;
	  }
	  
	  $get_nonce_value = $_POST[$this->getPostMetaName__ScoreClassificationEnabled() . '_meta_nonce'];
  
	  // Verify that the nonce is valid.
	  if ( ! wp_verify_nonce( $get_nonce_value, $this->getPostMetaName__ScoreClassificationEnabled() ) ) {
		return $postID;
	  }
  
	  //Case is auto save, not process saved parameter
	  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $postID;
	  }
  
	  //VERIFY is post submit is post_type equals a testimonials
	  if( !isset( $_POST['post_type'] ) && isset( $_POST['post_type'] ) && $_POST['post_type'] != 'post' ){
		return $postID;
	  }
  
	  //verify user contains permission of edit page or edit post
	  if( !current_user_can( 'edit_page', $postID ) && !current_user_can( 'edit_post', $postID )  ){
		return $postID;
	  }
	  
	  //Get data to save
	  $aemi_level = ( isset( $_POST[$this->getPostMetaName__ScoreClassificationEnabled()] ) && $_POST[$this->getPostMetaName__ScoreClassificationEnabled()] ? $_POST[$this->getPostMetaName__ScoreClassificationEnabled()] : array() );
	  update_post_meta( $postID, $this->getPostMetaName__ScoreClassificationEnabled(), $aemi_level );

	  $aemi_level_classification = ( isset( $_POST[$this->getPostMetaName__ScoreClassification()] ) && $_POST[$this->getPostMetaName__ScoreClassification()] ? $_POST[$this->getPostMetaName__ScoreClassification()] : array() );

	  if( $aemi_level_classification ){
		foreach ( $aemi_level_classification as $key => $value) {
			if( !is_numeric( $value ) ){
				$aemi_level_classification[$key] = '';
			}
		}
	  }

	  update_post_meta( $postID, $this->getPostMetaName__ScoreClassification(), $aemi_level_classification );
	  
	  //Se taxonomy level
	  wp_set_post_terms( $postID, $aemi_level, $this->taxonomy_name, false );
	}

	/**
	 * Render metabox
	 */
	public function renderMetaBoxRelatedTaxonomyLevels( $post ){
		$levels = $this->get_levels();

		$score_classification = array();
		$aemi_enabled = array();

		if( $post ){
			$score_classification = get_post_meta( $post->ID, $this->getPostMetaName__ScoreClassification(), true );
			$aemi_enabled = get_post_meta( $post->ID, $this->getPostMetaName__ScoreClassificationEnabled(), true );
		}

		if( $levels ){
			
			wp_nonce_field( $this->getPostMetaName__ScoreClassificationEnabled(), $this->getPostMetaName__ScoreClassificationEnabled() . '_meta_nonce' );
			?>
			<div class="aemi-gamification-relation-taxonomy-levels">
				<p><?php echo __( 'Add the scores the user will receive when viewing the content and the content level:', AEMI_TEXT_DOMAIN ); ?></p>
				<ul>
					<?php foreach( $levels as $level ): ?>
						<li>
							<input <?php echo ( $aemi_enabled && isset( $aemi_enabled[$level->term_id] ) ? ' checked="true"' : '' ) ?> id="aemi-label-relation-<?php echo $level->term_id; ?>" type="checkbox" name="<?php echo $this->getPostMetaName__ScoreClassificationEnabled(); ?>[<?php echo $level->term_id; ?>]" value="<?php echo $level->term_id; ?>" />
							<label for="aemi-label-relation-<?php echo $level->term_id; ?>" class="aemi-custom-level-title"><?php echo $level->name; ?>:</label>
							<ul>
								<?php foreach( $level->classification as $subclass ): ?>
									<li>
										<label class="aemi-custom-checkbox">
											<input <?php echo ( $aemi_enabled && isset( $aemi_enabled[$subclass->term_id] ) ? ' checked="true"' : '' ) ?> type="checkbox" name="<?php echo $this->getPostMetaName__ScoreClassificationEnabled(); ?>[<?php echo $subclass->term_id; ?>]" value="<?php echo $subclass->term_id; ?>" />
											<span></span>
											<p><?php echo $subclass->name; ?>:</p>
										</label>
										<input type="number" name="<?php echo $this->getPostMetaName__ScoreClassification(); ?>[<?php echo $subclass->term_id; ?>]" value="<?php echo ( $score_classification && isset( $score_classification[$subclass->term_id] ) ? $score_classification[$subclass->term_id] : '' ); ?>" placeholder="" />

									</li>
								<?php endforeach; ?>
							</ul>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<style>
				.aemi-gamification-relation-taxonomy-levels{
					padding: 20px 0 0;
				}
				.aemi-gamification-relation-taxonomy-levels > p{
					margin-bottom: 25px;
				}
				.aemi-gamification-relation-taxonomy-levels > ul{
					padding: 0px 0 0;
					margin: 0;
				}
				
				.aemi-gamification-relation-taxonomy-levels > ul > li{
					padding: 0;
					position: relative;
					margin: 0;
				}

				.aemi-gamification-relation-taxonomy-levels > ul > li > input{
					display: none;
				}
				
				.aemi-gamification-relation-taxonomy-levels > ul > li ul{
					display: none;
					padding: 11px;
					background: #f7f7f7;
					border: 1px solid #eee;
					margin-top: 18px;
				}

				.aemi-gamification-relation-taxonomy-levels > ul > li input:checked ~ ul{
					display: block;
				}

				.aemi-gamification-relation-taxonomy-levels > ul > li input ~ label{
					position: relative;
				}

				.aemi-gamification-relation-taxonomy-levels > ul > li input ~ label:before{
					content: "";
					display: inline-block;
					border-radius: 50px;
					border: 2px solid #ddd;
					width: 45px;
					height: 25px;
					background: #fff;
					margin-right: 10px;
					position: relative;
				}

				.aemi-gamification-relation-taxonomy-levels > ul > li input ~ label:after{
					content: "";
					display: block;
					width: 23px;
					height: 23px;
					border-radius: 50%;
					background: #eee;
					position: absolute;
					top: 3px;
					left: 4px;
					transition: all 300ms;
				}

				.aemi-gamification-relation-taxonomy-levels > ul > li input:checked ~ label:before{
					border-color: #f68321;
					transition: all 300ms;
				}
				
				.aemi-gamification-relation-taxonomy-levels > ul > li input:checked ~ label:after{
					left: 21px;
					background-color: #f68321;
				}

				.aemi-gamification-relation-taxonomy-levels > ul > li + li{
					border-top: 1px solid #eee;
					padding-top: 30px;
					margin-top: 30px;
				}

				.aemi-gamification-relation-taxonomy-levels > ul > li > p{
					font-weight: bold;
				}

				.aemi-gamification-relation-taxonomy-levels > ul > li ul li{
					display: flex;
					align-items: center;
					justify-content: space-between;
				}

				.aemi-gamification-relation-taxonomy-levels > ul > li ul li p{
					margin: 0;
					font-weight: bold;
				}

				.aemi-gamification-relation-taxonomy-levels > ul > li ul li label {
					display: flex;
					align-items: center;
					justify-content: space-between;
				}

				.aemi-gamification-relation-taxonomy-levels > ul > li ul li label input{
					display: none;
				}

				.aemi-custom-checkbox{
					
				}

				.aemi-custom-checkbox input{
					display: none;
				}

				.aemi-custom-checkbox input + span{
					display: inline-block;
					border-radius: 50px;
					border: 2px solid #ddd;
					width: 45px;
					height: 25px;
					background: #fff;
					margin-right: 10px;
					position: relative;
				}

				.aemi-custom-checkbox input + span:before{
					content: "";
					display: block;
					width: 23px;
					height: 23px;
					border-radius: 50%;
					background: #eee;
					position: absolute;
					top: 1px;
					left: 2px;
					transition: all 300ms;
				}

				.aemi-custom-checkbox input:checked + span{
					border-color: #f68321;
					transition: all 300ms;
				}
				
				.aemi-custom-checkbox input:checked + span:before{
					left: calc( 100% - 25px );
					background-color: #f68321;
				}

				.aemi-gamification-relation-taxonomy-levels > ul > li ul li > input{
					width: 100px;
				}

				.aemi-custom-level-title{
					font-size: 18px;
					font-weight: bold;
					align-items: center;
					display: flex;
				}

			</style>
			<?php
		}
	}

	/**
	 * ======================================================================================
	 * 							CODE OF TAXONOMY
	 * ======================================================================================
	 */

	/**
	 * Add custom css in edit/add category page
	 */
	public function addCustomStyleInAdminHead(){
		global $pagenow, $taxonomy;

		if( $pagenow == 'edit-tags.php' && isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] == $this->taxonomy_name && $taxonomy == $this->taxonomy_name ){
			?>
			<style>
				#addtag .term-parent-wrap #parent[name="parent"] option:not(.level-0){
					display: none;
				}
				#addtag .term-parent-wrap #parent[name="parent"] option:nth-child(1){
					display: block;
				}
			</style>
			<?php
		} else if( $pagenow == 'term.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'post' && $taxonomy == $this->taxonomy_name ){
			global $tag;
			?>
			<style>
				#addtag .term-parent-wrap #parent[name="parent"] option:not(.level-0){
					display: none;
				}
				#addtag .term-parent-wrap #parent[name="parent"] option:nth-child(1){
					display: block;
				}

				.form-field.term-description-wrap *[for="description"]{
					display: none;
				}

				.form-field.term-description-wrap th:before{
					content: "<?php echo __( 'Competencies / Skills', AEMI_TEXT_DOMAIN ); ?>:";
					display: block;
				}
				<?php if( $tag->parent == 0 ): ?>
					.form-field.term-parent-wrap{
						display: none;
					}
				<?php endif; ?>
			</style>
			<?php
		}
	}

	/**
	 * Disable viewing of subcategories in the relative's drop-down list in the record
	 */
	public function disableChildTaxonomyInDropdownAdminEditTaxonomy( $dropdown_args, $taxonomy ){

		if( $this->taxonomy_name == $taxonomy ){
			$dropdown_args['depth'] = 1;
		}

		return $dropdown_args;
	}

	/**
	 * Adds the option to register levels in the WordPress users submenu
	 */
	public function registerSubMenuPages(){
		add_submenu_page('users.php', __( 'Content level', AEMI_TEXT_DOMAIN ), __( 'Content level', AEMI_TEXT_DOMAIN ), 'administrator', 'edit-tags.php?taxonomy=' . $this->taxonomy_name, false );
	}

	/**
	 * 	Register taxonomy
	 */
	public function registerLevelTaxonomy() {
		$labels = array(
			'name'                       => __( 'Content levels', AEMI_TEXT_DOMAIN ),
			'singular_name'              => __( 'Content level', AEMI_TEXT_DOMAIN ),
			'menu_name'                  => __( 'Content level', AEMI_TEXT_DOMAIN ),
			'all_items'                  => __( 'All content levels', AEMI_TEXT_DOMAIN ),
			'parent_item'                => __( 'Parent level', AEMI_TEXT_DOMAIN ),
			'parent_item_colon'          => __( 'Parent level:', AEMI_TEXT_DOMAIN ),
			'new_item_name'              => __( 'New content level Name', AEMI_TEXT_DOMAIN ),
			'add_new_item'               => __( 'Add New content level', AEMI_TEXT_DOMAIN ),
			'edit_item'                  => __( 'Edit content level', AEMI_TEXT_DOMAIN ),
			'update_item'                => __( 'Update content level', AEMI_TEXT_DOMAIN ),
			'view_item'                  => __( 'View content level', AEMI_TEXT_DOMAIN ),
			'separate_items_with_commas' => __( 'Separate content levels with commas', AEMI_TEXT_DOMAIN ),
			'add_or_remove_items'        => __( 'Add or remove content level', AEMI_TEXT_DOMAIN ),
			'choose_from_most_used'      => __( 'Choose from the most used', AEMI_TEXT_DOMAIN ),
			'popular_items'              => __( 'Popular content levels', AEMI_TEXT_DOMAIN ),
			'search_items'               => __( 'Search content levels', AEMI_TEXT_DOMAIN ),
			'not_found'                  => __( 'Not Found', AEMI_TEXT_DOMAIN ),
			'no_terms'                   => __( 'No content levels', AEMI_TEXT_DOMAIN ),
			'items_list'                 => __( 'Content levels list', AEMI_TEXT_DOMAIN ),
			'items_list_navigation'      => __( 'Content levels list navigation', AEMI_TEXT_DOMAIN ),
		);

		$capabilities = array(
			//'edit_terms'    => '',
			'delete_terms'  => '',
			//'assign_terms'  => ''
		);

		//A temporary code to disable the registration of new taxonomies
		if( is_admin() && isset( $_SERVER['REQUEST_URI'] ) && $_SERVER['REQUEST_URI'] ){
			$temp = explode( '/wp-admin/', $_SERVER['REQUEST_URI'] );

			if( isset($temp[1]) && $temp[1] == 'edit-tags.php?taxonomy=' . $this->taxonomy_name ){
				$capabilities['edit_terms'] = '';
			}
		}

		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => false,
			'show_in_rest'               => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => false,
			'capabilities'      		 => $capabilities
		);

		register_taxonomy( $this->taxonomy_name, array(), $args );
	}

	/**
	 * A temporary code to enable the editing of taxonomies, 
	 * as the option to register new categories was temporarily disabled and 
	 * therefore the edit button disappears
	 */
	public function addBtnEditInRegisterLevelTaxonomy( $actions, $tag ){
		if( $tag->taxonomy == $this->taxonomy_name ){
			$taxonomy = $tag->taxonomy;
			$tax      = get_taxonomy( $taxonomy );
			$uri      = wp_doing_ajax() ? wp_get_referer() : $_SERVER['REQUEST_URI'];

			$edit_link = add_query_arg(
				'wp_http_referer',
				urlencode( wp_unslash( $uri ) ),
				get_admin_url() . 'term.php?taxonomy=' . $taxonomy . '&tag_ID=' . $tag->term_id . '&post_type=post'
			);

			$actions['edit'] = sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				esc_url( $edit_link ),
				/* translators: %s: Taxonomy term name. */
				esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $tag->name ) ),
				__( 'Edit' )
			);
		}

		return $actions;
	}

	/**
	 * Insert custom column
	 */
	public function customColumnTh( $columns ){
		unset( $columns['description'] );
		unset( $columns['posts'] );
		$columns[$this->tax_meta_name__start_level_order] = __('Level ordering (Priority)', AEMI_TEXT_DOMAIN);
		$columns[$this->tax_meta_name__score_limit] = __('Score limit', AEMI_TEXT_DOMAIN);
		return $columns;
	}

	/**
	 * Insert custom column
	 */
	public function customColumnTdValue( $value, $column_name, $term_id ){

		switch ( $column_name ) {
			case $this->tax_meta_name__score_limit:
				$_score_limit = get_term_meta( $term_id, $this->tax_meta_name__score_limit, true );
				$value = ( $_score_limit ? $_score_limit  : '' );
				break;

			case $this->tax_meta_name__start_level_order:
				$_start_level_at = get_term_meta( $term_id, $this->tax_meta_name__start_level_order, true );
				$value = ( $_start_level_at ? $_start_level_at  : '' );
				break;
		}
		
		return $value;
	}

	/**
	 * Register new fields 
	 */
	public function customMetaBox( $tax ){
		$_key_themes = get_term_meta( $tax->term_id, '_key_themes', true );
		$_start_level_at = get_term_meta( $tax->term_id, $this->tax_meta_name__start_level_order, true );
		$_score_limit = get_term_meta( $tax->term_id, $this->tax_meta_name__score_limit, true );

		//fields
		wp_nonce_field( $this->taxonomy_name, $this->taxonomy_name . '_meta_nonce' );
		?>
		<tr class="form-field">
			<th>
				<label for="_content_editor"><?php echo __('Key themes', AEMI_TEXT_DOMAIN); ?>:</label>
			</th>
			<td>
				<textarea name="_key_themes" id="key_themes" rows="5" cols="50" class="large-text"><?php echo $_key_themes; ?></textarea>
			</td>
		</tr>
		<?php if( $tax->parent > 0 ): ?>
		<tr class="form-field">
			<th>
				<label for="_content_editor"><?php echo __('Score limit', AEMI_TEXT_DOMAIN); ?>:</label>
			</th>
			<td>
				<input type="number" name="<?php echo $this->tax_meta_name__score_limit; ?>" id="score_limit" placeholder="100" value="<?php echo esc_attr( $_score_limit ); ?>" />
				
				<p class="description"><?php echo __('This is the maximum limit a user can reach when reading content related to this subclass.', AEMI_TEXT_DOMAIN); ?></p>
			</td>
		</tr>
		<?php else : ?>
		<tr class="form-field">
			<th>
				<label for="_content_editor"><?php echo __('Level ordering (Priority)', AEMI_TEXT_DOMAIN); ?>:</label>
			</th>
			<td>
				<input type="number" name="<?php echo $this->tax_meta_name__start_level_order; ?>" id="start_level_at" placeholder="2" value="<?php echo esc_attr( $_start_level_at ); ?>" />
				<p class="description"><?php echo __('This parameter is used to identify the order of the levels.', AEMI_TEXT_DOMAIN); ?></p>
			</td>
		</tr>
		<?php
		endif;
	}

	/**
	 * Save custom meta data term
	 * 
	 * @param INT $term_id Return current term id
	 */
	public function saveMetaData( $term_id ){
		
		// Check nonce exist
		if ( ! isset( $_POST[$this->taxonomy_name . '_meta_nonce'] ) ) {
			return $term_id;
		}
		
		$get_nonce_value = $_POST[$this->taxonomy_name . '_meta_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $get_nonce_value, $this->taxonomy_name ) ) {
			return $term_id;
		}

		//Case is auto save, not process saved parameter
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $term_id;
		}
		
		//VERIFY is post submit is taxonomy equals a testimonials
		if( !isset( $_POST['taxonomy'] ) && isset( $_POST['taxonomy'] ) && $_POST['taxonomy'] != $this->taxonomy_name ){
			return $term_id;
		}
		
		//verify user contains permission of edit page or edit post
		if( !current_user_can('edit_posts') ){
			return $term_id;
		}

		$current_Term = get_term( $term_id );

		if( $current_Term->parent > 0 ){
			$_score_limit = ( isset( $_POST[$this->tax_meta_name__score_limit] ) && $_POST[$this->tax_meta_name__score_limit] && is_numeric( $_POST[$this->tax_meta_name__score_limit] ) ? $_POST[$this->tax_meta_name__score_limit] : 0 );
			update_term_meta( $term_id, $this->tax_meta_name__score_limit, $_score_limit );
		} else {
			$_start_level_at = ( isset( $_POST[$this->tax_meta_name__start_level_order] ) && $_POST[$this->tax_meta_name__start_level_order] && is_numeric( $_POST[$this->tax_meta_name__start_level_order] ) ? $_POST[$this->tax_meta_name__start_level_order] : 0 );
			update_term_meta( $term_id, $this->tax_meta_name__start_level_order, $_start_level_at );
		}

		$_key_themes = ( isset( $_POST['_key_themes'] ) && $_POST['_key_themes']  ? $_POST['_key_themes'] : '' );
		update_term_meta( $term_id, '_key_themes', $_key_themes );



	}
}

new RegisterAemiTaxonomyLevelUsers();