<?php

namespace SFilter\Quote;

/**
 * Enqueue admin CSS/JS for Quote module
 */
class Assets
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /**
     * Enqueue admin assets on order screens
     *
     * @param string $hook Current admin page hook
     */
    public function enqueue_assets($hook)
    {
        if (!$this->is_order_screen()) {
            return;
        }

        wp_enqueue_style(
            'sf-quote-admin',
            SFILTER_URL . '/includes/Quote/assets/css/quote-admin.css',
            [],
            SFILTER_VERSION
        );

        wp_enqueue_script(
            'sf-quote-admin',
            SFILTER_URL . '/includes/Quote/assets/js/quote-admin.js',
            ['jquery'],
            SFILTER_VERSION,
            true
        );

        wp_localize_script('sf-quote-admin', 'sfQuoteAdmin', [
            'ajaxUrl'  => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('sf_save_assignee'),
            'pdfNonce' => wp_create_nonce('sf_download_pdf'),
        ]);
    }

    /**
     * Check if current screen is an order-related screen
     *
     * @return bool
     */
    private function is_order_screen()
    {
        if (!function_exists('get_current_screen')) {
            return false;
        }

        $screen = get_current_screen();
        if (!$screen) {
            return false;
        }

        $order_screens = [
            'edit-shop_order',
            'shop_order',
            'woocommerce_page_wc-orders',
        ];

        return in_array($screen->id, $order_screens, true);
    }
}
