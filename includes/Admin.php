<?php

namespace SFilter;

class Admin
{
    /**
     * Class initialize
     */
    function __construct()
    {
        new Admin\Menu();
        new Admin\Handler();

        add_filter('woocommerce_get_settings_pages', [$this, 'add_woocommerce_settings']);
    }

    /**
     * Add SFilter settings tab to WooCommerce settings
     *
     * @param array $settings
     * @return array
     */
    public function add_woocommerce_settings($settings)
    {
        $settings[] = new WooCommerce\Settings();
        return $settings;
    }
}
