<?php

namespace SFilter\ProductBuilder;

class ApplicationMetabox
{
    const META_KEY = '_sfilter_applications';

    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'register']);
        add_action('save_post_product', [$this, 'save'], 10, 2);
    }

    public function register()
    {
        add_meta_box(
            'sfilter_applications',
            __('Applications', 'sfilter'),
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
        wp_nonce_field('sfilter_applications_save', 'sfilter_applications_nonce');
        include __DIR__ . '/views/application-metabox.php';
    }

    public function save($post_id, $post)
    {
        if (!isset($_POST['sfilter_applications_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['sfilter_applications_nonce'], 'sfilter_applications_save')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $raw_data = isset($_POST['sf_applications']) ? $_POST['sf_applications'] : [];
        $clean_data = [];

        foreach ($raw_data as $row) {
            $make = isset($row['make']) ? sanitize_text_field($row['make']) : '';
            $model = isset($row['model']) ? sanitize_text_field($row['model']) : '';
            $year_from = isset($row['year_from']) ? absint($row['year_from']) : 0;
            $year_to = isset($row['year_to']) ? absint($row['year_to']) : 0;

            if (empty($make) && empty($model) && empty($year_from) && empty($year_to)) {
                continue;
            }

            $clean_data[] = [
                'make' => $make,
                'model' => $model,
                'year_from' => $year_from,
                'year_to' => $year_to,
            ];
        }

        if (!empty($clean_data)) {
            update_post_meta($post_id, self::META_KEY, $clean_data);
        } else {
            delete_post_meta($post_id, self::META_KEY);
        }
    }

    public static function get_applications($product_id)
    {
        $data = get_post_meta($product_id, self::META_KEY, true);
        return is_array($data) ? $data : [];
    }
}
