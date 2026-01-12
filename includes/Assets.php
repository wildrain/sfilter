<?php

namespace SFilter;

/**
 * Assets class handler
 */
class Assets
{
    /**
     * Initialize assets
     */
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
    }

    /**
     * SFilter scripts
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
     * SFilter styles
     *
     * @return array
     */
    public function get_styles()
    {
        return [
            'sfilter' => [
                'src'     => SFILTER_ASSETS . '/css/sfilter.css',
                'version' => filemtime(SFILTER_PATH . '/assets/css/sfilter.css'),
            ]
        ];
    }

    /**
     * Register assets
     */
    public function register_assets()
    {
        $scripts = $this->get_scripts();
        $styles = $this->get_styles();

        foreach ($scripts as $handle => $script) {
            $deps = isset($script['deps']) ? $script['deps'] : false;
            $version = isset($script['version']) ? $script['version'] : SFILTER_VERSION;

            wp_register_script($handle, $script['src'], $deps, $version, true);
        }

        foreach ($styles as $handle => $style) {
            $deps = isset($style['deps']) ? $style['deps'] : false;
            $version = isset($style['version']) ? $style['version'] : SFILTER_VERSION;

            wp_register_style($handle, $style['src'], $deps, $version);
        }
    }
}
