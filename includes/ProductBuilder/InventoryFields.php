<?php

namespace SFilter\ProductBuilder;

class InventoryFields
{
    const OEM_META_KEY = '_oem';

    public function __construct()
    {
        add_action('woocommerce_product_options_inventory_product_data', [$this, 'add_oem_field']);
        add_action('woocommerce_process_product_meta', [$this, 'save_oem_field']);
    }

    public function add_oem_field()
    {
        woocommerce_wp_text_input([
            'id'          => self::OEM_META_KEY,
            'label'       => __('OEM', 'sfilter'),
            'desc_tip'    => true,
            'description' => __('OEM part number', 'sfilter'),
        ]);
    }

    public function save_oem_field($post_id)
    {
        $oem = isset($_POST[self::OEM_META_KEY]) ? sanitize_text_field($_POST[self::OEM_META_KEY]) : '';
        update_post_meta($post_id, self::OEM_META_KEY, $oem);
    }

    public static function get_oem($product_id)
    {
        return get_post_meta($product_id, self::OEM_META_KEY, true);
    }
}
