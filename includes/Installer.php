<?php

namespace SFilter;

class Installer
{
    /**
     * Initialize class functions
     *
     * @return void
     */
    public function run()
    {
        $this->add_version();
        $this->create_tables();
    }

    /**
     * Store plugin information
     *
     * @return void
     */
    public function add_version()
    {
        $installed = get_option('sfilter_installed');

        if (!$installed) {
            update_option('sfilter_installed', time());
        }

        update_option('sfilter_version', SFILTER_VERSION);
    }

    /**
     * Create custom tables
     *
     * @return void
     */
    public function create_tables()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $checkout_scheme = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}sfilter_data` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(250) DEFAULT NULL,
            `value` varchar(250) DEFAULT NULL,
            `create_at` datetime NOT NULL,
            PRIMARY KEY (`id`)
          ) $charset_collate";

        if (!function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        dbDelta($checkout_scheme);
    }
}
