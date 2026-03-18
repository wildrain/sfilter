<?php

namespace SFilter\ProductBuilder;

class Search
{
    const META_KEY = '_sfilter_cross_references';

    public function find($query, $limit = 20)
    {
        if (empty($query) || strlen($query) < 2) {
            return [];
        }

        global $wpdb;

        $product_ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT DISTINCT post_id FROM {$wpdb->postmeta}
                WHERE meta_key = %s AND meta_value LIKE %s
                LIMIT %d",
                self::META_KEY,
                '%' . $wpdb->esc_like($query) . '%',
                $limit * 5
            )
        );

        if (empty($product_ids)) {
            return [];
        }

        $results = [];
        $query_lower = strtolower($query);

        foreach ($product_ids as $product_id) {
            $product = wc_get_product($product_id);
            if (!$product) {
                continue;
            }

            $refs = get_post_meta($product_id, self::META_KEY, true);
            if (!is_array($refs)) {
                continue;
            }

            $matches = [];
            foreach ($refs as $ref) {
                $manufacturer = isset($ref['manufacturer']) ? $ref['manufacturer'] : '';
                $codes = isset($ref['codes']) ? $ref['codes'] : [];

                if (stripos($manufacturer, $query) !== false) {
                    $matches[] = [
                        'manufacturer' => $manufacturer,
                        'codes' => $codes,
                        'match_type' => 'manufacturer'
                    ];
                    continue;
                }

                $matched_codes = [];
                foreach ($codes as $code) {
                    if (stripos($code, $query) !== false) {
                        $matched_codes[] = $code;
                    }
                }

                if (!empty($matched_codes)) {
                    $matches[] = [
                        'manufacturer' => $manufacturer,
                        'codes' => $matched_codes,
                        'match_type' => 'code'
                    ];
                }
            }

            if (!empty($matches)) {
                $results[] = [
                    'product_id' => $product_id,
                    'product_name' => $product->get_name(),
                    'product_sku' => $product->get_sku(),
                    'product_url' => get_permalink($product_id),
                    'matches' => $matches
                ];
            }

            if (count($results) >= $limit) {
                break;
            }
        }

        return $results;
    }

    public function get_product_ids($query)
    {
        $results = $this->find($query, 100);
        return array_column($results, 'product_id');
    }

    public function get_cross_refs($product_id)
    {
        $data = get_post_meta($product_id, self::META_KEY, true);
        return is_array($data) ? $data : [];
    }

    public function has_cross_refs($product_id)
    {
        $data = get_post_meta($product_id, self::META_KEY, true);
        return !empty($data) && is_array($data);
    }
}
