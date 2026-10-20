<?php 

if( !class_exists( 'AemiGoogleSpreadSheestImportUserToWPBase' ) ){
	
	class AemiGoogleSpreadSheestImportUserToWPBase {

		public $option_name__last_import_date = '__aemi_google_spread_sheets__last_import_date';
		public $option_name__data_is_being_imported = '__aemi_google_spread_sheets__data_is_being_imported';
		public $option_name__date_the_user_import_process_started = '__aemi_google_spread_sheets__date_the_user_import_process_started';
		public $option_name__import_users_log = '__aemi_google_spread_sheets_import_users_log';
        
		public $subpage_url = 'import_users_from_google_sheets';
		public $nonce_action = 'AemiGoogleSpreadSheestImportUserToWP';
		public $nonce_name = 'nonce_aemi_google_sheet';

		protected $rest_route__namespace = 'aemi-google-sheets-import-user/v1';
		protected $rest_route__route  = '/import-users/';
		protected $rest_route__route_parameters  = '(?P<token_validation_rest>\d+)';
		protected $rest_route__methods  = 'GET';

        public $token_validation_rest = '39440445540613919377709494756441996505061880533989012010720288368482740305096505';

		/**
		 * Return Log
		 */
		protected function get_import_users_log(){
			return get_option( $this->option_name__import_users_log, array());
		}

		/**
		 * Return log formatted
		 */
		protected function get_formatted_import_users_log(){
			$logs = $this->get_import_users_log();
			$logs = ( $logs ? array_reverse( $logs ) : $logs );
			$loaded_users = [];
			$html = '';

			if( $logs ){
				
				$class_type_info = 'color: #2681f3';
				$class_type_warning = 'color: #ff5722';
				$class_type_success = 'color: #1ea124';
				$class_type_error = 'color: #f44336';
				
				foreach ( $logs as $log ) {
					$html .= '<div class="log-container" style="border-bottom: 1px solid #ddd; padding: 5px; display: flex; flex-wrap: wrap;">';
						$html .= '<div style="display: flex; width:100%;">';
							$html .= '<div style="padding: 10px; font-weight: bold;">' . date( 'd/m/Y H:i:s', strtotime( $log['date'] )) . '</div>';
							$html .= '<div style="padding: 10px; width: 70px; ' . ${'class_type_' . $log['type']} . '; font-weight: bold;">[' . $log['type'] . ']</div>';
							$html .= '<div style="padding: 10px; flex: 1;">' . __( $log['msg'], AEMI_TEXT_DOMAIN );
							
								if( isset( $log['others_information']['import_users_report'] ) && $log['others_information']['import_users_report'] ){
									$html .= '<div class="info-importation-log">';
										$html .= '<div class="info-importation-log__box info-importation-log-success">';
											$html .= '<strong>' . __( 'Successfully imported users', AEMI_TEXT_DOMAIN ) . ':</strong> ' . esc_attr( $log['others_information']['import_users_report']['success'] ) . '<br />';
										$html .= '</div>';
										
										$html .= '<div class="info-importation-log__box info-importation-log-error">';
											$html .= '<button type="button"><strong>' . __( 'User import errors', AEMI_TEXT_DOMAIN ) . ':</strong> ' . esc_attr( count( $log['others_information']['import_users_report']['error'] ) ) . '</button>';

											if( $log['others_information']['import_users_report']['error'] ){
												$html .= '<ul>';
													foreach ( $log['others_information']['import_users_report']['error'] as $email_with_error => $msg_of_error ) {
														$html .= '<li><strong>' . esc_attr( $email_with_error ) . ':</strong> ' . esc_attr( $msg_of_error ) . '</li>';
													}
												$html .= '</ul>';
											}

										$html .= '</div>';

									$html .= '</div>'; 
								}

							$html .= '</div>';
							
							$html .= '<div style="padding: 10px;">'; 
								if( isset( $log['user_id'] ) && $log['user_id']  ){
									$current_user = false;

									if( isset( $loaded_users[$log['user_id']] )){
										$current_user = $loaded_users[$log['user_id']];
									} else{
										$get_user = get_user_by( 'id', $log['user_id'] );

										if( $get_user ){
											$loaded_users[$log['user_id']] = $get_user;
											$current_user = $get_user;
										}
									}
									
									if( $current_user ){
										$html .= '<a style="font-weight: bold;text-decoration: none;background: #016e99;color: #fff;padding: 5px 13px;border-radius: 50px;font-size: 12px;" href="' . get_admin_url() . 'wp-admin/user-edit.php?user_id=' . esc_attr( $log['user_id'] ) . '" target="_blank">' . $current_user->data->display_name . '</a>';
									} else {
										$html .= '<span style="font-weight: bold;text-decoration: none;background: #df2bb0;color: #fff;padding: 5px 13px;border-radius: 50px;font-size: 12px;">' . __( 'User ID', AEMI_TEXT_DOMAIN ) . ': ' . esc_attr( $log['user_id'] ) . '</span>';
									}

								} else {
									$html .= '<span style="font-weight: bold;text-decoration: none;background: tomato;color: #fff;padding: 5px 13px;border-radius: 50px;font-size: 12px;">' . __( 'API', AEMI_TEXT_DOMAIN ) . '</span>';
								}
							$html .= '</div>';

						$html .= '</div>';

 					$html .= '</div>';
				}
			}

			return '<pre style="width: 100%;background: #f7f7f7;border: 1px solid #ddd;border-radius: 10px;padding: 20px;box-sizing: border-box; max-height:600px; overflow: auto;">' . $html . '</pre>';
		}

		/**
		 * Saves a new message in the system information log
		 * 
		 * @param String $msg
		 * @param String $type
		 * @param Array $others_information - Custom data informations
		 * 
		 * @return Boolean 
		 */
		protected function set_new_import_users_log( $msg = '', $type = '', $others_information = array(), $current_user = false ){

			$log = $this->get_import_users_log();

			$data = array(
				'msg' => $msg,
				'type' => $type,
				'others_information' => ( is_array( $others_information ) ? $others_information : array() ),
				'date' => date('Y-m-d H:i:s')
			);

			if( $current_user && is_int( $current_user ) ){
				$data['user_id'] = $current_user;
			} else {
				$data['user_id'] = $current_user;
			}

			$log[] = $data;
			if( count( $log ) > 200 ){
					$log = array_slice( $log, -200 );
			}
			update_option( $this->option_name__import_users_log, $log );

			return true;
		}

        /**
         * If any import process is in progress, it will return TRUE and if there is no import data, it will return FALSE.
         */
        protected function get_data_is_being_imported(){
			$option_name__data_is_being_imported = ( get_option( $this->option_name__data_is_being_imported ) ? true : false );

			if( $option_name__data_is_being_imported ){
				$date_process_started = $this->get_date_the_user_import_process_started();
				$date_process_started = ( $date_process_started ? strtotime( $date_process_started ) : false );
				
				if( $date_process_started ){
					$now = time();
					$datediff = $now - $date_process_started;
	
					$days = round($datediff / (60 * 60 * 24));
	
					if( $days >= 1 ){
						$this->set_new_import_users_log( 'For some reason an error occurred while importing the users, the system ended the import attempt.', 'warning', array() );
						
						return false;
					}
				}
			}

            return $option_name__data_is_being_imported;
        }

		/**
		 * Defines in the system which is carrying out the user import process. This prevents duplicate data or imports at the same time.
		 */
        protected function set_data_is_being_imported(){
			update_option( $this->option_name__date_the_user_import_process_started, date( 'Y-m-d H:i:s' ) );
			update_option( $this->option_name__data_is_being_imported, true );
            return true;
        }
		
        protected function unset_data_is_being_imported(){
			update_option( $this->option_name__date_the_user_import_process_started, false );
			update_option( $this->option_name__data_is_being_imported, false );
            return true;
        }

		/**
		 * Returns the date the current import started
		 */
		protected function get_date_the_user_import_process_started(){
			return get_option( $this->option_name__date_the_user_import_process_started, false );
		}

		/**
		 * Return last date finish import users
		 */
		protected function get_last_import_date(){
			return get_option( $this->option_name__last_import_date, false );
		}

		/**
		 * Set last date finish import users
		 * @param Date $specific_date
		 * @return Boolean
		 */
		protected function set_last_import_date( $specific_date = false ){
			return update_option( $this->option_name__last_import_date, ( $specific_date ? $specific_date : date('Y-m-d H:i:s') ) );
		}

		/**
		 * Returns the html of the email that must be sent to the user informing about the accesses
		 * 
		 * @param Int $user_id
		 * @param String $user_password
		 * 
		 * return String
		 */
		protected function get_iugs_body_email_html_new_user_access( $user_id = false, $user_password = '' ){
			$blogName = get_bloginfo('name');
	
			if( !$user_id && !$user_password ){
				return false;
			}
	
			$get_user = get_user_by( 'id', $user_id );
			
			if( !$get_user ){
				return false;
			}
	
			$first_name = get_user_meta( $user_id, 'first_name', true );
			$last_name = get_user_meta( $user_id, 'last_name', true );
	
			$current_user_name = sanitize_text_field( htmlentities( $first_name . ( $first_name ? ' ' : '' ) . $last_name ) );
	
			ob_start();
			?><!DOCTYPE html>
			<html>
			<head>
				<meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<title></title>
			</head>
			<body style="background: #eee; padding: 25px 0;">
		
				<!--[if (gte mso 9)|(IE)]>
				<table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td>
					<![endif]-->
						<table align="center" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 600px; background-color:#fff;">
	
							<tr>
								<td style="padding: 25px 25px 0; text-align: center;">
									<img src="<?php echo get_aemi_url_assets_imgs('logo.png'); ?>" title="<?php echo $blogName; ?>" alt="<?php echo $blogName; ?>" />
	
									<hr style="opacity: 0.2; margin-top: 25px;" />
								</td>
							</tr>
	
							<tr>
								<td style="padding: 15px; text-align: center;">
									
									<p style="font-size: 16px; font-family: arial;">Hola <strong><?php echo $current_user_name; ?></strong>!</p>
	
									<p style="font-size: 16px;font-family: arial;line-height: 27px;color: #666;margin-bottom: 30px;">
										Su registro ha sido aprobado en nuestra plataforma,<br />
										Puede utilizar los siguientes datos para iniciar sesión:
										<br />
										<br />
										<strong>E-mail</strong>: <span style="padding: 10px 20px; background: #0072bc; color: #fff!important; border-radius: 5px;"><a href="mailto:<?php echo esc_attr( $get_user->data->user_email ); ?>" style="color: #fff; text-decoration: none;"><?php echo esc_attr( $get_user->data->user_email ); ?></a></span>
										<br />
										<br />
										<strong>Contraseña</strong>: <span style="padding: 10px 20px; background: #0072bc; color: #fff; border-radius: 5px;"><?php echo htmlentities( $user_password ); ?></span>.
									</p>
	
									<p style="font-size: 16px;font-family: arial;line-height: 27px;color: #666;">
										Para acceder a la página de inicio de sesión de nuestra plataforma.
										<br />
										haga clic en el botón de abajo:
										<br />
										<br />
										<a href="<?php echo home_url(); ?>" target="_blank" title="Ir al sistema" style="display: inline-block;padding: 10px 35px;background: #f68321;color: #fff;border-radius: 50px;text-decoration: none;">Ir al sistema</a>
									</p>
								</td>
							</tr>
	
							<tr>
								<td style="padding: 3px 0;text-align: center;background: #0072bc;">
								</td>
							</tr>
	
	
						</table>
					<!--[if (gte mso 9)|(IE)]>
					</td>
				</tr>
			</table>
			<![endif]-->
	
			</body>
			</html>
			<?php
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}

		/**
		 * Generate random string
		 * @author Stephen Watkins <https://stackoverflow.com/questions/4356289/php-random-string-generator>
		 * 
		 * @param Int $length
		 * @return String
		 */
		protected function generateRandomString($length = 10, $use_special_char = false) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

			if( $use_special_char ){
				$characters .= '#$!_-.$&#$!_-.$&'; 
			}

			$charactersLength = strlen($characters);
			$randomString = '';
		
			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
		
			return $randomString;
		}

		/**
		 * Return path credencials google sheets
		 */
		protected function get_path_credentials_google_sheets(){
			if( file_exists(ABSPATH . 'GoogleSheets/credentials.json') ){
				return ABSPATH . 'GoogleSheets/credentials.json';
			}

			return ABSPATH . '../GoogleSheets/credentials.json';
		}

		/**
		 * Check file exists
		 */
		protected function check_credentials_google_sheets_exists(){
			if( file_exists( $this->get_path_credentials_google_sheets() ) ){
				return true;
			}

			return false;
		}

	
		/**
		 * Returns data from a specific sheet of the worksheet
		 * 
		 * @param String $range - Tab name in worksheet
		 * 
		 * @return Array
		 */
		function get_google_sheets_range_values( $range = false ){
			if( $range ){

				if( defined( 'GOOGLE_SHEETS_APPLICATIONNAME' ) && defined( 'GOOGLE_SHEETS_SPREADSHEETID' ) ){
					// configure the Google Client
					$client = new \Google_Client();
					$client->setApplicationName( GOOGLE_SHEETS_APPLICATIONNAME );
					$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
					$client->setAccessType('offline');
					// credentials.json is the key file we downloaded while setting up our Google Sheets API
					$path = $this->get_path_credentials_google_sheets();
					$client->setAuthConfig($path);
					
					$service = new \Google_Service_Sheets($client);
					$spreadsheetId = GOOGLE_SHEETS_SPREADSHEETID;
					$response = $service->spreadsheets_values->get($spreadsheetId, $range);
					return $response;
				}
			}
			return false;
		}

		/**
		 * Returned users registered in the GoogleSheet
		 */
		protected function get_google_sheets_users(){
			
			$medico = $this->get_google_sheets_range_values( 'Médico' );
			$noVerificado = $this->get_google_sheets_range_values( 'No verificado' );
			$farmaceutico = $this->get_google_sheets_range_values( 'Farmacéutico' );
			$data = array();

			if( $medico ){
				$data['medico'] = $medico->getValues();
			}

			if( $farmaceutico ){
				$data['farmaceutico'] = $farmaceutico->getValues();
			}

			if( $noVerificado ){
				$data['noVerificado'] = $noVerificado->getValues();
			}

			//$data = $this->get_google_sheets_users_temporary_data_to_test();
			return $data;
		}

		protected function prepare_data_to_import_user(
			$default_users_in_sistem = array(),
			$nombre_y_apellidos = '', 
			$nombre = '', 
			$primer_apellido = '', 
			$segundo_apellido = '', 
			$email = false, 
			$telefono = '', 
			$profesion = '', 
			$especialidad = '', 
			$numero_de_colegiado = '', 
			$ejerciente = '', 
			$verificado = '', 
			$estado = '', 
			$colegio = '', 
			$powerappsid = '',
			$type = ''
		){
			if( !$email ){
				return false;
			}

			$email = strip_tags( $email );
			$email = trim( $email );

			if( isset( $default_users_in_sistem[$email] ) ){
				return false;
			}

			/**
			 * Aqui vou preparar os dados do nome do usuário
			 */
			$user_nice_name = $nombre_y_apellidos;
			$user_first_name = '';
			$user_last_name = '';

			//Vou verificar se um nice name eu vou tentar procurar por $nombre
			if( !$user_nice_name && $nombre){
				$user_nice_name = $nombre;

				//se existir vou definir o first e o last name com base no nome
				if( !$user_first_name && !$primer_apellido ){
					$temp_name = explode( ' ', $nombre );

					if( isset( $temp_name[0] ) && $temp_name[0] ){
						$user_first_name = $temp_name[0];
					}

					if( isset( $temp_name[1] ) && $temp_name[1] ){
						unset( $temp_name[0] );
						$user_last_name = implode( ' ', $temp_name );
					}
				}
			}

			//se não existir um nome válido vou tentar ver se existe a variavel $primer_apellido
			//para definir como nome do usuário
			if( !$user_nice_name && $primer_apellido){
				$user_nice_name = $primer_apellido;
				$user_first_name = $primer_apellido;

				if( $segundo_apellido ){
					$user_nice_name .= ' ' . $segundo_apellido;
					$user_last_name = $segundo_apellido;
				}
			}
			

			//caso não exista um first e um last name até esse momento vou gerar
			//esses dados baseado na variavel $user_nice_name
			if( !$user_first_name && $user_nice_name ){
				$temp_name = explode( ' ', $user_nice_name );

				if( isset( $temp_name[0] ) && $temp_name[0] ){
					$user_first_name = $temp_name[0];
				}

				if( isset( $temp_name[1] ) && $temp_name[1] ){
					unset( $temp_name[0] );
					$user_last_name = implode( ' ', $temp_name );
				}
			}

			$meta_data = array(
				// 'nombre_y_apellidos' => $nombre_y_apellidos,
				// 'nombre' => $nombre,
				// 'primer_apellido' => $primer_apellido,
				// 'segundo_apellido' => $segundo_apellido,
				'billing_first_name'								=> sanitize_text_field( $user_first_name ),
				'billing_last_name' 								=> sanitize_text_field( $user_last_name ),
				'billing_phone' 									=> sanitize_text_field( $telefono ),
				'__aemi__imported_profesion' 						=> sanitize_text_field( $profesion ),
				'__aemi__imported_especialidad' 					=> sanitize_text_field( $especialidad ),
				'__aemi__imported_numero_de_colegiado' 				=> sanitize_text_field( $numero_de_colegiado ),
				'__aemi__imported_ejerciente' 						=> sanitize_text_field( $ejerciente ),
				'__aemi__imported_verificado' 						=> sanitize_text_field( $verificado ),
				'__aemi__imported_estado' 							=> sanitize_text_field( $estado ),
				'__aemi__imported_colegio' 							=> sanitize_text_field( $colegio ),
				//'__aemi__imported___powerappsid__' 					=> sanitize_text_field( $powerappsid ),
				'__aemi__imported_user_type_on_import' 				=> $type,
				'__aemi__imported_user_imported_from_spreadsheets' 	=> true
			);

			$user_login = 'ae_g' . date('YmdH') . $this->generateRandomString( 15 );
			$user_rand_password = wp_generate_password( 20, true, true);

			$wp_import_user_data = array(
				'user_pass'				=> $user_rand_password,
				'user_login' 			=> $user_login,
				'nickname'				=> sanitize_text_field( $user_nice_name ),
				'user_nicename' 		=> sanitize_text_field( $user_nice_name ),
				'user_email' 			=> $email,
				'display_name' 			=> sanitize_text_field( $user_nice_name ),
				'first_name' 			=> sanitize_text_field( $user_first_name ),
				'last_name' 			=> sanitize_text_field( $user_last_name ),
				'meta_data' 			=> $meta_data
			);

			return $wp_import_user_data;
		}

		/**
		 * Returns formatted and ready data according to valid data for import
		 * 
		 * @param Boolean $returnUsersDataNotExistingOnPlatform - If activated, it only returns data from users who have not yet registered on the platform
		 * 
		 * @return Boolean
		 */
		protected function get_google_sheets_users_formatted( $returnUsersDataNotExistingOnPlatform = true ){
			$data = $this->get_google_sheets_users();
			$dataReturn = array();

			$get_all_users_emails = array();
			if( $returnUsersDataNotExistingOnPlatform ){
				$wp_users = get_users( array('number' => -1, 'fields' => array( 'user_email', 'id' ) ) );

				foreach( $wp_users as $user ){
					$get_all_users_emails[$user->user_email] = $user->id;
				}
			}

			if( $data ){

				if( isset( $data['medico'] ) && $data['medico'] ){
					$loop = 0;
					foreach ( $data['medico'] as $value ) {
						if( $loop !== 0 ){
							$dataPrepareUser = $this->prepare_data_to_import_user(
								$get_all_users_emails,
								$value[0], // Nombre y Apellidos
								'', 
								'', 
								'', 
								$value[1], //Email
								$value[2], //Teléfono
								'', //profesion
								$value[4], //Especialidad 
								$value[5], //Número de colegiado
								'', //ejerciente
								'', //verificado
								$value[3], //Estado, 
								'',//colegio
								$value[6], //__PowerAppsId__
								'medico'
							);
	
							if( $dataPrepareUser ){
								$dataReturn[] = $dataPrepareUser;
							}
						} else {
							$loop++;
						}
					}
				}

				if( isset( $data['farmaceutico'] ) && $data['farmaceutico'] ){
					$loop = 0;
					foreach ( $data['farmaceutico'] as $value ) {
						if( $loop !== 0 ){
							$dataPrepareUser = $this->prepare_data_to_import_user(
								$get_all_users_emails,
								$value[0], //Nombre y Apellidos
								'', //nombre
								'', //primer_apellido
								'', //segundo_apellido
								$value[1], //Email 
								$value[2], //Teléfono
								$value[3], //Profesión
								$value[4], //Especialidad
								$value[6], //Número de colegiado
								$value[7], // Ejerciente
								'', //verificado 
								'', //estado 
								$value[5], //Colegio
								$value[8], //__PowerAppsId__
								'farmaceutico'
							);

							if( $dataPrepareUser ){
								$dataReturn[] = $dataPrepareUser;
							}
						} else {
							$loop++;
						}
					}
				}
			}

			return $dataReturn;
		}

		protected function get_google_sheets_users_temporary_data_to_test(){

			$data = [
				'medico' => [
            		0 => [
						0 => 'Nombre y Apellidos',
						1 => 'Email',
						2 => 'Teléfono',
						3 => 'Estado',
						4 => 'Especialidad',
						5 => 'Número de colegiado',
						6 => '__PowerAppsId__'
					]
				],
				'farmaceutico' => [
            		0 => [
						0 => 'Nombre y Apellidos',
						1 => 'Email',
						2 => 'Teléfono',
						3 => 'Profesión',
						4 => 'Especialidad',
						5 => 'Colegio',
						6 => 'Número de colegiado',
						7 => 'Ejerciente',
						8 => '__PowerAppsId__'
					]
				],
				'noVerificado' => [
            		0 => [
						0 => 'Nombre y Apellidos',
						1 => 'Primer apellido',
						2 => 'Segundo apellido',
						3 => 'Email',
						4 => 'Teléfono',
						5 => 'Profesión',
						6 => 'Especialidad',
						7 => 'Número de colegiado',
						8 => 'Ejerciente',
						9 => 'Verificado',
						10 => '__PowerAppsId__',
					]
				]
			];

			for ( $i=0; $i < 1; $i++ ) { 
				
				$powerAppsId = 'M' . ( $i * $i ) . 'ih' . $i. 'd9VcuMOLxt' . $i. 'TPyn';
				
				$data['medico'][] = [
					0 => 'Nome de exemplo ' . $i,
					1 => 'kelvin+aemiteste_medico_o-' . $i . '@envixo.com',
					2 => '+000 00 000000-0000',
					3 => 'Médico lorem ipsum dolor ' . $i,
					4 => 'Médico General',
					5 => '00000000000000000000000'.$i,
					6 => $powerAppsId
				];

				$data['farmaceutico'][] = [
					0 => 'Nome de exemplo ' . $i,
					1 => 'kelvin+aemiteste_farmaceutico_' . $i . '@envixo.com',
					2 => '+000 00 000000-0000',
					3 => 'Farmacéutico ' . $i,
					4 => 'Medicine ' . $i,
					5 => 'Exemplo ' . $i,
					6 => ( $i * 1256 ),
					7 => 'Exemplode de dados',
					8 => $powerAppsId
				];

				$data['noVerificado'][] = [
					0 => 'Nombre y Apellidos ' . $i,
					1 => 'Primer apellido ' . $i,
					2 => 'Segundo apellido ' . $i,
					3 => 'kelvin+aemiteste_noverificado_' . $i . '@envixo.com',
					4 => '+000 00 000000-0000',
					5 => 'Farmacéutico ' . $i,
					6 => 'Medicine ' . $i,
					7 => ( $i * 1256 ),
					8 => 'No disponible',
					9 => 'No',
					10 => $powerAppsId,
				];
			}

			return $data;
		}

	}

	new AemiGoogleSpreadSheestImportUserToWPBase();
}