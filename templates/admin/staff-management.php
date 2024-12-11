<?php
if (!defined('ABSPATH')) {
    exit; // Uscita se accesso diretto
}


function ristopos_process_add_staff() {
    if (!current_user_can('ristopos_access')) {
        wp_die(__('Non hai i permessi per eseguire questa azione.', 'ristopos'));
    }

    check_admin_referer('ristopos_add_staff');

    $username = sanitize_user($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $role = sanitize_text_field($_POST['role']);
    $password = $_POST['password'];

    error_log("Attempting to create user: $username, $email, $role");

    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        error_log("Error creating user: " . $user_id->get_error_message());
        wp_die($user_id->get_error_message());
    } else {
        error_log("User created successfully. User ID: $user_id");
        
        $update_result = wp_update_user(array(
            'ID' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'role' => $role
        ));

        if (is_wp_error($update_result)) {
            error_log("Error updating user: " . $update_result->get_error_message());
        } else {
            error_log("User updated successfully");
        }

        $set_password_result = wp_set_password($password, $user_id);
        error_log("Set password result: " . ($set_password_result === null ? "Success" : "Failure"));

        wp_new_user_notification($user_id, null, 'both');

        wp_redirect(add_query_arg('message', 'staff_added', wp_get_referer()));
        exit;
    }
}
add_action('admin_post_ristopos_add_staff', 'ristopos_process_add_staff');

function ristopos_process_edit_staff() {
    if (!current_user_can('ristopos_access')) {
        wp_die(__('Non hai i permessi per eseguire questa azione.', 'ristopos'));
    }

    check_admin_referer('ristopos_edit_staff');

    $user_id = intval($_POST['staff_id']);
    $email = sanitize_email($_POST['email']);
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $role = sanitize_text_field($_POST['role']);

    $user_data = array(
        'ID' => $user_id,
        'user_email' => $email,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'role' => $role
    );

    $user_id = wp_update_user($user_data);

    if (is_wp_error($user_id)) {
        // Gestisci l'errore
        wp_die($user_id->get_error_message());
    } else {
        // Reindirizza con un messaggio di successo
        wp_redirect(add_query_arg('message', 'staff_updated', wp_get_referer()));
        exit;
    }
}
add_action('admin_post_ristopos_edit_staff', 'ristopos_process_edit_staff');

function ristopos_process_delete_staff() {
    if (!current_user_can('ristopos_access')) {
        wp_die(__('Non hai i permessi per eseguire questa azione.', 'ristopos'));
    }

    $user_id = intval($_GET['staff_id']);
    check_admin_referer('delete_staff_' . $user_id);

    if (wp_delete_user($user_id)) {
        wp_redirect(add_query_arg('message', 'staff_deleted', wp_get_referer()));
        exit;
    } else {
        wp_die(__('Si è verificato un errore durante l\'eliminazione del membro dello staff.', 'ristopos'));
    }
}
add_action('admin_post_ristopos_delete_staff', 'ristopos_process_delete_staff');

function ristopos_admin_notices() {
    if (isset($_GET['message'])) {
        $message = '';
        switch ($_GET['message']) {
            case 'staff_added':
                $message = __('Membro dello staff aggiunto con successo.', 'ristopos');
                break;
            case 'staff_updated':
                $message = __('Membro dello staff aggiornato con successo.', 'ristopos');
                break;
            case 'staff_deleted':
                $message = __('Membro dello staff eliminato con successo.', 'ristopos');
                break;
        }
        if ($message) {
            echo '<div class="notice notice-success is-dismissible"><p>' . $message . '</p></div>';
        }
    }
}
add_action('admin_notices', 'ristopos_admin_notices');

function ristopos_display_staff_form() {
    $staff_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
    $staff = $staff_id ? get_userdata($staff_id) : null;

    ?>
    <h2><?php echo $staff ? __('Modifica Membro dello Staff', 'ristopos') : __('Aggiungi Nuovo Membro dello Staff', 'ristopos'); ?></h2>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php wp_nonce_field($staff ? 'ristopos_edit_staff' : 'ristopos_add_staff'); ?>
        <input type="hidden" name="action" value="<?php echo $staff ? 'ristopos_edit_staff' : 'ristopos_add_staff'; ?>">
        <?php if ($staff): ?>
            <input type="hidden" name="staff_id" value="<?php echo $staff_id; ?>">
        <?php endif; ?>
        <table class="form-table">
            <tr>
                <th><label for="username"><?php _e('Username', 'ristopos'); ?></label></th>
                <td>
                    <?php if ($staff): ?>
                        <input type="text" name="username" id="username" value="<?php echo esc_attr($staff->user_login); ?>" disabled>
                    <?php else: ?>
                        <input type="text" name="username" id="username" required>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><label for="email"><?php _e('Email', 'ristopos'); ?></label></th>
                <td><input type="email" name="email" id="email" value="<?php echo $staff ? esc_attr($staff->user_email) : ''; ?>" required></td>
            </tr>
            <tr>
                <th><label for="first_name"><?php _e('Nome', 'ristopos'); ?></label></th>
                <td><input type="text" name="first_name" id="first_name" value="<?php echo $staff ? esc_attr($staff->first_name) : ''; ?>"></td>
            </tr>
            <tr>
                <th><label for="last_name"><?php _e('Cognome', 'ristopos'); ?></label></th>
                <td><input type="text" name="last_name" id="last_name" value="<?php echo $staff ? esc_attr($staff->last_name) : ''; ?>"></td>
            </tr>
            <tr>
                <th><label for="role"><?php _e('Ruolo', 'ristopos'); ?></label></th>
                <td>
                    <select name="role" id="role">
                        <option value="waiter" <?php selected($staff && in_array('waiter', (array)$staff->roles)); ?>><?php _e('Cameriere', 'ristopos'); ?></option>
                        <option value="chef" <?php selected($staff && in_array('chef', (array)$staff->roles)); ?>><?php _e('Chef', 'ristopos'); ?></option>
                        <option value="restaurant_manager" <?php selected($staff && in_array('restaurant_manager', (array)$staff->roles)); ?>><?php _e('Manager', 'ristopos'); ?></option>
                    </select>
                </td>
            </tr>
            <?php if (!$staff): ?>
            <tr>
                <th><label for="password"><?php _e('Password', 'ristopos'); ?></label></th>
                <td>
                    <input type="password" name="password" id="password" required>
                    <p class="description"><?php _e('La password deve essere forte. Usa lettere maiuscole e minuscole, numeri e simboli.', 'ristopos'); ?></p>
                </td>
            </tr>
            <?php endif; ?>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo $staff ? __('Aggiorna Membro', 'ristopos') : __('Aggiungi Membro', 'ristopos'); ?>">
        </p>
    </form>
    <?php
}

function ristopos_staff_styles() {
    echo '
    <style>
     .ristopos-div-button {
        display: flex;
        gap: 5px;
    }
    .wrap {
        margin: 10px 20px 0 20px;
    }
        .ristopos-header {
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    
        .ristopos-button {
            background-color: #0073aa;
            color: white;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
    </style>
    ';
}

function ristopos_display_staff_list() {
    $staff = get_users(array('role__in' => array('waiter', 'chef', 'restaurant_manager')));

    echo '<h2>Lista del Personale</h2>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Nome</th><th>Email</th><th>Ruolo</th><th>Azioni</th></tr></thead>';
    echo '<tbody>';
    foreach ($staff as $member) {
        $role = reset($member->roles);
        $role_display = '';
        switch ($role) {
            case 'waiter':
                $role_display = 'Cameriere';
                break;
            case 'chef':
                $role_display = 'Chef';
                break;
            case 'restaurant_manager':
                $role_display = 'Manager';
                break;
            default:
                $role_display = ucfirst($role);; // Salta gli utenti con ruoli non personalizzati
        }

        echo '<tr>';
        echo '<td>' . esc_html($member->display_name) . '</td>';
        echo '<td>' . esc_html($member->user_email) . '</td>';
        echo '<td>' . esc_html($role_display) . '</td>';
        echo '<td>';
        if (current_user_can('ristopos_access')) {
            echo '<a href="' . esc_url(add_query_arg(array('page' => 'ristopos-staff', 'edit' => $member->ID), admin_url('admin.php'))) . '" class="button">Modifica</a> ';
            echo '<a href="' . esc_url(wp_nonce_url(add_query_arg(array('action' => 'ristopos_delete_staff', 'staff_id' => $member->ID), admin_url('admin-post.php')), 'delete_staff_' . $member->ID)) . '" class="button" onclick="return confirm(\'Sei sicuro di voler eliminare questo membro dello staff?\')">Elimina</a>';
        }
        echo '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}
