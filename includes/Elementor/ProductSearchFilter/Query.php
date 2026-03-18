<?php

namespace SFilter\Elementor\ProductSearchFilter;

if (!defined('ABSPATH')) {
    exit;
}

class ProductSearchFilter_Query
{
    /**
     * Build and execute WP_Query for products
     *
     * @param array $params
     * @return \WP_Query
     */
    public static function query($params = [])
    {
        $defaults = [
            'search'        => '',
            'search_type'   => '',
            'taxonomies'    => [],
            'price_min'     => '',
            'price_max'     => '',
            'orderby'       => 'date',
            'order'         => 'DESC',
            'posts_per_page' => 12,
            'paged'         => 1,
        ];

        $params = wp_parse_args($params, $defaults);

        $args = [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => intval($params['posts_per_page']),
            'paged'          => intval($params['paged']),
        ];

        // Text search / cross-reference search
        if (!empty($params['search'])) {
            $search_type = sanitize_text_field($params['search_type']);
            $search_term = sanitize_text_field($params['search']);

            if ($search_type === 'part') {
                if (!isset($args['meta_query'])) {
                    $args['meta_query'] = [];
                }
                $args['meta_query'][] = [
                    'key'     => '_sfilter_cross_references',
                    'value'   => $search_term,
                    'compare' => 'LIKE',
                ];
            } elseif ($search_type === 'multipart') {
                $terms = array_map('trim', explode(',', $search_term));
                $terms = array_filter($terms);

                if (!empty($terms)) {
                    if (!isset($args['meta_query'])) {
                        $args['meta_query'] = [];
                    }
                    $cross_ref_query = ['relation' => 'OR'];
                    foreach ($terms as $term) {
                        $cross_ref_query[] = [
                            'key'     => '_sfilter_cross_references',
                            'value'   => sanitize_text_field($term),
                            'compare' => 'LIKE',
                        ];
                    }
                    $args['meta_query'][] = $cross_ref_query;
                }
            } else {
                $args['s'] = $search_term;
            }
        }

        // Taxonomy filters
        if (!empty($params['taxonomies']) && is_array($params['taxonomies'])) {
            $tax_query = [];
            foreach ($params['taxonomies'] as $taxonomy => $terms) {
                if (empty($terms)) {
                    continue;
                }
                $terms = is_array($terms) ? $terms : [$terms];
                $terms = array_map('sanitize_text_field', $terms);
                $tax_query[] = [
                    'taxonomy' => sanitize_text_field($taxonomy),
                    'field'    => 'slug',
                    'terms'    => $terms,
                ];
            }
            if (!empty($tax_query)) {
                $tax_query['relation'] = 'AND';
                $args['tax_query'] = $tax_query;
            }
        }

        // Hide out of stock if WooCommerce setting is enabled
        if ('yes' === get_option('woocommerce_hide_out_of_stock_items')) {
            $args['meta_query'][] = [
                'key'     => '_stock_status',
                'value'   => 'instock',
                'compare' => '=',
            ];
        }

        // Price range filter
        if ($params['price_min'] !== '' || $params['price_max'] !== '') {
            if (!isset($args['meta_query'])) {
                $args['meta_query'] = [];
            }
            $price_query = [
                'key'     => '_price',
                'type'    => 'NUMERIC',
            ];
            if ($params['price_min'] !== '' && $params['price_max'] !== '') {
                $price_query['value']   = [floatval($params['price_min']), floatval($params['price_max'])];
                $price_query['compare'] = 'BETWEEN';
            } elseif ($params['price_min'] !== '') {
                $price_query['value']   = floatval($params['price_min']);
                $price_query['compare'] = '>=';
            } else {
                $price_query['value']   = floatval($params['price_max']);
                $price_query['compare'] = '<=';
            }
            $args['meta_query'][] = $price_query;
        }

        // Sorting
        switch ($params['orderby']) {
            case 'price_asc':
                $args['meta_key'] = '_price';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'ASC';
                break;
            case 'price_desc':
                $args['meta_key'] = '_price';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            case 'title_asc':
                $args['orderby'] = 'title';
                $args['order']   = 'ASC';
                break;
            case 'title_desc':
                $args['orderby'] = 'title';
                $args['order']   = 'DESC';
                break;
            case 'popularity':
                $args['meta_key'] = 'total_sales';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            case 'rating':
                $args['meta_key'] = '_wc_average_rating';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            case 'date':
            default:
                $args['orderby'] = 'date';
                $args['order']   = 'DESC';
                break;
        }

        return new \WP_Query($args);
    }

    /**
     * Get global min/max price from all published products
     *
     * @return array ['min' => float, 'max' => float]
     */
    public static function get_price_range()
    {
        global $wpdb;

        $min = $wpdb->get_var("
            SELECT MIN( CAST( pm.meta_value AS DECIMAL(10,2) ) )
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = '_price'
            AND p.post_type = 'product'
            AND p.post_status = 'publish'
            AND pm.meta_value != ''
        ");

        $max = $wpdb->get_var("
            SELECT MAX( CAST( pm.meta_value AS DECIMAL(10,2) ) )
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = '_price'
            AND p.post_type = 'product'
            AND p.post_status = 'publish'
            AND pm.meta_value != ''
        ");

        return [
            'min' => floatval($min),
            'max' => floatval($max),
        ];
    }

    /**
     * Get registered product taxonomies with their terms
     *
     * @return array
     */
    public static function get_product_taxonomies()
    {
        $taxonomies = get_object_taxonomies('product', 'objects');
        $result = [];

        foreach ($taxonomies as $taxonomy) {
            if (!$taxonomy->public || $taxonomy->name === 'product_type' || $taxonomy->name === 'product_visibility') {
                continue;
            }

            $terms = get_terms([
                'taxonomy'   => $taxonomy->name,
                'hide_empty' => true,
            ]);

            if (is_wp_error($terms) || empty($terms)) {
                continue;
            }

            $result[$taxonomy->name] = [
                'label' => $taxonomy->label,
                'terms' => $terms,
            ];
        }

        return $result;
    }
}
