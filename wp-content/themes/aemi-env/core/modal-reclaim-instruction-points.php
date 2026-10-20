<?php 

if( !function_exists('get_user_viewed_modal_reclaim_instruction_points') ){
    function get_user_viewed_modal_reclaim_instruction_points( $user_id = false ){
        if( is_user_logged_in() ){
            $user_id = ( $user_id == false ? get_current_user_id() : $user_id );
            if( get_user_meta( $user_id, 'viewed_modal_reclaim_instruction_points', true ) ){
                return true;
            } else{
                return false;
            }
        }
        //se o usuário não estiver logado não vamos forçar o modal a aparecer
        return true;
    }
}

if( !class_exists( 'AemiModalReclaimInstructionPointsClass' ) ){
    class AemiModalReclaimInstructionPointsClass
    {
        public $nonce_name = 'aemi_reclaim_instruction_points';
        public $nonce_action = 'aemi_reclaim_instruction_points_action';
        
	    function __construct(){
            add_action( 'wp_ajax_aemi_mark_as_viewed_reclaim_instruction_points', array( $this, 'mark_reclaim_instruction_points' ) );

            if( is_admin() ){
              add_action( 'show_user_profile', array( $this, 'custom_fields_user_admin' ) );
              add_action( 'edit_user_profile', array( $this, 'custom_fields_user_admin' ) );
              add_action( 'personal_options_update', array( $this, 'save_option' ) );
              add_action( 'edit_user_profile_update', array( $this, 'save_option' ) );
            }
        }

        public function mark_reclaim_instruction_points(){


            if( is_user_logged_in() ){
                if( isset( $_POST[$this->nonce_name] ) && $_POST[$this->nonce_name] ){
                    if( wp_verify_nonce( $_POST[$this->nonce_name], $this->nonce_action ) ){
                        $user_id = get_current_user_id();
                        update_user_meta( $user_id, 'viewed_modal_reclaim_instruction_points', true );
                    }
                }
            }
        }
        
        /**
         * HTML Custom fields
         * 
         * @param WP_User $user
         */
        public function custom_fields_user_admin( $user ){
            $userID = false;
    
            if( $user ){
            $userArr = (Array) $user;
            
            if( $userArr && isset( $userArr['ID'] ) ){
                $userID = $userArr['ID'];
            }
            }
    
            $viewed_modal_reclaim_instruction_points = false;
    
            if( $userID ){
                $viewed_modal_reclaim_instruction_points = get_user_viewed_modal_reclaim_instruction_points( $userID );
            }
    
            wp_nonce_field( $this->nonce_action, $this->nonce_name );
            ?>
            <table class="form-table">
                <tr>
                    <th>
                        <label>Tutorial de puntadas visto:</label>
                    </th>
                    <td>
                        <select name="<?php echo 'viewed_modal_reclaim_instruction_points'; ?>" class="regular-text">
                            <option value="false" <?php echo ( $viewed_modal_reclaim_instruction_points == FALSE ? 'selected="selected"' : '' ); ?>>No</option>
                            <option value="true" <?php echo ( $viewed_modal_reclaim_instruction_points == TRUE ? 'selected="selected"' : '' ); ?>>Sí</option>
                        </select>
                        <br />
                        <span style="display: block; padding-top: 15px; opacity: 0.7;">
                            Si está marcado como Sí, significa que el usuario ya vio el modal sobre cómo obtener puntos de contenido
                        </span>
                    </td>
                </tr>
            </table>
            <?php
        }
        
        /**
         * Detect and save custom field user admin
         * 
         * @param Int $user_id
         */
        public function save_option( $user_id ){

            if( isset( $_POST[$this->nonce_name] ) && wp_verify_nonce( $_POST[$this->nonce_name], $this->nonce_action ) ){
    
                if( isset( $_POST['viewed_modal_reclaim_instruction_points'] ) ){
                    update_user_meta( $user_id, 'viewed_modal_reclaim_instruction_points', ( $_POST['viewed_modal_reclaim_instruction_points'] == 'true' ? true : false ) );
                }
            
            }
        }
    }

    $aemiModalReclaimInstructionPointsClass = new AemiModalReclaimInstructionPointsClass();
}