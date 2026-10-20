<?php 

if( !class_exists( 'AemiGoogleSpreadSheestImportUserToWPAdmin' ) ){
	
	class AemiGoogleSpreadSheestImportUserToWPAdmin extends AemiGoogleSpreadSheestImportUserToWPBase {

		public function __construct(){
			add_action( 'admin_menu', array( $this, 'submenu_page' ) );
			add_action( 'wp_ajax_aemi_run_manual_process_import_user', array( $this, 'aemi_run_manual_process_import_user' ) );
		}
		
		/**
		 * Registering script data to process and run the import user script
		 */
		public function aemi_run_manual_process_import_user(){

			$returnData = array(
				'success' => false,
				'msg' => __( 'There was an error processing your request, please try again later!', AEMI_TEXT_DOMAIN )
			);

			if( is_user_logged_in() ){
				if( isset( $_POST[$this->nonce_name] ) && $_POST[$this->nonce_name] ){
					if( wp_verify_nonce( $_POST[$this->nonce_name], $this->nonce_action ) ){
						$returnData['success'] = true;
						$returnData['msg'] = __( 'Users are being imported...', AEMI_TEXT_DOMAIN );
						$response = wp_remote_get( home_url() . '/wp-json/' . $this->rest_route__namespace . $this->rest_route__route . $this->token_validation_rest . '?current_user=' . get_current_user_id() );
						
						if (!is_wp_error($response)) {
							$returnData['success'] = true;
						} else {
							// $returnData['success'] = false;
							// $returnData['msg'] = __( 'There was an error processing your request, please try again later!', AEMI_TEXT_DOMAIN ) . '²';
						}
					}
				}
			}

			header('Content-Type: application/json; charset=utf-8');
			echo json_encode( $returnData );
			wp_die(); 
		}

		/**
		 * Register user import menu
		 */
		public function submenu_page(){
			add_submenu_page( 
				'options-general.php', __( 'Import users from Google Sheets', AEMI_TEXT_DOMAIN ), __( 'Import users from Google Sheets', AEMI_TEXT_DOMAIN ), 'manage_options', $this->subpage_url, array( $this, 'submenu_page_content' )
			);
		}

		/**
		 * Returns a url based on the settings page
		 */
		public function get_current_page_screen( $extra = '' ){
			return get_admin_url() . '/options-general.php?page=' . $this->subpage_url . ( $extra ? '&current_tab=' . $extra : '' );
		}

		/**
		 * Validates if the current item is not the page
		 * and returns the class to activate the item
		 * 
		 * @param String $base
		 * @return String;
		 */
		public function is_current_page_screen( $base = '' ){
			$returnData = false;

			if( $base == '' && !isset( $_GET['current_tab'] ) ){
				$returnData = true;
			}

			if( isset( $_GET['current_tab'] ) && $_GET['current_tab'] == $base ){
				$returnData = true;
			}

			return ( $returnData ? 'active' : '' );
		}

		/**
		 * Contents of the settings page
		 */
		public function submenu_page_content(){
			$get_last_import_date = $this->get_last_import_date();
			?>
			<style>
				.container-box-imports{
					padding: 30px;
					border-radius: 10px;
					background: #fff;
					box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.05);
				}

				.container-box-imports__tabs{
				}
				
				.container-box-imports__tabs ul{
					display: flex;
					margin: 0;
				}
				
				.container-box-imports__tabs ul li{
					display: flex;
					margin: 0;
					padding: 0;
				}
				
				.container-box-imports__tabs ul li + li {
					margin-left: 5px;
				}

				.container-box-imports__tabs ul li a{
					display: flex;
					text-decoration: none;
					background: #eee;
					color: #666;
					padding: 15px 20px;
					border-radius: 16px 16px 0 0;
					font-weight: bold;
				}

				.container-box-imports__tabs ul li a.active{
					background: #01739d;
					color: #fff;
				}

				.container-box-imports__section{
					padding: 25px;
					border: 1px solid #eee;
				}
				
				.aemi-msg-toast-gs{
					width: 300px;
					position: fixed;
					right: 20px;
					bottom: 20px;
					color: #fff;
					background: #4caf50;
					padding: 10px;
					text-align: center;
					border-radius: 50px;
				}

				.aemi-msg-toast-gs[data-type="error"]{
					background: #e91e1e;
				}

				button[disabled] .spin-rotate{
					margin-left: 10px;
					display: inline-block;
					width: 10px;
					height: 10px;
					border-radius: 50%;
					border: 2px solid #f39910;
					border-bottom: 2px solid rgba(0,0,0,0);
					animation: animatationSpinGoogleImportUsers 1s infinite linear;
					position: relative;
					top: 2px;
					opacity: 0.5;
				}

				@keyframes animatationSpinGoogleImportUsers {
					0%{
						transform: rotate(0deg);
					}
					100%{
						transform: rotate(360deg);
					}
				}

				.info-importation-log{
					margin-top: 10px;
				}

				.info-importation-log__box{
					background: #fff;
					border: 1px solid #eee;
					padding: 10px;
					font-size: 12px;
				}
				
				.info-importation-log__box.info-importation-log-error{

				}

				.info-importation-log__box.info-importation-log-error button{
					background: none;
					border: none;
					padding: 0;
					cursor: pointer;
					width: 100%;
					text-align: left;
					position: relative;
				}

				.info-importation-log__box.info-importation-log-error button:after{
					content: "";
					display: block;
					width: 5px;
					height: 5px;
					border-left: 2px solid #666;
					border-bottom: 2px solid #666;
					transform: rotate(-45deg);
					position: absolute;
					right: 8px;
					top: 3px;
				}

				.info-importation-log__box.info-importation-log-error ul{
					background: #f7f7f7;
					padding: 12px 10px;
					display: none;
				}
				
				.info-importation-log__box.info-importation-log-error button:focus + ul{
					display: block;
				}
				.info-importation-log__box.info-importation-log-error ul li{}
				.info-importation-log__box.info-importation-log-error ul li strong{
					color: #cb1818;
				}

			</style>
			<div class="wrap">
				<h1><?php echo __( 'Import users from Google Sheets', AEMI_TEXT_DOMAIN ); ?></h1>

				<div class="container-box-imports">
					<div class="container-box-imports__tabs">
						<ul>
							<li>
								<a href="<?= $this->get_current_page_screen(); ?>" title="" class="<?= $this->is_current_page_screen(''); ?>">
									<?php echo __( 'General', AEMI_TEXT_DOMAIN ); ?>
								</a>
							</li>
							<li>
								<a href="<?= $this->get_current_page_screen('log'); ?>" title="" class="<?= $this->is_current_page_screen('log'); ?>">
									<?php echo __( 'Log', AEMI_TEXT_DOMAIN ); ?>
								</a>
							</li>
						</ul>
					</div>
					<div class="container-box-imports__section">

						<?php if( $this->is_current_page_screen('') ): ?>

							<?php if( !$this->check_credentials_google_sheets_exists() ): ?>
								<div style="background: #ff4e4e;color: #fff;padding: 19px;border-radius: 50px;"><?php echo __( 'Google Sheets settings are not set', AEMI_TEXT_DOMAIN ); ?></div>
							<?php else: ?>
								<?php
									$data_is_being_imported = $this->get_data_is_being_imported();	
								?>
								<p style="font-size: 16px; margin-bottom: 25px; display: block;">
									<strong><?php echo __( 'Last import date:', AEMI_TEXT_DOMAIN ); ?></strong> 
									<span style="background: #eee; display: inline-block; padding: 5px 20px; border-radius: 50px; border: 1px solid #ddd;">
									<?php echo ( $get_last_import_date ? date( 'd/m/Y H:i:s', strtotime( $get_last_import_date ) ) : __( 'No user import yet', AEMI_TEXT_DOMAIN ) ); ?>
									</span>
								</p>

								<?php if( $data_is_being_imported ): ?>
									<div style="margin: 30px 0 30px; background: #f39910; display: table;color: #fff; padding: 20px; border-radius: 10px;"><?php
										echo __( 'There is an import currently running, the import started at:', AEMI_TEXT_DOMAIN );
										echo ' <strong>' . date( 'd/m/Y H:i:s', strtotime( $this->get_date_the_user_import_process_started() ) ) . '</strong>';
									?></div>
								<?php endif; ?>

								<button <?php echo ( $data_is_being_imported ? 'disabled=""' : '' ); ?> type="button" class="button button-primary run-import-user-btn">
									<?php echo __( 'Run user import', AEMI_TEXT_DOMAIN ); ?>
									<span class="spin-rotate"></span>
								</button>
							<?php endif; ?>
						<?php endif; ?>

						<?php if( $this->is_current_page_screen('log') ): ?>
							<div class="">
								<p style="font-size: 18px;"><strong><?php echo __( 'User import log:', AEMI_TEXT_DOMAIN ) ?></strong></p>

								<?php echo $this->get_formatted_import_users_log(); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<script>
                window.addEventListener('load', function(){

                    jQuery('.run-import-user-btn').click(function(){
						if( jQuery('.run-import-user-btn:disabled').length <= 0 ){
							jQuery('.run-import-user-btn').prop('disabled', true);

							jQuery.ajax({ 
								url : '<?php echo get_admin_url(); ?>admin-ajax.php', 
								type : 'POST',
								dataType: "json",
								data : {
									action : 'aemi_run_manual_process_import_user',
									<?php echo $this->nonce_name; ?>: '<?php echo wp_create_nonce( $this->nonce_action ); ?>'
								}, 
								success: function( data ){
									var htmlMessage = "<div class='aemi-msg-toast-gs' data-type='" + ( data.success ? 'success' : 'error' ) + "'>" + data.msg + "</div>";

									jQuery('body').append(htmlMessage);

									setTimeout( function(){
										jQuery('.aemi-msg-toast-gs').remove();

                                        if( data.success ){
                                            window.location.reload();
                                        }

									}, 5000 );
								}
							});
						}
                    });
                });
            </script>
			<?php
		}
	}

	new AemiGoogleSpreadSheestImportUserToWPAdmin();
}