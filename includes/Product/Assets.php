<?php

namespace SFilter\Product;

class Assets
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue()
    {
        if (is_product()) {
            wp_enqueue_style(
                'sfilter-product',
                plugins_url('assets/css/frontend.css', __FILE__),
                [],
                SFILTER_VERSION
            );
        }
    }
}
