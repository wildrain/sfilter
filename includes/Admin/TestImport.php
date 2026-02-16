<?php

namespace SFilter\Admin;

use SFilter\ProductBuilder\Metabox;
use SFilter\ProductBuilder\ApplicationMetabox;

class TestImport
{
    private $messages = [];
    private $errors = [];

    public function handle_import()
    {
        $this->process_submissions();
        include __DIR__ . '/views/import.php';
    }

    /**
     * Process submissions externally (for WooCommerce Settings integration)
     */
    public function process_submissions_external()
    {
        $this->process_submissions();
    }

    private function process_submissions()
    {
        if (!isset($_POST['sf_import_nonce']) || !wp_verify_nonce($_POST['sf_import_nonce'], 'sf_import_action')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        // Handle Cross Reference Import
        if (isset($_POST['import_type']) && $_POST['import_type'] === 'cross_reference') {
            if (!empty($_FILES['import_file']['tmp_name'])) {
                $this->process_cross_reference_csv($_FILES['import_file']['tmp_name']);
            } else {
                $this->errors[] = __('Please select a CSV file to import.', 'sfilter');
            }
        }

        // Handle Application Import
        if (isset($_POST['import_type']) && $_POST['import_type'] === 'application') {
            if (!empty($_FILES['import_file']['tmp_name'])) {
                $this->process_application_csv($_FILES['import_file']['tmp_name']);
            } else {
                $this->errors[] = __('Please select a CSV file to import.', 'sfilter');
            }
        }
    }

    private function process_cross_reference_csv($file_path)
    {
        $handle = fopen($file_path, 'r');
        if ($handle === false) {
            $this->errors[] = __('Could not open the CSV file.', 'sfilter');
            return;
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            $this->errors[] = __('Could not read CSV header.', 'sfilter');
            fclose($handle);
            return;
        }

        // Normalize header
        $header = array_map('strtolower', array_map('trim', $header));

        // Validate required columns
        $required = ['sku', 'manufacturer', 'codes'];
        foreach ($required as $col) {
            if (!in_array($col, $header)) {
                $this->errors[] = sprintf(__('Missing required column: %s', 'sfilter'), $col);
                fclose($handle);
                return;
            }
        }

        $sku_col = array_search('sku', $header);
        $manufacturer_col = array_search('manufacturer', $header);
        $codes_col = array_search('codes', $header);

        // Process in batches by SKU to minimize memory usage
        $imported = 0;
        $skipped = 0;
        $current_sku = null;
        $current_entries = [];
        $current_product_id = null;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) {
                continue;
            }

            $sku = trim($row[$sku_col]);
            $manufacturer = sanitize_text_field(trim($row[$manufacturer_col]));
            $codes_str = trim($row[$codes_col]);

            if (empty($sku)) {
                continue;
            }

            // Parse codes
            $codes = array_map('trim', explode(',', $codes_str));
            $codes = array_filter($codes);
            $codes = array_values($codes);

            if (empty($manufacturer) && empty($codes)) {
                continue;
            }

            $entry = [
                'manufacturer' => $manufacturer,
                'codes' => $codes,
            ];

            // When SKU changes, flush the previous batch
            if ($current_sku !== null && $current_sku !== $sku) {
                $result = $this->save_cross_ref_batch($current_sku, $current_entries, $current_product_id);
                $imported += $result['imported'];
                $skipped += $result['skipped'];
                $current_entries = [];
                $current_product_id = null;
            }

            // Cache product ID lookup for current SKU
            if ($current_sku !== $sku) {
                $current_sku = $sku;
                $current_product_id = $this->get_product_id_by_sku($sku);
            }

            $current_entries[] = $entry;
        }

        // Save last batch
        if ($current_sku !== null && !empty($current_entries)) {
            $result = $this->save_cross_ref_batch($current_sku, $current_entries, $current_product_id);
            $imported += $result['imported'];
            $skipped += $result['skipped'];
        }

        fclose($handle);

        $this->messages[] = sprintf(
            __('Cross Reference Import Complete: %d entries imported, %d skipped (product not found).', 'sfilter'),
            $imported,
            $skipped
        );
    }

    private function save_cross_ref_batch($sku, $entries, $product_id)
    {
        if (!$product_id) {
            return ['imported' => 0, 'skipped' => count($entries)];
        }

        $existing = Metabox::get_cross_refs($product_id);
        $merged = array_merge($existing, $entries);
        update_post_meta($product_id, Metabox::META_KEY, $merged);

        return ['imported' => count($entries), 'skipped' => 0];
    }

    private function process_application_csv($file_path)
    {
        $handle = fopen($file_path, 'r');
        if ($handle === false) {
            $this->errors[] = __('Could not open the CSV file.', 'sfilter');
            return;
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            $this->errors[] = __('Could not read CSV header.', 'sfilter');
            fclose($handle);
            return;
        }

        // Normalize header
        $header = array_map('strtolower', array_map('trim', $header));

        // Validate required columns
        $required = ['sku', 'make', 'model', 'year_from', 'year_to'];
        foreach ($required as $col) {
            if (!in_array($col, $header)) {
                $this->errors[] = sprintf(__('Missing required column: %s', 'sfilter'), $col);
                fclose($handle);
                return;
            }
        }

        $sku_col = array_search('sku', $header);
        $make_col = array_search('make', $header);
        $model_col = array_search('model', $header);
        $year_from_col = array_search('year_from', $header);
        $year_to_col = array_search('year_to', $header);

        // Process in batches by SKU to minimize memory usage
        $imported = 0;
        $skipped = 0;
        $current_sku = null;
        $current_entries = [];
        $current_product_id = null;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 5) {
                continue;
            }

            $sku = trim($row[$sku_col]);
            $make = sanitize_text_field(trim($row[$make_col]));
            $model = sanitize_text_field(trim($row[$model_col]));
            $year_from = absint($row[$year_from_col]);
            $year_to = absint($row[$year_to_col]);

            if (empty($sku)) {
                continue;
            }

            if (empty($make) && empty($model) && empty($year_from) && empty($year_to)) {
                continue;
            }

            $entry = [
                'make' => $make,
                'model' => $model,
                'year_from' => $year_from,
                'year_to' => $year_to,
            ];

            // When SKU changes, flush the previous batch
            if ($current_sku !== null && $current_sku !== $sku) {
                $result = $this->save_application_batch($current_sku, $current_entries, $current_product_id);
                $imported += $result['imported'];
                $skipped += $result['skipped'];
                $current_entries = [];
                $current_product_id = null;
            }

            // Cache product ID lookup for current SKU
            if ($current_sku !== $sku) {
                $current_sku = $sku;
                $current_product_id = $this->get_product_id_by_sku($sku);
            }

            $current_entries[] = $entry;
        }

        // Save last batch
        if ($current_sku !== null && !empty($current_entries)) {
            $result = $this->save_application_batch($current_sku, $current_entries, $current_product_id);
            $imported += $result['imported'];
            $skipped += $result['skipped'];
        }

        fclose($handle);

        $this->messages[] = sprintf(
            __('Application Import Complete: %d entries imported, %d skipped (product not found).', 'sfilter'),
            $imported,
            $skipped
        );
    }

    private function save_application_batch($sku, $entries, $product_id)
    {
        if (!$product_id) {
            return ['imported' => 0, 'skipped' => count($entries)];
        }

        $existing = ApplicationMetabox::get_applications($product_id);
        $merged = array_merge($existing, $entries);
        update_post_meta($product_id, ApplicationMetabox::META_KEY, $merged);

        return ['imported' => count($entries), 'skipped' => 0];
    }

    private function get_product_id_by_sku($sku)
    {
        global $wpdb;

        $product_id = $wpdb->get_var($wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_sku' AND meta_value = %s LIMIT 1",
            $sku
        ));

        return $product_id ? (int) $product_id : false;
    }

    public function get_messages()
    {
        return $this->messages;
    }

    public function get_errors()
    {
        return $this->errors;
    }
}
