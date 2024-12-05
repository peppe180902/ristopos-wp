<?php

class RistoPOS {

    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        if (defined('RISTOPOS_VERSION')) {
            $this->version = RISTOPOS_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'ristopos';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        require_once RISTOPOS_PLUGIN_DIR . 'includes/class-ristopos-loader.php';
        require_once RISTOPOS_PLUGIN_DIR . 'includes/class-ristopos-admin.php';

        $this->loader = new RistoPOS_Loader();
    }

    private function define_admin_hooks() {
        $plugin_admin = new RistoPOS_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    private function define_public_hooks() {
        // Qui aggiungeremo i ganci per la parte pubblica del sito, se necessario
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }
}
