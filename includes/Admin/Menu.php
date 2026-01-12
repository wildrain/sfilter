<?php

namespace SFilter\Admin;

/**
 * Admin menu class
 */
class Menu
{
    /**
     * Initialize menu
     */
    function __construct()
    {
        add_action('admin_menu', [$this, 'admin_menu']);
    }

    /**
     * Handle plugin menu
     *
     * @return void
     */
    public function admin_menu()
    {
        $parent_slug = 'sfilter-dashboard';
        $capability = 'manage_options';

        add_menu_page(__('SFilter Dashboard', 'sfilter'), __('SFilter', 'sfilter'), $capability, $parent_slug, [$this, 'dashboard_page'], 'dashicons-buddicons-groups');
        add_submenu_page($parent_slug, __('Settings', 'sfilter'), __('Settings', 'sfilter'), $capability, $parent_slug, [$this, 'dashboard_page']);
        add_submenu_page($parent_slug, __('Report', 'sfilter'), __('Report', 'sfilter'), $capability, 'sfilter-report', [$this, 'report_page']);
        add_submenu_page($parent_slug, __('Test Import', 'sfilter'), __('Test Import', 'sfilter'), $capability, 'sfilter-test-import', [$this, 'test_import_page']);
    }

    /**
     * Handle menu page
     *
     * @return void
     */
    public function dashboard_page()
    {
        $settings = new Settings();
        $settings->settings_page();
    }

    /**
     * SFilter report page
     *
     * @return void
     */
    public function report_page()
    {
        $settings = new Settings();
        $settings->report_page();
    }

    /**
     * SFilter test import page
     *
     * @return void
     */
    public function test_import_page()
    {
        $test_import = new TestImport();
        $test_import->handle_import();
    }
}
