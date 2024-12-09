<?php

namespace Squartup\RistoPos;

/**
 * Asset Manager class.
 *
 * Responsible for managing all of the assets (CSS, JS, Images, Locales).
 */
class Assets
{
    /**
     * Constructor.
     *
     * @since 0.1.0
     */
    public function __construct()
    {
        add_action('init', [$this, 'registerAllScripts']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
    }

    /**
     * Register all scripts and styles.
     *
     * @since 0.1.0
     *
     * @return void
     */
    public function registerAllScripts()
    {
        $this->registerStyles($this->getStyles());
        $this->registerScripts($this->getScripts());
    }

    /**
     * Get all styles.
     *
     * @since 0.1.0
     *
     * @return array
     */
    public function getStyles(): array
    {
        return [
            'ristopos-css' => [
                'src' => RISTOPOS_BUILD . '/index.css',
                'version' => RISTOPOS_VERSION,
                'deps' => [],
            ],
        ];
    }

    /**
     * Get all scripts.
     *
     * @since 0.1.0
     *
     * @return array
     */
    public function getScripts(): array
    {
        $dependency = require_once RISTOPOS_DIR . '/build/index.asset.php';

        return [
            'ristopos-app' => [
                'src' => RISTOPOS_BUILD . '/index.js',
                'version' => $dependency['version'],
                'deps' => $dependency['dependencies'],
                'in_footer' => true,
            ],
        ];
    }

    /**
     * Register styles.
     *
     * @since 0.1.0
     *
     * @param array $styles Styles to register.
     *
     * @return void
     */
    public function registerStyles(array $styles)
    {
        foreach ($styles as $handle => $style) {
            wp_register_style($handle, $style['src'], $style['deps'], $style['version']);
        }
    }

    /**
     * Register scripts.
     *
     * @since 0.1.0
     *
     * @return void
     */
    public function registerScripts(array $scripts)
    {
        foreach ($scripts as $handle => $script) {
            wp_register_script($handle, $script['src'], $script['deps'], $script['version'], $script['in_footer']);
        }
    }

    /**
     * Enqueue admin styles and scripts.
     *
     * @since 0.1.0
     *
     * @return void
     */
    public function enqueueAdminAssets()
    {
        // Check if we are on the admin page and page=ristopos.
        if (!is_admin() || !isset($_GET['page']) || sanitize_text_field(wp_unslash($_GET['page'])) !== 'ristopos') {
            return;
        }

        wp_enqueue_style('ristopos-css');
        wp_enqueue_script('ristopos-app');
    }
}
