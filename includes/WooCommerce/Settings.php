<?php

namespace SFilter\WooCommerce;

use SFilter\Admin\TestImport;

class Settings extends \WC_Settings_Page
{
    private $importer;

    public function __construct()
    {
        $this->id = 'sfilter';
        $this->label = __('SFilter', 'sfilter');

        add_action('woocommerce_settings_' . $this->id, [$this, 'output']);
        add_action('woocommerce_settings_save_' . $this->id, [$this, 'save']);
        add_action('woocommerce_sections_' . $this->id, [$this, 'output_sections']);

        parent::__construct();
    }

    public function get_sections()
    {
        $sections = [
            ''       => __('General', 'sfilter'),
            'import' => __('Import', 'sfilter'),
        ];

        return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
    }

    public function get_settings($current_section = '')
    {
        $settings = [];

        if ($current_section === '') {
            $settings = [
                [
                    'title' => __('SFilter General Settings', 'sfilter'),
                    'type'  => 'title',
                    'desc'  => __('General settings for SFilter plugin.', 'sfilter'),
                    'id'    => 'sfilter_general_options',
                ],
                [
                    'type' => 'sectionend',
                    'id'   => 'sfilter_general_options',
                ],
            ];
        }

        return apply_filters('woocommerce_get_settings_' . $this->id, $settings, $current_section);
    }

    public function output()
    {
        global $current_section;

        if ($current_section === 'import') {
            $this->output_import_section();
        } else {
            $settings = $this->get_settings($current_section);
            \WC_Admin_Settings::output_fields($settings);
        }
    }

    public function save()
    {
        global $current_section;

        if ($current_section === 'import') {
            // Import section handles its own saving via POST
            return;
        }

        $settings = $this->get_settings($current_section);
        \WC_Admin_Settings::save_fields($settings);
    }

    private function output_import_section()
    {
        $this->importer = new TestImport();
        $this->importer->process_submissions_external();

        $messages = $this->importer->get_messages();
        $errors = $this->importer->get_errors();

        include __DIR__ . '/views/import.php';
    }
}
