<?php

/**
 * Part Search Widget Assets Handler.
 *
 * @package SFilter\Elementor\PartSearch
 */

namespace SFilter\Elementor\PartSearch;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Assets class for Part Search widget
 *
 * Handles asset registration for the Part Search Elementor widget.
 * This class provides self-contained asset paths relative to the module directory.
 *
 * @since 1.0.0
 */
class Assets
{
    /**
     * Module directory path.
     *
     * @var string
     */
    private static $module_path;

    /**
     * Module directory URL.
     *
     * @var string
     */
    private static $module_url;

    /**
     * Initialize the assets handler.
     *
     * @since 1.0.0
     * @return void
     */
    public static function init()
    {
        self::$module_path = dirname(__FILE__);
        self::$module_url = self::get_module_url();
    }

    /**
     * Get the module URL.
     *
     * @since 1.0.0
     * @return string
     */
    private static function get_module_url()
    {
        $plugin_path = SFILTER_PATH;
        $module_path = dirname(__FILE__);

        $relative_path = str_replace($plugin_path, '', $module_path);
        $relative_path = ltrim($relative_path, '/\\');

        return SFILTER_URL . '/' . $relative_path;
    }

    /**
     * Register widget assets.
     *
     * @since 1.0.0
     * @return void
     */
    public static function register()
    {
        self::init();

        $scripts = self::get_scripts();
        $styles = self::get_styles();

        foreach ($scripts as $handle => $script) {
            $deps = isset($script['deps']) ? $script['deps'] : [];
            $version = isset($script['version']) ? $script['version'] : SFILTER_VERSION;
            wp_register_script($handle, $script['src'], $deps, $version, true);
        }

        foreach ($styles as $handle => $style) {
            $deps = isset($style['deps']) ? $style['deps'] : [];
            $version = isset($style['version']) ? $style['version'] : SFILTER_VERSION;
            wp_register_style($handle, $style['src'], $deps, $version);
        }
    }

    /**
     * Get widget scripts.
     *
     * @since 1.0.0
     * @return array
     */
    public static function get_scripts()
    {
        self::init();

        return [
            'sfilter-part-search' => [
                'src'     => self::$module_url . '/assets/js/part-search.js',
                'version' => filemtime(self::$module_path . '/assets/js/part-search.js'),
                'deps'    => ['jquery']
            ],
        ];
    }

    /**
     * Get widget styles.
     *
     * @since 1.0.0
     * @return array
     */
    public static function get_styles()
    {
        self::init();

        return [
            'sfilter-part-search' => [
                'src'     => self::$module_url . '/assets/css/part-search.css',
                'version' => filemtime(self::$module_path . '/assets/css/part-search.css'),
            ],
        ];
    }
}
