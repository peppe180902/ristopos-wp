<?php
if (!defined('ABSPATH')) {
    exit; // Uscita se accesso diretto
}

// Definizione della tabella nel database
function ristopos_create_messages_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ristopos_messages';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        sender_id bigint(20) NOT NULL,
        recipient_id bigint(20) NOT NULL,
        message text NOT NULL,
        sent_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        read_at datetime DEFAULT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Invia un messaggio
function ristopos_send_staff_message($sender_id, $recipient_id, $message) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ristopos_messages';

    $wpdb->insert(
        $table_name,
        array(
            'sender_id' => $sender_id,
            'recipient_id' => $recipient_id,
            'message' => $message,
            'sent_at' => current_time('mysql')
        )
    );

    return $wpdb->insert_id;
}

// Recupera i messaggi per un utente
function ristopos_get_user_messages($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ristopos_messages';

    $messages = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE recipient_id = %d ORDER BY sent_at DESC",
            $user_id
        )
    );

    return $messages;
}

// Segna un messaggio come letto
function ristopos_mark_message_as_read($message_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ristopos_messages';

    $wpdb->update(
        $table_name,
        array('read_at' => current_time('mysql')),
        array('id' => $message_id)
    );
}

// Interfaccia utente per la messaggistica
function ristopos_display_messaging_interface() {
    $current_user_id = get_current_user_id();
    $messages = ristopos_get_user_messages($current_user_id);

    // Aggiungi questo codice per il debug
    $nonce = wp_create_nonce('ristopos_send_message');
    echo "<script>console.log('PHP generated nonce:', '" . esc_js($nonce) . "');</script>";

    echo '<div class="wrap ristopos-messaging">';
    echo '<h1>Sistema di Comunicazione Interna</h1>';
    
    // Form per inviare un nuovo messaggio
    echo '<div class="ristopos-new-message">';
    echo '<h2>Invia un nuovo messaggio</h2>';
    echo '<form id="send-message-form" method="post" onsubmit="return false;">';
    echo '<select name="recipient_id" required>';
    echo '<option value="">Seleziona un destinatario</option>';
    $staff = get_users(array('role__in' => array('administrator', 'editor', 'author', 'contributor')));
    foreach ($staff as $member) {
        if ($member->ID != $current_user_id) {
            echo '<option value="' . $member->ID . '">' . esc_html($member->display_name) . '</option>';
        }
    }
    echo '</select>';
    echo '<textarea name="message" required placeholder="Scrivi il tuo messaggio qui"></textarea>';
    echo '<button type="submit" class="button button-primary">Invia</button>';
    echo '</form>';
    echo '</div>';

    // Lista dei messaggi
    echo '<div class="ristopos-message-list">';
    echo '<h2>I tuoi messaggi</h2>';
    echo '<ul>';
    foreach ($messages as $message) {
        $sender = get_userdata($message->sender_id);
        echo '<li' . ($message->read_at ? '' : ' class="unread"') . '>';
        echo '<strong>' . esc_html($sender->display_name) . '</strong>: ';
        echo esc_html($message->message);
        echo '<span class="message-time">' . $message->sent_at . '</span>';
        echo '</li>';
    }
    echo '</ul>';
    echo '</div>';
    
    echo '</div>';
}

// Gestisci l'invio del messaggio via AJAX
function ristopos_handle_send_message() {
    error_log('ristopos_handle_send_message called');
    error_log('POST data: ' . print_r($_POST, true));
    error_log('Received nonce: ' . (isset($_POST['security']) ? $_POST['security'] : 'Not set'));

    $expected_nonce = wp_create_nonce('ristopos_send_message');
    error_log('Expected nonce: ' . $expected_nonce);

    if (!check_ajax_referer('ristopos_send_message', 'security', false)) {
        error_log('Nonce check failed. Received nonce: ' . $_POST['security']);
        wp_send_json_error(array('message' => 'Security check failed'));
        return;
    }

    $sender_id = get_current_user_id();
    $recipient_id = intval($_POST['recipient_id']);
    $message = sanitize_textarea_field($_POST['message']);
    
    error_log("Sender ID: $sender_id, Recipient ID: $recipient_id, Message: $message");

    $message_id = ristopos_send_staff_message($sender_id, $recipient_id, $message);

    if ($message_id) {
        error_log("Message sent successfully. Message ID: $message_id");
        wp_send_json_success(array('message' => 'Messaggio inviato con successo.'));
    } else {
        error_log("Error sending message");
        wp_send_json_error(array('message' => 'Errore nell\'invio del messaggio.'));
    }
}
add_action('wp_ajax_ristopos_send_message', 'ristopos_handle_send_message');