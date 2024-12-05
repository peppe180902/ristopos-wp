<?php
/**
 * Plugin Name: RistoPOS
 * Plugin URI: http://example.com/ristopos
 * Description: Un plugin POS per gestire un ristorante integrato con WooCommerce
 * Requires Plugins: woocommerce
 * Version: 0.1.1
 * Author: Phyno
 * Author URI: https://www.giuseppenapoli.com/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: ristopos
 * Domain Path: /languages
 */

 if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


// Verifica la presenza di WooCommerce
function ristopos_check_woocommerce() {
    if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        return true;
    }
    if (is_multisite() && array_key_exists('woocommerce/woocommerce.php', get_site_option('active_sitewide_plugins', array()))) {
        return true;
    }
    return false;
}


// Avviso se WooCommerce non è attivo
function ristopos_woocommerce_missing_notice() {
    ?>
    <div class="error">
        <p><?php _e('RistoPOS richiede che WooCommerce sia installato e attivo.', 'ristopos'); ?></p>
    </div>
    <?php
}

function ristopos_hide_admin_bar() {
    if (isset($_GET['page']) && ($_GET['page'] === 'ristopos' || $_GET['page'] === 'ristopos-orders' || $_GET['page'] === 'ristopos-products' || $_GET['page'] === 'ristopos-tables' || $_GET['page'] === 'ristopos-product-management' || $_GET['page'] === 'ristopos-analytics' || $_GET['page'] === 'ristopos-staff')) {
        add_filter('show_admin_bar', '__return_false');
        add_action('admin_head', 'ristopos_custom_admin_styles');
    }
}
add_action('admin_init', 'ristopos_hide_admin_bar');

function ristopos_custom_admin_styles() {
    echo '
    <style>
            .form-table th {
                width: 150px;
            }
            .form-table input[type="text"],
            .form-table input[type="number"],
            .form-table select {
                width: 300px;
            }
            .wp-list-table img {
                max-width: 50px;
                height: auto;
            }

        .ristopos-button {
            background-color: #0073aa;
            color: white;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .wrap {
            margin: 10px 20px 0 20px;
        }
        .ristopos-div-button {
            display: flex;
            gap: 5px;
        }
        .ristopos-header {
            background-color: #23282d;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        #wpcontent, #wpfooter {
            margin-left: 0 !important;
            padding-left: 0 !important;
        }
        #wpbody-content {
            padding-bottom: 0;
        }
        #wpbody {
            padding-top: 0px
        }
        #wpfooter {
            display: none;
        }
        html.wp-toolbar {
            padding-top: 0 !important;
        }
        #wpadminbar, #adminmenumain {
            display: none;
        }
        .wrap.ristopos-container {
            margin: 0;
            max-width: 100%;
        }
        
        #wpadminbar, #adminmenuback, #adminmenuwrap { display: none !important; }
        #wpcontent, #wpfooter { margin-left: 0 !important; }
    </style>
    ';
}

function ristopos_add_menu() {
    add_menu_page(
        'RistoPOS',
        'RistoPOS',
        'ristopos_access',
        'ristopos',
        'ristopos_admin_page',
        'dashicons-food',
        56
    );

    add_submenu_page(
        'ristopos',
        'POS',
        'POS',
        'take_orders',
        'ristopos-products',
        'ristopos_products_page'
    );

    add_submenu_page(
        'ristopos',
        'Ordini',
        'Ordini',
        'view_orders',
        'ristopos-orders',
        'ristopos_orders_page'
    );

    add_submenu_page(
        'ristopos',
        'Gestione Tavoli',
        'Gestione Tavoli',
        'take_orders',
        'ristopos-tables',
        'ristopos_tables_page'
    );

    add_submenu_page(
        'ristopos',
        'Gestione Prodotti',
        'Gestione Prodotti',
        'manage_menu',
        'ristopos-product-management',
        'ristopos_product_management_page'
    );

    add_submenu_page(
        'ristopos',
        'Dashboard Analitica',
        'Dashboard Analitica',
        'view_reports',
        'ristopos-analytics',
        'ristopos_analytics_dashboard'
    );

    add_submenu_page(
        'ristopos',
        'Gestione del Personale',
        'Gestione del Personale',
        'manage_staff',
        'ristopos-staff',
        'ristopos_staff_management'
    );
}

function ristopos_login_redirect($redirect_to, $request, $user) {
    if (isset($user->roles) && is_array($user->roles)) {
        if (in_array('waiter', $user->roles) || in_array('chef', $user->roles) || in_array('restaurant_manager', $user->roles)) {
            return admin_url('admin.php?page=ristopos');
        }
    }
    return $redirect_to;
}
add_filter('login_redirect', 'ristopos_login_redirect', 10, 3);

// Pagine del menu
function ristopos_admin_page() {
    echo '<div class="ristopos-header">';
    
    // Bottone per tornare a WordPress
    echo '<a href="' . admin_url() . '" class="ristopos-button ristopos-back-button">Torna a WordPress</a>';
    
    // Sezione dei pulsanti principali
    echo '<div class="ristopos-div-button">';
    echo '</div>';
    
    echo '</div>'; // Chiudi il div .ristopos-header

    // Sezione principale di benvenuto
    ?>
    <div class="wrap ristopos-welcome-page">
        <h1>Benvenuto in RistoPOS - Gestione Completa per il Tuo Ristorante</h1>
        
        <div class="ristopos-intro">
            <p>RistoPOS è la soluzione integrata per la gestione efficiente del tuo ristorante. Dalla gestione degli ordini alla reportistica avanzata, RistoPOS ti offre tutti gli strumenti necessari per ottimizzare le tue operazioni.</p>
        </div>

        <div class="ristopos-features">
            <h2>Funzionalità Principali</h2>
            <div class="ristopos-feature-grid">
                <div class="ristopos-feature-item">
                    <h3><span class="dashicons dashicons-cart"></span> Gestione Ordini</h3>
                    <p>Gestisci facilmente gli ordini in corso e accedi allo storico completo. Ottimizza il flusso di lavoro dalla cucina alla sala.</p>
                    <a href="<?php echo admin_url('admin.php?page=ristopos-orders'); ?>" class="button button-primary">Gestisci Ordini</a>
                </div>
                <div class="ristopos-feature-item">
                    <h3><span class="dashicons dashicons-grid-view"></span> Gestione Tavoli</h3>
                    <p>Organizza e assegna i tavoli in modo efficiente. Monitora lo stato di ogni tavolo in tempo reale.</p>
                    <a href="<?php echo admin_url('admin.php?page=ristopos-tables'); ?>" class="button button-primary">Gestisci Tavoli</a>
                </div>
                <div class="ristopos-feature-item">
                    <h3><span class="dashicons dashicons-products"></span> Gestione Prodotti</h3>
                    <p>Aggiungi, modifica ed elimina prodotti con facilità. Gestisci il tuo menu in modo dinamico.</p>
                    <a href="<?php echo admin_url('admin.php?page=ristopos-product-management'); ?>" class="button button-primary">Gestisci Prodotti</a>
                </div>
                <div class="ristopos-feature-item">
                    <h3><span class="dashicons dashicons-money-alt"></span> Sistema POS</h3>
                    <p>Gestisci le transazioni al tavolo con il nostro sistema POS intuitivo e veloce.</p>
                    <a href="<?php echo admin_url('admin.php?page=ristopos-products'); ?>" class="button button-primary">Apri POS</a>
                </div>
                <div class="ristopos-feature-item">
                    <h3><span class="dashicons dashicons-chart-bar"></span> Reportistica Avanzata</h3>
                    <p>Analizza le performance del tuo ristorante con report dettagliati e insights preziosi.</p>
                    <a href="<?php echo admin_url('admin.php?page=ristopos-analytics'); ?>" class="button button-primary">Visualizza Report</a>
                </div>
                <div class="ristopos-feature-item">
                    <h3><span class="dashicons dashicons-groups"></span> Gestione Personale</h3>
                    <p>Gestisci efficacemente il tuo staff, assegna ruoli e monitora le performance.</p>
                    <a href="<?php echo admin_url('admin.php?page=ristopos-staff'); ?>" class="button button-primary">Gestisci Staff</a>
                </div>
            </div>
        </div>

        <div class="ristopos-quickstart">
            <h2>Guida Rapida</h2>
            <ol>
                <li><strong>Configura i Prodotti:</strong> Inizia aggiungendo i tuoi prodotti nella sezione Gestione Prodotti.</li>
                <li><strong>Imposta i Tavoli:</strong> Configura la disposizione dei tavoli del tuo ristorante.</li>
                <li><strong>Gestisci lo Staff:</strong> Aggiungi il tuo personale e assegna i ruoli appropriati.</li>
                <li><strong>Inizia con gli Ordini:</strong> Usa il sistema POS per iniziare a prendere ordini.</li>
                <li><strong>Monitora le Prestazioni:</strong> Utilizza la sezione Reportistica per analizzare le performance del tuo ristorante.</li>
            </ol>
        </div>

        <div class="ristopos-support">
            <h2>Hai Bisogno di Aiuto?</h2>
            <p>Se hai domande o hai bisogno di assistenza, non esitare a contattare il nostro team di supporto.</p>
            <a href="#" class="button button-secondary">Contatta il Supporto</a>
        </div>
    </div>
    <?php
}


function ristopos_products_page() {
    require_once plugin_dir_path(__FILE__) . 'admin/ristopos-products.php';
}

function ristopos_orders_page() {
    require_once plugin_dir_path(__FILE__) . 'admin/ristopos-orders.php';
}

function ristopos_tables_page() {
    require_once plugin_dir_path(__FILE__) . 'admin/ristopos-tables.php';
}

function ristopos_product_management_page() {
    require_once plugin_dir_path(__FILE__) . 'admin/ristopos-product-management.php';
}

require_once plugin_dir_path(__FILE__) . 'admin/ristopos-analytics.php';

require_once plugin_dir_path(__FILE__) . 'admin/ristopos-staff-management.php';

require_once plugin_dir_path(__FILE__) . 'admin/ristopos-messaging.php';

require_once plugin_dir_path(__FILE__) . 'admin/ristopos-navigation.php';


// Inizializza il plugin
function ristopos_init() {
    if (ristopos_check_woocommerce()) {
        add_action('admin_menu', 'ristopos_add_menu');
    } else {
        add_action('admin_notices', 'ristopos_woocommerce_missing_notice');
    }
}

//modifica orario
function ristopos_set_timezone() {
    date_default_timezone_set('Europe/Rome');
}
add_action('init', 'ristopos_set_timezone');

function get_table_status_text($status) {
    return $status === 'occupied' ? 'Occupato' : 'Libero';
}

//crea ordine
function ristopos_create_order() {
    check_ajax_referer('ristopos-nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permessi insufficienti');
    }

    $cart = json_decode(stripslashes($_POST['cart']), true);
    $table_id = $_POST['table_id'];

    if (empty($cart)) {
        wp_send_json_error('Carrello vuoto');
    }

    $order = wc_create_order();

    $current_user_id = get_current_user_id();
    $order->set_customer_id($current_user_id);

    $order_total = 0;
    foreach ($cart as $item) {
        $product = wc_get_product($item['id']);
        $order_item_id = $order->add_product($product, $item['quantity']);
        
        if (!empty($item['notes'])) {
            wc_add_order_item_meta($order_item_id, 'Note', sanitize_textarea_field($item['notes']));
        }

        $order_total += $item['price'] * $item['quantity'];
    }

    $order->calculate_totals();
    $order->update_status('completed', 'Ordine creato e completato tramite RistoPOS');
    $order->set_date_created(current_time('mysql', true));

    $order->update_meta_data('_created_via', 'RistoPOS');
    $order->update_meta_data('_table_id', $table_id);

    $order->save();

    // Aggiorna le informazioni del tavolo
    $tables = get_option('ristopos_tables', array());
    if (isset($tables[$table_id])) {
        $tables[$table_id]['status'] = 'occupied';
        $tables[$table_id]['total'] += $order_total;
        $tables[$table_id]['orders'][] = $order->get_id();
        update_option('ristopos_tables', $tables);
    }

    wp_send_json_success(array(
        'order_id' => $order->get_id(),
        'table_total' => $tables[$table_id]['total']
    ));
}
add_action('wp_ajax_ristopos_create_order', 'ristopos_create_order');

// Funzione per aggiungere un tavolo
function ristopos_add_table_ajax() {
    check_ajax_referer('ristopos-nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Permessi insufficienti'));
        return;
    }

    $tables = get_option('ristopos_tables', array());
    
    $new_table_id = 1;
    while (isset($tables[$new_table_id])) {
        $new_table_id++;
    }
    
    $tables[$new_table_id] = array(
        'status' => 'free',
        'total' => 0,
        'orders' => array()
    );
    
    update_option('ristopos_tables', $tables);
    
    wp_send_json_success(array(
        'message' => 'Tavolo aggiunto con successo',
        'table_id' => $new_table_id,
        'tables' => $tables  // Invia tutti i dati dei tavoli aggiornati
    ));
}
add_action('wp_ajax_ristopos_add_table', 'ristopos_add_table_ajax');

// Funzione per eliminare un tavolo
function ristopos_delete_table_ajax() {
    error_log('ristopos_delete_table_ajax called');
    error_log(print_r($_POST, true));

    check_ajax_referer('ristopos-nonce', 'nonce');
    $table_id = $_POST['table_id'];
    $tables = get_option('ristopos_tables', array());
    if (isset($tables[$table_id])) {
        unset($tables[$table_id]);
        update_option('ristopos_tables', $tables);
        wp_send_json_success(array(
            'message' => 'Tavolo eliminato con successo',
            'table_id' => $table_id,
            'tables' => $tables
        ));
    } else {
        wp_send_json_error(array('message' => 'Tavolo non trovato'));
    }
}
add_action('wp_ajax_ristopos_delete_table', 'ristopos_delete_table_ajax');

// Funzione per svuotare un tavolo
function ristopos_clear_table_ajax() {
    error_log('ristopos_clear_table_ajax called');
    error_log(print_r($_POST, true));

    check_ajax_referer('ristopos-nonce', 'nonce');
    $table_id = $_POST['table_id'];
    $tables = get_option('ristopos_tables', array());
    if (isset($tables[$table_id])) {
        $tables[$table_id]['status'] = 'free';
        $tables[$table_id]['total'] = 0;
        $tables[$table_id]['orders'] = array();
        update_option('ristopos_tables', $tables);
        wp_send_json_success(array(
            'message' => 'Tavolo svuotato con successo',
            'table_id' => $table_id,
            'tables' => $tables
        ));
    } else {
        wp_send_json_error(array('message' => 'Tavolo non trovato'));
    }
}
add_action('wp_ajax_ristopos_clear_table', 'ristopos_clear_table_ajax');

// Funzione per recuperare i dettagli di un tavolo
function ristopos_get_table_details_ajax() {
    check_ajax_referer('ristopos-nonce', 'nonce');
    $table_id = $_POST['table_id'];
    $tables = get_option('ristopos_tables', array());
    
    if (isset($tables[$table_id])) {
        $table = $tables[$table_id];
        $orders_details = array();
        
        foreach ($table['orders'] as $order_id) {
            $order = wc_get_order($order_id);
            if ($order) {
                $user_id = $order->get_customer_id();
                $user = get_user_by('id', $user_id);
                $waiter_name = $user ? $user->user_login : 'N/A';
                
                $order_details = array(
                    'order_id' => $order_id,
                    'waiter' => $waiter_name,
                    'date' => $order->get_date_created()->date_i18n('Y-m-d H:i:s'),
                    'products' => array()
                );
                
                foreach ($order->get_items() as $item) {
                    $product = array(
                        'name' => $item->get_name(),
                        'quantity' => $item->get_quantity(),
                        'note' => wc_get_order_item_meta($item->get_id(), 'Note', true)
                    );
                    $order_details['products'][] = $product;
                }
                
                $orders_details[] = $order_details;
            }
        }
        
        wp_send_json_success(array('orders' => $orders_details));
    } else {
        wp_send_json_error(array('message' => 'Tavolo non trovato'));
    }
}
add_action('wp_ajax_ristopos_get_table_details', 'ristopos_get_table_details_ajax');

// Funzione per recuperare i dati dei prodotti
function ristopos_get_products() {
    check_ajax_referer('ristopos-nonce', 'nonce');
    $products = wc_get_products(array('limit' => -1));
    $product_list = array();

    foreach ($products as $product) {
        $product_list[] = array(
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'price' => $product->get_price(),
            'image' => wp_get_attachment_image_url($product->get_image_id(), 'thumbnail'),
            'categories' => wp_get_post_terms($product->get_id(), 'product_cat', array('fields' => 'names'))
        );
    }

    wp_send_json_success($product_list);
}
add_action('wp_ajax_ristopos_get_products', 'ristopos_get_products');

// Funzione per aggiungere un prodotto
function ristopos_add_product() {
    check_ajax_referer('ristopos-nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permessi insufficienti');
    }

    $product = new WC_Product_Simple();
    $product->set_name(sanitize_text_field($_POST['product_name']));
    $product->set_regular_price(floatval($_POST['product_price']));
    $product->set_category_ids($_POST['product_category']);

    if (!empty($_FILES['product_image'])) {
        $upload = wp_upload_bits($_FILES['product_image']['name'], null, file_get_contents($_FILES['product_image']['tmp_name']));
        if (!$upload['error']) {
            $wp_filetype = wp_check_filetype($upload['file'], null);
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name($_FILES['product_image']['name']),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attachment_id = wp_insert_attachment($attachment, $upload['file']);
            if (!is_wp_error($attachment_id)) {
                require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                wp_update_attachment_metadata($attachment_id, $attachment_data);
                $product->set_image_id($attachment_id);
            }
        }
    }

    $product_id = $product->save();

    if ($product_id) {
        wp_send_json_success(array('message' => 'Prodotto aggiunto con successo', 'product_id' => $product_id));
    } else {
        wp_send_json_error('Errore nell\'aggiunta del prodotto');
    }
}
add_action('wp_ajax_ristopos_add_product', 'ristopos_add_product');

// Funzione per modificare un prodotto
function ristopos_update_product() {
    check_ajax_referer('ristopos-nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permessi insufficienti');
    }

    $product_id = intval($_POST['product_id']);
    $product = wc_get_product($product_id);

    if (!$product) {
        wp_send_json_error('Prodotto non trovato');
    }

    $product->set_name(sanitize_text_field($_POST['product_name']));
    $product->set_regular_price(floatval($_POST['product_price']));
    $product->set_category_ids($_POST['product_category']);

    if (!empty($_FILES['product_image'])) {
        $upload = wp_upload_bits($_FILES['product_image']['name'], null, file_get_contents($_FILES['product_image']['tmp_name']));
        if (!$upload['error']) {
            $wp_filetype = wp_check_filetype($upload['file'], null);
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name($_FILES['product_image']['name']),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attachment_id = wp_insert_attachment($attachment, $upload['file']);
            if (!is_wp_error($attachment_id)) {
                require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                wp_update_attachment_metadata($attachment_id, $attachment_data);
                $product->set_image_id($attachment_id);
            }
        }
    }

    $updated = $product->save();

    if ($updated) {
        wp_send_json_success(array('message' => 'Prodotto aggiornato con successo'));
    } else {
        wp_send_json_error('Errore nell\'aggiornamento del prodotto');
    }
}
add_action('wp_ajax_ristopos_update_product', 'ristopos_update_product');

// Funzione per eliminare un prodotto
function ristopos_ajax_delete_product() {
    check_ajax_referer('ristopos-nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permessi insufficienti');
    }

    if (!function_exists('wc_get_product')) {
        wp_send_json_error('WooCommerce non è attivo o non è stato caricato correttamente');
    }

    $product_id = intval($_POST['product_id']);
    $product = wc_get_product($product_id);

    if (!$product) {
        wp_send_json_error('Prodotto non trovato');
    }

    if ($product->delete(true)) {
        wp_send_json_success(array('message' => 'Prodotto eliminato con successo'));
    } else {
        wp_send_json_error('Errore nell\'eliminazione del prodotto');
    }
}
add_action('wp_ajax_ristopos_delete_product', 'ristopos_ajax_delete_product');

function ristopos_create_custom_roles() {
    // Rimuovi i ruoli esistenti per evitare conflitti
    remove_role('waiter');
    remove_role('chef');
    remove_role('restaurant_manager');

    // Capacità di base per tutti i ruoli
    $base_caps = array(
        'read' => true,
        'ristopos_access' => true,
    );

    // Crea il ruolo di Cameriere
    add_role('waiter', 'Cameriere', array_merge($base_caps, array(
        'view_orders' => true,
        'take_orders' => true,
    )));

    // Crea il ruolo di Chef
    add_role('chef', 'Chef', array_merge($base_caps, array(
        'view_orders' => true,
        'manage_menu' => true,
    )));

    // Crea il ruolo di Manager
    add_role('restaurant_manager', 'Manager', array_merge($base_caps, array(
        'view_orders' => true,
        'take_orders' => true,
        'manage_menu' => true,
        'manage_staff' => true,
        'view_reports' => true,
    )));

    // Aggiungi capacità all'amministratore
    $admin = get_role('administrator');
    $admin->add_cap('ristopos_access');
    $admin->add_cap('view_orders');
    $admin->add_cap('take_orders');
    $admin->add_cap('manage_menu');
    $admin->add_cap('manage_staff');
    $admin->add_cap('view_reports');
}

// Esegui questa funzione all'attivazione del plugin e quando il plugin viene aggiornato
register_activation_hook(__FILE__, 'ristopos_create_custom_roles');
add_action('plugins_loaded', 'ristopos_create_custom_roles');


function ristopos_plugin_activation() {
    ristopos_create_custom_roles();
    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'ristopos_plugin_activation');

// Funzione per recuperare i dati di vendita
function ristopos_enqueue_chartjs($hook) {
    if ('ristopos_page_ristopos-analytics' !== $hook) {
        return;
    }
    wp_enqueue_style('ristopos-analytics-css', plugin_dir_url(__FILE__) . 'admin/css/ristopos-analytics.css');
    wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.7.0', true);
    wp_enqueue_script('ristopos-charts', plugin_dir_url(__FILE__) . 'admin/js/ristopos-charts.js', array('jquery', 'chartjs'), '1.0.0', true);
}
add_action('admin_enqueue_scripts', 'ristopos_enqueue_chartjs');

// Funzione per inviare un messaggio
function ristopos_enqueue_messaging_assets($hook) {
    if ('ristopos_page_ristopos-messaging' !== $hook) {
        return;
    }
    wp_enqueue_style('ristopos-messaging', plugin_dir_url(__FILE__) . 'admin/css/ristopos-messaging.css', array(), '1.0.0');
    wp_enqueue_script('ristopos-messaging', plugin_dir_url(__FILE__) . 'admin/js/ristopos-messaging.js', array('jquery'), '1.0.0', true);
    
    $script_data = array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('ristopos_send_message')
    );
    wp_localize_script('ristopos-messaging', 'ristopos_ajax', $script_data);

    // Debug
    echo "<script>console.log('PHP script data:', " . json_encode($script_data) . ");</script>";
}
add_action('admin_enqueue_scripts', 'ristopos_enqueue_messaging_assets');


function ristopos_activate_plugin() {
    ristopos_create_messages_table();
}
register_activation_hook(__FILE__, 'ristopos_activate_plugin');

function ristopos_enqueue_scripts($hook) {
    // Carica gli script solo nelle pagine del plugin
    if (strpos($hook, 'ristopos') === false) {
        return;
    }

    wp_enqueue_style('dashicons');
    wp_enqueue_script('ristopos-js', plugin_dir_url(__FILE__) . 'admin/js/ristopos.js', array('jquery'), time(), true);
    wp_localize_script('ristopos-js', 'ristopos_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ristopos-nonce')
    ));
}
add_action('admin_enqueue_scripts', 'ristopos_enqueue_scripts');

add_action('admin_footer', function() {
    if (isset($_GET['page']) && $_GET['page'] === 'ristopos-messaging') {
        echo "<script>console.log('Admin footer reached for ristopos-messaging page');</script>";
        
        global $wp_scripts;
        $loaded_scripts = array();
        foreach ($wp_scripts->queue as $handle) {
            $loaded_scripts[] = $handle;
        }
        echo "<script>console.log('Loaded scripts:', " . json_encode($loaded_scripts) . ");</script>";
    }
});

// Aggiungi questa funzione nel tuo file principale ristopos.php
function ristopos_add_custom_roles_and_capabilities() {
    // Rimuovi i ruoli esistenti per evitare conflitti
    remove_role('waiter');
    remove_role('chef');
    remove_role('restaurant_manager');

    // Crea il ruolo di Cameriere
    add_role('waiter', 'Cameriere', array(
        'read' => true,
        'ristopos_access' => true,
        'view_orders' => true,
        'take_orders' => true,
        'access_dashboard' => true
    ));

    // Crea il ruolo di Chef
    add_role('chef', 'Chef', array(
        'read' => true,
        'ristopos_access' => true,
        'view_orders' => true,
        'manage_menu' => true,
        'access_dashboard' => true
    ));

    // Crea il ruolo di Manager
    add_role('restaurant_manager', 'Manager', array(
        'read' => true,
        'ristopos_access' => true,
        'view_orders' => true,
        'take_orders' => true,
        'manage_menu' => true,
        'manage_staff' => true,
        'view_reports' => true,
        'access_dashboard' => true
    ));

    // Aggiungi capacità all'amministratore
    $admin = get_role('administrator');
    $admin->add_cap('ristopos_access');
    $admin->add_cap('view_orders');
    $admin->add_cap('take_orders');
    $admin->add_cap('manage_menu');
    $admin->add_cap('manage_staff');
    $admin->add_cap('view_reports');
}

function ristopos_redirect_custom_roles() {
    $user = wp_get_current_user();
    $custom_roles = array('restaurant_manager', 'chef', 'waiter');

    if (array_intersect($custom_roles, $user->roles) && is_admin()) {
        $screen = get_current_screen();
        if ($screen->id === 'dashboard') {
            wp_redirect(admin_url('admin.php?page=ristopos'));
            exit;
        }
    }
}
add_action('current_screen', 'ristopos_redirect_custom_roles');

function ristopos_check_admin_access() {
    $user = wp_get_current_user();
    $allowed_roles = array('administrator', 'restaurant_manager', 'chef', 'waiter');

    if (!array_intersect($allowed_roles, $user->roles)) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('admin_init', 'ristopos_check_admin_access');


function ristopos_welcome_message() {
    $user = wp_get_current_user();
    $custom_roles = array('waiter', 'chef', 'restaurant_manager');

    if (array_intersect($custom_roles, $user->roles)) {
        echo '<div class="notice notice-info is-dismissible">';
        echo '<p>Benvenuto nel backend di WordPress! Sei loggato come ' . ucfirst($user->roles[0]) . '. ';
        echo 'Puoi accedere alle funzionalità di RistoPOS dal menu laterale.</p>';
        echo '</div>';
    }
}
add_action('admin_notices', 'ristopos_welcome_message');

remove_action('admin_init', 'ristopos_check_admin_access');
remove_action('current_screen', 'ristopos_redirect_custom_roles');

/* ---------------- FUNZIONI PER GESTIRE CREAZIONE ORDINE SU APP MOBILE -------------------------*/

add_action('rest_api_init', function () {
    register_rest_route('ristopos/v1', '/tables', array(
        'methods' => 'GET',
        'callback' => function() {
            $tables = get_option('ristopos_tables', array());
            error_log('Fetching tables: ' . print_r($tables, true));
            return new WP_REST_Response($tables, 200);
        },
        'permission_callback' => '__return_true'
    ));
});

// Register REST API endpoint for updating table after order
add_action('rest_api_init', function () {
    register_rest_route('ristopos/v1', '/update-table', array(
        'methods' => 'POST',
        'callback' => 'update_table_after_order',
        'permission_callback' => '__return_true', // Permetti l'accesso
    ));
});

function update_table_after_order($request) {
    $table_id = $request->get_param('table_id');
    $order_id = $request->get_param('order_id');
    $order_total = $request->get_param('order_total');

    if (!$table_id || !$order_id) {
        return new WP_REST_Response(array(
            'message' => 'Missing table_id or order_id',
        ), 400);
    }

    $tables = get_option('ristopos_tables', array());

    if (isset($tables[$table_id])) {
        // Aggiorna lo stato e il totale del tavolo
        $tables[$table_id]['status'] = 'occupied';
        $tables[$table_id]['total'] += $order_total;
        $tables[$table_id]['orders'][] = $order_id;
        update_option('ristopos_tables', $tables);

        return new WP_REST_Response(array(
            'message' => 'Table updated successfully',
            'table_total' => $tables[$table_id]['total']
        ), 200);
    } else {
        return new WP_REST_Response(array(
            'message' => 'Table not found',
        ), 404);
    }
}


/* ----------- ------------ ------------ ---------- ---------- ----------- ------------- ----------------- */

function ristopos_customize_admin_for_custom_roles() {
    $user = wp_get_current_user();
    $allowed_roles = array('waiter', 'chef', 'restaurant_manager');
    
    if (array_intersect($allowed_roles, $user->roles)) {
        // Rimuovi tutti i menu predefiniti
        remove_menu_page('index.php');                  // Dashboard
        remove_menu_page('edit.php');                   // Posts
        remove_menu_page('upload.php');                 // Media
        remove_menu_page('edit.php?post_type=page');    // Pages
        remove_menu_page('edit-comments.php');          // Comments
        remove_menu_page('themes.php');                 // Appearance
        remove_menu_page('plugins.php');                // Plugins
        remove_menu_page('users.php');                  // Users
        remove_menu_page('tools.php');                  // Tools
        remove_menu_page('options-general.php');        // Settings
        
        // Nascondi la barra di amministrazione frontend
        add_filter('show_admin_bar', '__return_false');
        
        // Aggiungi stili CSS per nascondere elementi non necessari
        add_action('admin_head', 'ristopos_custom_admin_styles');
    }
}
add_action('admin_menu', 'ristopos_customize_admin_for_custom_roles', 999);

// Esegui questa funzione all'attivazione del plugin
register_activation_hook(__FILE__, 'ristopos_add_custom_roles_and_capabilities');

// Esegui questa funzione anche quando il plugin viene aggiornato
add_action('plugins_loaded', 'ristopos_add_custom_roles_and_capabilities');

// Aggancia l'inizializzazione del plugin all'azione 'plugins_loaded'
add_action('plugins_loaded', 'ristopos_init');