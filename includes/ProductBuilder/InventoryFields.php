<?php

namespace SFilter\ProductBuilder;

class InventoryFields
{
    const OEM_META_KEY = 'oem';

    public function __construct()
    {
        add_action('woocommerce_product_options_inventory_product_data', [$this, 'add_oem_field']);
        add_action('woocommerce_process_product_meta', [$this, 'save_oem_field']);
        add_filter('woocommerce_csv_product_import_reserved_fields_pair', [$this, 'add_csv_reserved_fields']);
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

    public function add_csv_reserved_fields($fields)
    {
        $fields['meta:' . self::OEM_META_KEY] = [
            'title'       => 'meta:' . self::OEM_META_KEY,
            'description' => __('OEM part number', 'sfilter'),
        ];
        return $fields;
    }
}
