<?php

namespace SFilter\Checkout;

class Hooks
{
    public function __construct()
    {
        // Remove default WooCommerce checkout elements
        add_filter('woocommerce_checkout_fields', [$this, 'remove_default_fields'], 9999);
        add_filter('woocommerce_cart_needs_payment', '__return_false');

        // Remove coupon hooks at the right time (after WC registers them)
        add_action('wp', [$this, 'remove_coupon_hooks']);

        // Disable coupons entirely on checkout
        add_filter('woocommerce_coupons_enabled', [$this, 'disable_coupons_on_checkout']);

        // Add custom checkout form
        add_action('woocommerce_checkout_before_customer_details', [$this, 'output_custom_form']);

        // Hide default WooCommerce sections via CSS
        add_action('woocommerce_before_checkout_form', [$this, 'hide_default_sections']);

        // Save order meta
        add_action('woocommerce_checkout_update_order_meta', [$this, 'save_order_meta']);

        // Display PDF link in admin
        add_action('woocommerce_admin_order_data_after_billing_address', [$this, 'display_admin_pdf_link']);

        // Remove order review (payment methods, etc.)
        add_action('wp', [$this, 'remove_order_review_hooks']);

        // Disable the default place order button
        add_filter('woocommerce_order_button_html', '__return_empty_string');
    }

    /**
     * Remove default WooCommerce checkout fields
     *
     * @param array $fields
     * @return array
     */
    public function remove_default_fields($fields)
    {
        // Clear all default billing fields
        $fields['billing'] = [];

        // Clear all default shipping fields
        $fields['shipping'] = [];

        // Clear account fields
        $fields['account'] = [];

        // Clear order fields (order comments, etc.)
        $fields['order'] = [];

        return $fields;
    }

    /**
     * Remove coupon hooks after WooCommerce registers them
     */
    public function remove_coupon_hooks()
    {
        if (!is_checkout()) {
            return;
        }

        remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
    }

    /**
     * Disable coupons on checkout page
     *
     * @param bool $enabled
     * @return bool
     */
    public function disable_coupons_on_checkout($enabled)
    {
        if (is_checkout()) {
            return false;
        }
        return $enabled;
    }

    /**
     * Remove order review hooks after WooCommerce registers them
     */
    public function remove_order_review_hooks()
    {
        if (!is_checkout()) {
            return;
        }

        remove_action('woocommerce_checkout_order_review', 'woocommerce_order_review', 10);
        remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
    }

    /**
     * Output custom checkout form
     */
    public function output_custom_form()
    {
        include __DIR__ . '/views/checkout-form.php';
    }

    /**
     * Hide default WooCommerce sections via inline CSS
     */
    public function hide_default_sections()
    {
        ?>
        <style>
            /* Coupon form elements */
            .woocommerce-checkout .woocommerce-form-coupon-toggle,
            .woocommerce-checkout .checkout_coupon,
            .woocommerce-checkout .woocommerce-form-coupon,
            .woocommerce-checkout .coupon,

            /* Theme coupon form */
            body.woocommerce-checkout .checkout-coupon-wrapper,
            body.woocommerce-checkout .coupon-error,

            /* Order review/summary */
            .woocommerce-checkout #order_review,
            .woocommerce-checkout .woocommerce-checkout-review-order,
            .woocommerce-checkout .woocommerce-checkout-review-order-table,
            .woocommerce-checkout .shop_table,
            .woocommerce-checkout .order-total,

            /* Customer details */
            .woocommerce-checkout #customer_details,
            .woocommerce-checkout .col-1,
            .woocommerce-checkout .col-2,

            /* Payment and other sections */
            .woocommerce-checkout .woocommerce-checkout-payment,
            .woocommerce-checkout #payment,
            .woocommerce-checkout .woocommerce-additional-fields,
            .woocommerce-checkout .woocommerce-billing-fields,
            .woocommerce-checkout .woocommerce-shipping-fields,
            .woocommerce-checkout .place-order,
            .woocommerce-checkout #order_review .woocommerce-checkout-payment {
                display: none !important;
            }

            /* Hide the Payments wrapper that contains order_review */
            .woocommerce-checkout .col-span-full:has(#order_review) {
                display: none !important;
            }

            /* Make checkout form wrapper span full width */
            .woocommerce-checkout .sf-checkout-form-wrapper {
                grid-column: 1 / -1;
            }
        </style>
        <?php
    }

    /**
     * Save custom fields as order meta
     *
     * @param int $order_id
     */
    public function save_order_meta($order_id)
    {
        $data = Fields::get_sanitized_data();

        foreach ($data as $key => $value) {
            if (!empty($value)) {
                update_post_meta($order_id, '_' . $key, $value);
            }
        }
    }

    /**
     * Display PDF download link in admin order page
     *
     * @param \WC_Order $order
     */
    public function display_admin_pdf_link($order)
    {
        $pdf_url = get_post_meta($order->get_id(), '_sf_quotation_pdf_url', true);

        if (!empty($pdf_url)) {
            echo '<p><strong>' . __('Quotation PDF:', 'sfilter') . '</strong><br>';
            echo '<a href="' . esc_url($pdf_url) . '" target="_blank" class="button">';
            echo __('View Quotation PDF', 'sfilter');
            echo '</a></p>';
        }

        // Display custom fields
        $fields = Fields::get_fields();
        echo '<div class="sf-custom-fields" style="margin-top: 15px;">';
        echo '<h4>' . __('Customer Information', 'sfilter') . '</h4>';

        foreach ($fields as $key => $field) {
            $value = get_post_meta($order->get_id(), '_' . $key, true);
            if (!empty($value)) {
                if ($key === 'sf_region') {
                    $regions = Fields::get_regions();
                    $value = isset($regions[$value]) ? $regions[$value] : $value;
                }
                echo '<p><strong>' . esc_html($field['label']) . ':</strong> ' . esc_html($value) . '</p>';
            }
        }

        echo '</div>';
    }
}
