<?php

namespace SFilter;

use Elementor\Plugin;
use SFilter\Elementor\Widgets\PartSearch\Assets as PartSearchAssets;
use SFilter\Elementor\Widgets\PartSearch\PartSearch;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Load elementor class
 */
class Load_Elementor
{
    /**
     * Init elementor class
     *
     * @since 1.0.0
     * @return null
     */
    public function __construct()
    {
        add_action('elementor/elements/categories_registered', [$this, 'register_category']);
        add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets']);
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'custom_elementor_scripts']);
    }


    /**
     * custom_elementor_scripts
     *
     * @since 1.0.0
     */
    public function custom_elementor_scripts()
    {
        $scripts     = $this->get_scripts();

        foreach ($scripts as $handle => $script) {
            $deps    = isset($script['deps']) ? $script['deps'] : false;
            $version = isset($script['version']) ? $script['version'] : SFILTER_VERSION;
            wp_register_script($handle, $script['src'], $deps, $version, true);
            wp_enqueue_script($handle);
        }
    }

    /**
     * Register elementor category
     *
     * @param object $elementor
     *
     * @since 1.0.0
     * @return object
     */
    public function register_category($elementor)
    {
        $elementor->add_category(
            'sfilter-widgets',
            [
                'title' =>  __('SFilter Widgets', 'sfilter'),
                'icon'  => 'eicon-font',
            ]
        );

        return $elementor;
    }

    /**
     * Register elementor widgets
     *
     * @since 1.0.0
     * @return void
     */
    public function register_widgets()
    {
        $this->includeWidgetsFiles();

        // Register legacy widgets from includes/Elementor/
        Plugin::instance()->widgets_manager->register(new Elementor\Hello_World());

        // Register modular widgets
        Plugin::instance()->widgets_manager->register(new PartSearch());
    }

    /**
     * Widget Scripts
     *
     * @since 1.0.0
     * @return array
     */
    public static function getWidgetScript()
    {
        return [];
    }

    /**
     * SFilter scripts (centralized scripts only)
     *
     * @return array
     */
    public function get_scripts()
    {
        return [
            'sfilter' => [
                'src'     => SFILTER_ASSETS . '/js/sfilter.js',
                'version' => filemtime(SFILTER_PATH . '/assets/js/sfilter.js'),
                'deps'    => ['jquery']
            ],
        ];
    }

    /**
     * SFilter Elementor styles (centralized styles only)
     *
     * @since 1.0.0
     * @return array
     */
    public function getStyles()
    {
        return [
            'sfilter' => [
                'src'     => SFILTER_ASSETS . '/css/sfilter.css',
                'version' => filemtime(SFILTER_PATH . '/assets/css/sfilter.css'),
            ],
        ];
    }

    /**
     * Legacy widget list (widgets in includes/Elementor/)
     *
     * @since 1.0.0
     * @return array
     */
    public static function getWidgetList()
    {
        return [
            'Hello_World',
        ];
    }

    /**
     * Widget files
     *
     * @since 1.0.0
     * @return void
     */
    public function includeWidgetsFiles()
    {
        $scripts     = $this->get_scripts();
        $styles      = $this->getStyles();
        $widget_list = $this->getWidgetList();

        // Load legacy widgets from includes/Elementor/
        foreach ($widget_list as $handle => $widget) {
            $file = SFILTER_ELEMENTOR . $widget . '.php';
            if (!file_exists($file)) {
                continue;
            }
            require_once $file;
        }

        // Register centralized scripts
        foreach ($scripts as $handle => $script) {
            $deps    = isset($script['deps']) ? $script['deps'] : false;
            $version = isset($script['version']) ? $script['version'] : SFILTER_VERSION;
            wp_register_script($handle, $script['src'], $deps, $version, true);
        }

        // Register centralized styles
        foreach ($styles as $handle => $style) {
            $deps = isset($style['deps']) ? $style['deps'] : false;
            $version = isset($style['version']) ? $style['version'] : SFILTER_VERSION;
            wp_register_style($handle, $style['src'], $deps, $version);
        }

        // Register modular widget assets
        PartSearchAssets::register();
    }
}
