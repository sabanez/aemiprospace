<?php
class AemiUserLastAccessUpdate {
    public function __construct() {
        add_action('init', array($this, 'update_last_access'));
        add_action('show_user_profile', array($this, 'add_last_access_field'));
        add_action('edit_user_profile', array($this, 'add_last_access_field'));
    }

    public function update_last_access() {
        if(is_user_logged_in()) {
            $user_id = get_current_user_id();
            $last_access = get_user_meta($user_id, 'user_last_access', true);
            $current_date = date('Y-m-d');

            if(empty($last_access) || $last_access != $current_date) {
                update_user_meta($user_id, 'user_last_access', $current_date);
                do_action('activecampaign_update_last_access', $user_id);
            }
        }
    }

    public function add_last_access_field($user) {
        $last_access = get_user_meta($user->ID, 'user_last_access', true);
        ?>
        <h3>Información del último acceso</h3>
        <table class="form-table">
            <tr>
                <th><label for="user_last_access"><?php _e('Last Access', 'text-domain'); ?></label></th>
                <td><input type="text" name="user_last_access" id="user_last_access" value="<?php echo esc_attr($last_access); ?>" class="regular-text" readonly /></td>
            </tr>
        </table>
        <?php
    }
}

new AemiUserLastAccessUpdate();