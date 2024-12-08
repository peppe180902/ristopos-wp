<?php

namespace Squartup\RistoPos\Admin;

/**
 * Admin Menu class.
 *
 * Responsible for managing admin menus.
 */
class Menu
{
    /**
     * Constructor.
     *
     * @since 0.1.0
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'init']);
        add_action('admin_init', [$this, 'HideAminBar']);
    }

    /**
     * Init Menu.
     *
     * @since 0.1.0
     *
     * @return void
     */
    public function init()
    {
        global $submenu;

        $slug = RISTOPOS_SLUG;
        $menu_position = 50;
        $capability = 'manage_options';
        $logo_icon = 'dashicons-food'; //RISTOPOS_ASSETS . '/images/logo.webp';

        add_menu_page(esc_attr__('RistoPOS', 'ristopos'), esc_attr__('RistoPOS', 'ristopos'), $capability, $slug, [$this, 'plugin_page'], $logo_icon, $menu_position);

        if (current_user_can($capability)) {
            $submenu[$slug][] = [esc_attr__('Home', 'ristopos'), $capability, 'admin.php?page=' . $slug . '#/']; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
            // $submenu[$slug][] = [esc_attr__('Jobs', 'ristopos'), $capability, 'admin.php?page=' . $slug . '#/jobs']; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
        }

        // add_menu_page(
        //     'RistoPOS',
        //     'RistoPOS',
        //     'ristopos_access',
        //     'ristopos',
        //     'ristopos_admin_page',
        //     'dashicons-food',
        //     56
        // );

        add_submenu_page(
            'ristopos',
            'POS',
            'POS',
            'take_orders',
            'ristopos-products',
            [$this, 'ristopos_products_page']
        );

        add_submenu_page(
            'ristopos',
            'Ordini',
            'Ordini',
            'view_orders',
            'ristopos-orders',
            [$this, 'ristopos_orders_page']
        );

        add_submenu_page(
            'ristopos',
            'Gestione Tavoli',
            'Gestione Tavoli',
            'take_orders',
            'ristopos-tables',
            [$this, 'ristopos_tables_page']
        );

        add_submenu_page(
            'ristopos',
            'Gestione Prodotti',
            'Gestione Prodotti',
            'manage_menu',
            'ristopos-product-management',
            [$this, 'ristopos_product_management_page']
        );

        add_submenu_page(
            'ristopos',
            'Dashboard Analitica',
            'Dashboard Analitica',
            'view_reports',
            'ristopos-analytics',
            [$this, 'getAnalyticsPage']
        );

        add_submenu_page(
            'ristopos',
            'Gestione del Personale',
            'Gestione del Personale',
            'manage_staff',
            'ristopos-staff',
            [$this, 'getStaffManagementPage']
        );
    }


    public function HideAminBar()
    {
        if (isset($_GET['page']) && ($_GET['page'] === 'ristopos' || $_GET['page'] === 'ristopos-orders' || $_GET['page'] === 'ristopos-products' || $_GET['page'] === 'ristopos-tables' || $_GET['page'] === 'ristopos-product-management' || $_GET['page'] === 'ristopos-analytics' || $_GET['page'] === 'ristopos-staff')) {
            add_filter('show_admin_bar', '__return_false');
            add_action('admin_head', 'ristopos_custom_admin_styles');
        }
    }

    /**
     * Render the plugin page.
     *
     * @since 0.1.0
     *
     * @return void
     */
    public function plugin_page()
    {
        require_once RISTOPOS_TEMPLATE_PATH . '/app.php';
    }


    public function getStaffManagementPage()
    {
        if (!current_user_can('manage_staff')) {
            wp_die(__('Non hai il permesso di accedere a questa pagina.', 'ristopos'));
        }

        do_action('ristopos_before_page_content');

        echo '<div class="wrap">';
        echo '<h1>Gestione del Personale RistoPOS</h1>';

        // Gestione delle azioni
        if (isset($_POST['ristopos_add_staff'])) {
            ristopos_process_add_staff();
        } elseif (isset($_POST['ristopos_edit_staff'])) {
            ristopos_process_edit_staff();
        } elseif (isset($_GET['action']) && $_GET['action'] == 'delete') {
            ristopos_process_delete_staff();
        }

        // Visualizza il form di aggiunta/modifica staff
        ristopos_display_staff_form();

        // Visualizza la lista del personale
        ristopos_display_staff_list();

        echo '</div>';
    }

    public function getAnalyticsPage()
    {
        do_action('ristopos_before_page_content');

        if (!ristopos_wc_functions_available()) {
            echo '<div class="wrap"><h1>Dashboard Analitica RistoPOS</h1>';
            echo '<div class="error"><p>WooCommerce non è completamente caricato. La Dashboard Analitica richiede WooCommerce per funzionare correttamente.</p></div>';
            echo '</div>';
            return;
        }

        echo '<div class="wrap">';
        echo '<h1>Dashboard Analitica RistoPOS</h1>';

        ristopos_display_main_stats();
        ristopos_display_charts();

        echo '</div>';
    }


    public function ristopos_products_page()
    {
        require_once RISTOPOS_TEMPLATE_PATH . '/admin/products.php';
    }

    public function ristopos_orders_page()
    {
        require_once RISTOPOS_TEMPLATE_PATH . '/admin/orders.php';
    }

    public function ristopos_tables_page()
    {
        require_once RISTOPOS_TEMPLATE_PATH . '/admin/tables.php';
    }

    public function ristopos_product_management_page()
    {
        require_once RISTOPOS_TEMPLATE_PATH . '/admin/product-management.php';
    }
}
