<?php

namespace SFilter\Quote;

/**
 * Add PDF download action to WooCommerce order list actions column
 */
class OrderColumns
{
    public function __construct()
    {
        // Add PDF action to WooCommerce's existing actions column
        add_filter('woocommerce_admin_order_actions', [$this, 'add_pdf_action'], 10, 2);

        // Add custom CSS for the PDF action icon
        add_action('admin_head', [$this, 'add_action_styles']);
    }

    /**
     * Add PDF download action to order actions
     *
     * @param array $actions Existing actions
     * @param \WC_Order $order Order object
     * @return array
     */
    public function add_pdf_action($actions, $order)
    {
        $pdf_url = $order->get_meta('_sf_quotation_pdf_url');

        if (!empty($pdf_url)) {
            $actions['sf_download_pdf'] = [
                'url'    => '#',  // Prevent direct navigation, handled via AJAX
                'name'   => __('Download Quote PDF', 'sfilter'),
                'action' => 'sf_download_pdf',
            ];
        }

        return $actions;
    }

    /**
     * Add CSS for PDF action icon
     */
    public function add_action_styles()
    {
        $screen = get_current_screen();
        if (!$screen || !in_array($screen->id, ['edit-shop_order', 'woocommerce_page_wc-orders'])) {
            return;
        }
        ?>
        <style>
            .wc-action-button-sf_download_pdf::after {
                font-family: Dashicons !important;
                content: "\f190" !important; /* dashicons-media-document (PDF-like icon) */
            }
            .wc-action-button-sf_download_pdf {
                color: #a00 !important;
            }
            .wc-action-button-sf_download_pdf:hover {
                color: #dc3232 !important;
            }
            .wc-action-button-sf_download_pdf.sf-loading::after {
                animation: sf-spin 1s linear infinite;
            }
            @keyframes sf-spin {
                100% { transform: rotate(360deg); }
            }
        </style>
        <?php
    }
}
