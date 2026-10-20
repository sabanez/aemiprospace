<?php 
if( !class_exists( 'AemiGoogleSpreadSheestImportUserToWPUserEditCustomFields' ) ){
	
	class AemiGoogleSpreadSheestImportUserToWPUserEditCustomFields  {
        static $aemi_nonce_name = 'HaBNDMstCibLqjaP';
        static $aemi_nonce_action = 'tPjPagjjOHvQeeUF';

        public function __construct(){
            add_action( 'personal_options', array( $this, 'custom_user_option' ), 999, 1 );
            add_action( 'show_user_profile', array( $this, 'custom_user_option' ), 999, 1 );
            add_action( 'personal_options_update', array( $this, 'save_custom_user_option') );
            add_action( 'edit_user_profile_update', array( $this, 'save_custom_user_option') );
        }

        /**
         * @param WP_User  $profileuser WP_User object for the currently listed user.
         */
        public function custom_user_option( $profileuser ){
            $aemi__imported_profesion = get_user_meta( $profileuser->ID, '__aemi__imported_profesion', true );
            $aemi__imported_especialidad = get_user_meta( $profileuser->ID, '__aemi__imported_especialidad', true );
            $aemi__imported_numero_de_colegiado = get_user_meta( $profileuser->ID, '__aemi__imported_numero_de_colegiado', true );
            $aemi__imported_ejerciente = get_user_meta( $profileuser->ID, '__aemi__imported_ejerciente', true );
            $aemi__imported_verificado = get_user_meta( $profileuser->ID, '__aemi__imported_verificado', true );
            $aemi__imported_estado = get_user_meta( $profileuser->ID, '__aemi__imported_estado', true );
            $aemi__imported_colegio = get_user_meta( $profileuser->ID, '__aemi__imported_colegio', true );
            $aemi__imported___powerappsid__ = get_user_meta( $profileuser->ID, '__aemi__imported___powerappsid__', true );
            $aemi__imported_user_type_on_import = get_user_meta( $profileuser->ID, '__aemi__imported_user_type_on_import', true );
            $aemi__imported_user_imported_from_spreadsheets = get_user_meta( $profileuser->ID, '__aemi__imported_user_imported_from_spreadsheets', true );

            if( $aemi__imported_user_imported_from_spreadsheets ):
            ?>
             <tr>
                <?php echo wp_nonce_field( self::$aemi_nonce_action, self::$aemi_nonce_name, true, false ); ?>
                <th scope="row"><?php echo __('Profession', AEMI_TEXT_DOMAIN); ?>:</th>
                <td><input class="regular-text" type="text" name="__aemi__imported_profesion" value="<?php echo esc_attr( $aemi__imported_profesion ); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><?php echo __('Specialty', AEMI_TEXT_DOMAIN); ?>:</th>
                <td><input class="regular-text" type="text" name="__aemi__imported_especialidad" value="<?php echo esc_attr( $aemi__imported_especialidad ); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><?php echo __('Collegiate number', AEMI_TEXT_DOMAIN); ?>:</th>
                <td><input class="regular-text" type="text" name="__aemi__imported_numero_de_colegiado" value="<?php echo esc_attr( $aemi__imported_numero_de_colegiado ); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><?php echo __('Practitioner', AEMI_TEXT_DOMAIN); ?>:</th>
                <td><input class="regular-text" type="text" name="__aemi__imported_ejerciente" value="<?php echo esc_attr( $aemi__imported_ejerciente ); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><?php echo __('Verified', AEMI_TEXT_DOMAIN); ?>:</th>
                <td><input class="regular-text" type="text" name="__aemi__imported_verificado" value="<?php echo esc_attr( $aemi__imported_verificado ); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><?php echo __('Status:', AEMI_TEXT_DOMAIN); ?></th>
                <td><input class="regular-text" type="text" name="__aemi__imported_estado" value="<?php echo esc_attr( $aemi__imported_estado ); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><?php echo __('School', AEMI_TEXT_DOMAIN); ?>:</th>
                <td><input class="regular-text" type="text" name="__aemi__imported_colegio" value="<?php echo esc_attr( $aemi__imported_colegio ); ?>" /></td>
            </tr>
            <tr>
                <th scope="row"><?php echo __('Imported User Type', AEMI_TEXT_DOMAIN); ?>:</th>
                <td>
                    <select name="__aemi__imported_user_type_on_import">
                        <option <?php echo ( $aemi__imported_user_type_on_import == 'farmaceutico' ? 'selected="" ': '' ); ?> value="farmaceutico"><?php echo __('farmaceutico', AEMI_TEXT_DOMAIN); ?></option>
                        <option <?php echo ( $aemi__imported_user_type_on_import == 'medico' ? 'selected="" ': '' ); ?> value="medico"><?php echo __('medico', AEMI_TEXT_DOMAIN); ?></option>
                    </select>
                </td>
            </tr>
            <?php
            endif;
        }

        public function save_custom_user_option( $user_id ){
           
            if( $user_id ){
                if( isset( $_POST[self::$aemi_nonce_name] ) && wp_verify_nonce( $_POST[self::$aemi_nonce_name], self::$aemi_nonce_action ) && $user_id ){
                    
                    $aemi__imported_user_imported_from_spreadsheets = get_user_meta( $user_id, '__aemi__imported_user_imported_from_spreadsheets', true );

                    if( $aemi__imported_user_imported_from_spreadsheets ){
                        $aemi__imported_profesion = ( isset( $_POST['__aemi__imported_profesion'] ) && $_POST['__aemi__imported_profesion'] ? sanitize_text_field( $_POST['__aemi__imported_profesion'] ) : '' );
                        update_user_meta( $user_id, '__aemi__imported_profesion', sanitize_text_field( $aemi__imported_profesion ) );
                        
                        $aemi__imported_especialidad = ( isset( $_POST['__aemi__imported_especialidad'] ) && $_POST['__aemi__imported_especialidad'] ? sanitize_text_field( $_POST['__aemi__imported_especialidad'] ) : '' );
                        update_user_meta( $user_id, '__aemi__imported_especialidad', sanitize_text_field( $aemi__imported_especialidad ) );
                        
                        $aemi__imported_numero_de_colegiado = ( isset( $_POST['__aemi__imported_numero_de_colegiado'] ) && $_POST['__aemi__imported_numero_de_colegiado'] ? sanitize_text_field( $_POST['__aemi__imported_numero_de_colegiado'] ) : '' );
                        update_user_meta( $user_id, '__aemi__imported_numero_de_colegiado', sanitize_text_field( $aemi__imported_numero_de_colegiado ) );
                        
                        $aemi__imported_ejerciente = ( isset( $_POST['__aemi__imported_ejerciente'] ) && $_POST['__aemi__imported_ejerciente'] ? sanitize_text_field( $_POST['__aemi__imported_ejerciente'] ) : '' );
                        update_user_meta( $user_id, '__aemi__imported_ejerciente', sanitize_text_field( $aemi__imported_ejerciente ) );
                        
                        $aemi__imported_verificado = ( isset( $_POST['__aemi__imported_verificado'] ) && $_POST['__aemi__imported_verificado'] ? sanitize_text_field( $_POST['__aemi__imported_verificado'] ) : '' );
                        update_user_meta( $user_id, '__aemi__imported_verificado', sanitize_text_field( $aemi__imported_verificado ) );
                        
                        $aemi__imported_estado = ( isset( $_POST['__aemi__imported_estado'] ) && $_POST['__aemi__imported_estado'] ? sanitize_text_field( $_POST['__aemi__imported_estado'] ) : '' );
                        update_user_meta( $user_id, '__aemi__imported_estado', sanitize_text_field( $aemi__imported_estado ) );
                        
                        $aemi__imported_colegio = ( isset( $_POST['__aemi__imported_colegio'] ) && $_POST['__aemi__imported_colegio'] ? sanitize_text_field( $_POST['__aemi__imported_colegio'] ) : '' );
                        update_user_meta( $user_id, '__aemi__imported_colegio', sanitize_text_field( $aemi__imported_colegio ) );

                        $aemi__imported_user_type_on_import = ( isset( $_POST['__aemi__imported_user_type_on_import'] ) && $_POST['__aemi__imported_user_type_on_import'] ? sanitize_text_field( $_POST['__aemi__imported_user_type_on_import'] ) : '' );
                        if( in_array( $aemi__imported_user_type_on_import, array( 'farmaceutico', 'medico' ) ) ){
                            update_user_meta( $user_id, '__aemi__imported_user_type_on_import', $aemi__imported_user_type_on_import );
                        }
                        
                    }
                }
            }
        }
    }

    new AemiGoogleSpreadSheestImportUserToWPUserEditCustomFields();
}