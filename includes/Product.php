<?php

namespace SFilter;

class Product
{
    public function __construct()
    {
        if (!is_admin()) {
            new Product\Tabs();
            add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        }
    }

    public function enqueue_assets()
    {
        if (is_product()) {
            wp_enqueue_style(
                'sfilter-product',
                SFILTER_ASSETS . '/Product/css/frontend.css',
                [],
                SFILTER_VERSION
            );
        }
    }
}
