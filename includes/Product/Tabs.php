<?php

namespace SFilter\Product;

use SFilter\Generator;
use SFilter\ProductBuilder\Metabox;

class Tabs
{
    private $generator;

    public function __construct()
    {
        $this->generator = new Generator();
        add_filter('woocommerce_product_tabs', [$this, 'add_tabs']);
    }

    public function add_tabs($tabs)
    {
        unset($tabs['reviews']);

        $tabs['sf_specs'] = [
            'title'    => __('Specs', 'sfilter'),
            'priority' => 15,
            'callback' => [$this, 'render_specs_tab'],
        ];

        $tabs['sf_cross_reference'] = [
            'title'    => __('Cross Reference', 'sfilter'),
            'priority' => 20,
            'callback' => [$this, 'render_cross_reference_tab'],
        ];

        $tabs['sf_application'] = [
            'title'    => __('Application', 'sfilter'),
            'priority' => 25,
            'callback' => [$this, 'render_application_tab'],
        ];

        return $tabs;
    }

    public function render_specs_tab()
    {
        global $product;
        $product_id = $product->get_id();
        $taxonomies = $this->generator->get_taxonomies();

        include __DIR__ . '/views/tab-specs.php';
    }

    public function render_cross_reference_tab()
    {
        global $product;
        $product_id = $product->get_id();
        $cross_refs = Metabox::get_cross_refs($product_id);

        include __DIR__ . '/views/tab-cross-reference.php';
    }

    public function render_application_tab()
    {
        include __DIR__ . '/views/tab-application.php';
    }
}
