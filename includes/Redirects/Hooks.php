<?php

namespace Squartup\RistoPos\Redirects;


class Hooks
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        add_action('current_screen', [$this, 'redirectCustomRoles']);
        add_action('admin_init', [$this, 'checkAdminAccess']);
        remove_action('current_screen', [$this, 'redirectCustomRoles']);
    }

    public function redirectCustomRoles()
    {
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

    public function checkAdminAccess()
    {
        $user = wp_get_current_user();
        $allowed_roles = array('administrator', 'restaurant_manager', 'chef', 'waiter');

        if (!array_intersect($allowed_roles, $user->roles)) {
            wp_redirect(home_url());
            exit;
        }
    }
}