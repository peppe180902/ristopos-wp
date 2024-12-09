<?php

class RistoPOS_Admin {

    public function __construct() {
        // add_action('admin_menu', array($this, 'add_plugin_menu'));
        // add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
    }

    // public function add_plugin_menu() {
    //     add_menu_page(
    //         __('RistoPOS', 'ristopos'),
    //         __('RistoPOS', 'ristopos'),
    //         'manage_options',
    //         'ristopos',
    //         array($this, 'display_plugin_page'),
    //         'dashicons-food',
    //         56
    //     );
    // }

    // public function display_plugin_page() {
    //     require_once RISTOPOS_PLUGIN_DIR . 'admin/partials/ristopos-admin-display.php';
    // }

    // public function enqueue_admin_styles() {
    //     wp_enqueue_style('ristopos-admin-style', RISTOPOS_PLUGIN_URL . 'admin/css/ristopos-admin.css', array(), RISTOPOS_VERSION, 'all');
    // }
}
