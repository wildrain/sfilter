<?php

namespace SFilter\Quote;

/**
 * Quote download metabox in order edit page
 */
class Metabox
{
    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'add_metabox']);
    }

    /**
     * Add metabox to order edit screen
     */
    public function add_metabox()
    {
        $screen = $this->get_order_screen();

        add_meta_box(
            'sf_quote_download',
            __('Quote Download', 'sfilter'),
            [$this, 'render_metabox'],
            $screen,
            'side',
            'high'
        );
    }

    /**
     * Render the metabox content
     *
     * @param \WP_Post|\WC_Order $post_or_order
     */
    public function render_metabox($post_or_order)
    {
        $order = $this->get_order($post_or_order);
        if (!$order) {
            return;
        }

        $pdf_url = $order->get_meta('_sf_quotation_pdf_url');

        include __DIR__ . '/views/metabox.php';
    }

    /**
     * Get the order screen ID for metabox
     *
     * @return string
     */
    private function get_order_screen()
    {
        // Check for HPOS
        if (function_exists('wc_get_page_screen_id')) {
            $hpos_screen = wc_get_page_screen_id('shop-order');
            if ($hpos_screen && $hpos_screen !== 'shop_order') {
                return $hpos_screen;
            }
        }

        return 'shop_order';
    }

    /**
     * Get order object from post or order
     *
     * @param \WP_Post|\WC_Order $post_or_order
     * @return \WC_Order|null
     */
    private function get_order($post_or_order)
    {
        if ($post_or_order instanceof \WC_Order) {
            return $post_or_order;
        }

        if ($post_or_order instanceof \WP_Post) {
            return wc_get_order($post_or_order->ID);
        }

        return null;
    }
}
