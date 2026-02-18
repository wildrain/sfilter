<?php

namespace SFilter;

use Elementor\Plugin;
use SFilter\Elementor\PartSearch\Assets as PartSearchAssets;
use SFilter\Elementor\PartSearch\PartSearch;
use SFilter\Elementor\ProductSearchFilter\ProductSearchFilter;
use SFilter\Elementor\ProductSearchFilter\ProductSearchFilter_Ajax;

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

        new ProductSearchFilter_Ajax();
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

        Plugin::instance()->widgets_manager->register(new PartSearch());
        Plugin::instance()->widgets_manager->register(new ProductSearchFilter());
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
     * Widget files
     *
     * @since 1.0.0
     * @return void
     */
    public function includeWidgetsFiles()
    {
        $scripts     = $this->get_scripts();
        $styles      = $this->getStyles();

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
