<?php

namespace Squartup\RistoPos\Rest;

/**
 * Rest Manager class.
 */
class Manager
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    public function registerRoutes()
    {
        register_rest_route('ristopos/v1', '/tables', array(
            'methods' => 'GET',
            'callback' => function () {
                $tables = get_option('ristopos_tables', array());
                error_log('Fetching tables: ' . print_r($tables, true));
                return new WP_REST_Response($tables, 200);
            },
            'permission_callback' => '__return_true'
        ));

        register_rest_route('ristopos/v1', '/update-table', array(
            'methods' => 'POST',
            'callback' => 'update_table_after_order',
            'permission_callback' => '__return_true', // Permetti l'accesso
        ));
    }
}