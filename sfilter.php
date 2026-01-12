<?php

/**
 * Plugin Name:       SFilter
 * Plugin URI:        https://saudifilter.com
 * Description:       Helper plugin for Saudi Filter
 * Version:           1.0.0
 * Author:            SFilter
 * Author URI:        https://saudifilter.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sfilter
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Main plugin class
 */
final class SFilter
{
    /**
     * Plugin version
     *
     * @var string
     */
    const version = '1.0.0';

    /**
     * contractor
     */
    private function __construct()
    {
        $this->define_constants();

        register_activation_hook(__FILE__, [$this, 'activate']);
        add_action('plugins_loaded', [$this, 'init_plugin']);
    }

    /**
     * Initialize singleton instance
     *
     * @return \SFilter
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Define constants
     *
     * @return void
     */
    public function define_constants()
    {
        define('SFILTER_VERSION', self::version);
        define('SFILTER_FILE', __FILE__);
        define('SFILTER_PATH', __DIR__);
        define('SFILTER_URL', plugins_url('', SFILTER_FILE));
        define('SFILTER_ASSETS', SFILTER_URL . '/assets');
        define('SFILTER_DIR_PATH', plugin_dir_path(__FILE__));
        define('SFILTER_ELEMENTOR', SFILTER_DIR_PATH . 'includes/Elementor/');
    }

    /**
     * Plugin information
     *
     * @return void
     */
    public function activate()
    {
        $installer = new SFilter\Installer();
        $installer->run();
    }

    /**
     * Load plugin files
     *
     * @return void
     */
    public function init_plugin()
    {
        new SFilter\Assets();
        new SFilter\Ajax();
        new SFilter\API();
        new SFilter\Load_Elementor();
        new SFilter\Generator();
        new SFilter\Customizer();
        if (is_admin()) {
            new SFilter\Admin();
        } else {
            new SFilter\Frontend();
        }
    }
}

/**
 * Initialize main plugin
 *
 * @return \SFilter
 */
function sfilter()
{
    return SFilter::init();
}

sfilter();
