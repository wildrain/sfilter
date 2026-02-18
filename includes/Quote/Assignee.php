<?php

namespace SFilter\Quote;

/**
 * Assignee dropdown field for orders
 */
class Assignee
{
    /**
     * Assignee options
     *
     * @var array
     */
    private $assignees = [
        'eastern' => 'Eastern Region - Alla.Jehad@saudifilter.com',
        'central' => 'Central Region - Esa.Ruhulla@saudifilter.com',
        'western' => 'Western Region - Haytham.Bashir@saudifilter.com',
    ];

    public function __construct()
    {
        add_action('woocommerce_admin_order_data_after_shipping_address', [$this, 'display_field'], 20);

        // Legacy save
        add_action('woocommerce_process_shop_order_meta', [$this, 'save_field']);

        // HPOS save
        add_action('woocommerce_update_order', [$this, 'save_field_hpos']);

        // AJAX save
        add_action('wp_ajax_sf_save_assignee', [$this, 'ajax_save_assignee']);

        // AJAX PDF download
        add_action('wp_ajax_sf_download_pdf', [$this, 'ajax_download_pdf']);
    }

    /**
     * Display assignee dropdown
     *
     * @param \WC_Order $order
     */
    public function display_field($order)
    {
        $assignees = $this->assignees;
        $current_assignee = $order->get_meta('_sf_assignee');

        include __DIR__ . '/views/assignee-field.php';
    }

    /**
     * Save assignee field (legacy)
     *
     * @param int $order_id
     */
    public function save_field($order_id)
    {
        if (!isset($_POST['sf_assignee'])) {
            return;
        }

        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }

        $assignee = sanitize_text_field($_POST['sf_assignee']);

        // Validate against allowed values
        if (!empty($assignee) && !array_key_exists($assignee, $this->assignees)) {
            return;
        }

        // Only update meta, don't call save() - WooCommerce handles that
        $order->update_meta_data('_sf_assignee', $assignee);
    }

    /**
     * Save assignee field (HPOS)
     *
     * @param \WC_Order|int $order Order object or ID
     */
    public function save_field_hpos($order)
    {
        if (!isset($_POST['sf_assignee'])) {
            return;
        }

        // Handle both order object and order ID
        if (is_numeric($order)) {
            $order = wc_get_order($order);
        }

        if (!$order) {
            return;
        }

        $assignee = sanitize_text_field($_POST['sf_assignee']);

        // Validate against allowed values
        if (!empty($assignee) && !array_key_exists($assignee, $this->assignees)) {
            return;
        }

        // Only update meta, don't call save() to avoid recursion
        // WooCommerce will save the order after this hook
        $order->update_meta_data('_sf_assignee', $assignee);
    }

    /**
     * AJAX handler for saving assignee
     */
    public function ajax_save_assignee()
    {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sf_save_assignee')) {
            wp_send_json_error(['message' => __('Security check failed.', 'sfilter')]);
        }

        // Check permissions
        if (!current_user_can('edit_shop_orders')) {
            wp_send_json_error(['message' => __('Permission denied.', 'sfilter')]);
        }

        $order_id = isset($_POST['order_id']) ? absint($_POST['order_id']) : 0;
        $assignee = isset($_POST['assignee']) ? sanitize_text_field($_POST['assignee']) : '';

        $order = wc_get_order($order_id);
        if (!$order) {
            wp_send_json_error(['message' => __('Order not found.', 'sfilter')]);
        }

        // Validate assignee value
        if (!empty($assignee) && !array_key_exists($assignee, $this->assignees)) {
            wp_send_json_error(['message' => __('Invalid assignee.', 'sfilter')]);
        }

        $order->update_meta_data('_sf_assignee', $assignee);
        $order->save();

        wp_send_json_success(['message' => __('Assignee saved successfully.', 'sfilter')]);
    }

    /**
     * AJAX handler for PDF download
     */
    public function ajax_download_pdf()
    {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sf_download_pdf')) {
            wp_send_json_error(['message' => __('Security check failed.', 'sfilter')]);
        }

        // Check permissions
        if (!current_user_can('edit_shop_orders')) {
            wp_send_json_error(['message' => __('Permission denied.', 'sfilter')]);
        }

        $order_id = isset($_POST['order_id']) ? absint($_POST['order_id']) : 0;

        $order = wc_get_order($order_id);
        if (!$order) {
            wp_send_json_error(['message' => __('Order not found.', 'sfilter')]);
        }

        $pdf_url = $order->get_meta('_sf_quotation_pdf_url');

        if (empty($pdf_url)) {
            wp_send_json_error(['message' => __('PDF not found.', 'sfilter')]);
        }

        wp_send_json_success(['url' => $pdf_url]);
    }
}
