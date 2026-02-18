<?php

namespace SFilter\Checkout;

class Ajax
{
    public function __construct()
    {
        add_action('wp_ajax_sf_checkout_quotation', [$this, 'handle_quotation']);
        add_action('wp_ajax_nopriv_sf_checkout_quotation', [$this, 'handle_quotation']);
    }

    /**
     * Handle quotation AJAX request
     */
    public function handle_quotation()
    {
        // Verify nonce
        if (!isset($_POST['sf_checkout_nonce']) || !wp_verify_nonce($_POST['sf_checkout_nonce'], 'sf_checkout_quotation')) {
            wp_send_json_error([
                'message' => __('Security verification failed. Please refresh the page and try again.', 'sfilter'),
            ]);
        }

        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            wp_send_json_error([
                'message' => __('WooCommerce is required.', 'sfilter'),
            ]);
        }

        // Check cart is not empty
        if (WC()->cart->is_empty()) {
            wp_send_json_error([
                'message' => __('Your cart is empty.', 'sfilter'),
            ]);
        }

        // Get and sanitize form data
        $data = Fields::get_sanitized_data();

        // Validate fields
        $validation = Fields::validate($data);

        if (!$validation['valid']) {
            wp_send_json_error([
                'message' => __('Please correct the errors below.', 'sfilter'),
                'errors'  => $validation['errors'],
            ]);
        }

        // Create the order
        $order_id = $this->create_order($data);

        if (is_wp_error($order_id)) {
            wp_send_json_error([
                'message' => $order_id->get_error_message(),
            ]);
        }

        // Generate PDF
        $pdf = new PDF($order_id, $data);
        $pdf_path = $pdf->generate();

        if (!$pdf_path) {
            wp_send_json_error([
                'message' => __('Failed to generate quotation PDF. Please try again.', 'sfilter'),
            ]);
        }

        // Get PDF URL
        $pdf_url = $pdf->get_pdf_url();

        // Clear the cart
        WC()->cart->empty_cart();

        // Get thank you page URL
        $order = wc_get_order($order_id);
        $redirect_url = $order->get_checkout_order_received_url();

        wp_send_json_success([
            'message'      => __('Your quotation has been generated successfully!', 'sfilter'),
            'order_id'     => $order_id,
            'pdf_url'      => $pdf_url,
            'redirect_url' => $redirect_url,
        ]);
    }

    /**
     * Create WooCommerce order from cart
     *
     * @param array $data Customer data
     * @return int|\WP_Error Order ID or error
     */
    protected function create_order($data)
    {
        try {
            // Create order
            $order = wc_create_order([
                'status' => 'pending',
            ]);

            if (is_wp_error($order)) {
                return $order;
            }

            // Add cart items to order
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $product = $cart_item['data'];
                $quantity = $cart_item['quantity'];

                $order->add_product(
                    $product,
                    $quantity,
                    [
                        'subtotal' => $cart_item['line_subtotal'],
                        'total'    => $cart_item['line_total'],
                    ]
                );
            }

            // Set billing address from custom fields
            $order->set_billing_first_name($this->get_first_name($data['sf_full_name']));
            $order->set_billing_last_name($this->get_last_name($data['sf_full_name']));
            $order->set_billing_email($data['sf_email']);
            $order->set_billing_phone($data['sf_phone']);
            $order->set_billing_company($data['sf_company']);
            $order->set_billing_address_1($data['sf_address']);
            $order->set_billing_state($data['sf_region']);

            // Save custom fields as order meta
            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    $order->update_meta_data('_' . $key, $value);
                }
            }

            // Add order note
            $order->add_order_note(
                __('Quotation order created via SFilter custom checkout.', 'sfilter')
            );

            // Calculate totals
            $order->calculate_totals();

            // Save order
            $order->save();

            return $order->get_id();

        } catch (\Exception $e) {
            return new \WP_Error('order_creation_failed', $e->getMessage());
        }
    }

    /**
     * Extract first name from full name
     *
     * @param string $full_name
     * @return string
     */
    protected function get_first_name($full_name)
    {
        $parts = explode(' ', trim($full_name), 2);
        return $parts[0] ?? '';
    }

    /**
     * Extract last name from full name
     *
     * @param string $full_name
     * @return string
     */
    protected function get_last_name($full_name)
    {
        $parts = explode(' ', trim($full_name), 2);
        return $parts[1] ?? '';
    }
}
