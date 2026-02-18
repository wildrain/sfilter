<?php

namespace SFilter\Elementor\ProductSearchFilter;

if (!defined('ABSPATH')) {
    exit;
}

class ProductSearchFilter_Ajax
{
    public function __construct()
    {
        add_action('wp_ajax_sf_product_search', [$this, 'handle_search']);
        add_action('wp_ajax_nopriv_sf_product_search', [$this, 'handle_search']);
    }

    public function handle_search()
    {
        check_ajax_referer('sf_product_search', 'nonce');

        $params = [
            'search'         => isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '',
            'search_type'    => isset($_POST['search_type']) ? sanitize_text_field($_POST['search_type']) : '',
            'taxonomies'     => isset($_POST['taxonomies']) ? $this->sanitize_taxonomies($_POST['taxonomies']) : [],
            'price_min'      => isset($_POST['price_min']) ? sanitize_text_field($_POST['price_min']) : '',
            'price_max'      => isset($_POST['price_max']) ? sanitize_text_field($_POST['price_max']) : '',
            'orderby'        => isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : 'date',
            'posts_per_page' => isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 12,
            'paged'          => isset($_POST['paged']) ? intval($_POST['paged']) : 1,
        ];

        $settings = isset($_POST['settings']) ? $this->sanitize_settings($_POST['settings']) : [];
        $view     = isset($_POST['view']) ? sanitize_text_field($_POST['view']) : 'grid';

        $query = ProductSearchFilter_Query::query($params);

        ob_start();
        if ($query->have_posts()) {
            $template = ($view === 'list') ? 'product-list.php' : 'product-grid.php';
            include __DIR__ . '/templates/' . $template;
        } else {
            include __DIR__ . '/templates/no-results.php';
        }
        $html = ob_get_clean();

        ob_start();
        $pagination_type = isset($settings['pagination_type']) ? $settings['pagination_type'] : 'numbered';
        $load_more_text  = isset($settings['load_more_text']) ? $settings['load_more_text'] : __('Load More', 'sfilter');
        $paged           = $params['paged'];
        $max_pages       = $query->max_num_pages;
        include __DIR__ . '/templates/pagination.php';
        $pagination_html = ob_get_clean();

        wp_send_json_success([
            'html'        => $html,
            'pagination'  => $pagination_html,
            'found_posts' => $query->found_posts,
            'max_pages'   => $query->max_num_pages,
        ]);
    }

    private function sanitize_taxonomies($taxonomies)
    {
        if (!is_array($taxonomies)) {
            return [];
        }

        $clean = [];
        foreach ($taxonomies as $taxonomy => $terms) {
            $taxonomy = sanitize_text_field($taxonomy);
            if (is_array($terms)) {
                $clean[$taxonomy] = array_map('sanitize_text_field', $terms);
            } else {
                $clean[$taxonomy] = sanitize_text_field($terms);
            }
        }

        return $clean;
    }

    private function sanitize_settings($settings)
    {
        if (!is_array($settings)) {
            return [];
        }

        $clean = [];
        foreach ($settings as $key => $value) {
            $clean[sanitize_text_field($key)] = sanitize_text_field($value);
        }

        return $clean;
    }
}
