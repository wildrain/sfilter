<?php

namespace SFilter\ProductBuilder;

class Metabox
{
    const META_KEY = '_sfilter_cross_references';

    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'register']);
        add_action('save_post_product', [$this, 'save'], 10, 2);
    }

    public function register()
    {
        add_meta_box(
            'sfilter_cross_refs',
            __('Cross References', 'sfilter'),
            [$this, 'render'],
            'product',
            'normal',
            'default'
        );
    }

    public function render($post)
    {
        $data = get_post_meta($post->ID, self::META_KEY, true);
        if (!is_array($data)) {
            $data = [];
        }
        wp_nonce_field('sfilter_cross_refs_save', 'sfilter_cross_refs_nonce');
        include __DIR__ . '/views/metabox.php';
    }

    public function save($post_id, $post)
    {
        if (!isset($_POST['sfilter_cross_refs_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['sfilter_cross_refs_nonce'], 'sfilter_cross_refs_save')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $raw_data = isset($_POST['sf_cross_refs']) ? $_POST['sf_cross_refs'] : [];
        $clean_data = [];

        foreach ($raw_data as $row) {
            $manufacturer = isset($row['manufacturer']) ? sanitize_text_field($row['manufacturer']) : '';
            $codes_str = isset($row['codes']) ? sanitize_text_field($row['codes']) : '';

            if (empty($manufacturer) && empty($codes_str)) {
                continue;
            }

            $codes = array_map('trim', explode(',', $codes_str));
            $codes = array_filter($codes);
            $codes = array_values($codes);

            if (!empty($manufacturer) || !empty($codes)) {
                $clean_data[] = [
                    'manufacturer' => $manufacturer,
                    'codes' => $codes,
                ];
            }
        }

        if (!empty($clean_data)) {
            update_post_meta($post_id, self::META_KEY, $clean_data);
        } else {
            delete_post_meta($post_id, self::META_KEY);
        }
    }

    public static function get_cross_refs($product_id)
    {
        $data = get_post_meta($product_id, self::META_KEY, true);
        return is_array($data) ? $data : [];
    }
}
