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

        // Remove order review (payment methods, etc.)
        add_action('wp', [$this, 'remove_order_review_hooks']);

        // Clear "added to cart" notices on checkout page
        add_action('wp', [$this, 'clear_cart_notices']);

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

        // Remove theme's order summary hook (Turbo theme)
        remove_all_actions('woocommerce_checkout_after_order_review');
    }

    /**
     * Clear WooCommerce "added to cart" notices on checkout page
     */
    public function clear_cart_notices()
    {
        if (!is_checkout()) {
            return;
        }

        wc_clear_notices();
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

            /* Theme order summary - target the container div with the h4 and summary */
            body.woocommerce-checkout .turbo-checkout-order-summary,
            body.woocommerce-checkout div:has(> .turbo-checkout-order-summary),
            body.woocommerce-checkout div.mt-8:has(.turbo-checkout-order-summary),
            body.woocommerce-checkout div[class*="mt-8"]:has(.turbo-checkout-order-summary),

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

            /* Hide coupon and order summary sections */
            .sf-hidden-section {
                display: none !important;
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Hide coupon section - find by input placeholder and hide its row
                var couponInputs = document.querySelectorAll('input[placeholder*="coupon" i], input[placeholder*="كوبون" i]');
                couponInputs.forEach(function(input) {
                    // Find the flex row containing both input and button
                    var row = input.closest('div');
                    while (row && row.parentElement) {
                        var hasButton = row.querySelector('button');
                        var hasInput = row.querySelector('input');
                        if (hasButton && hasInput) {
                            row.classList.add('sf-hidden-section');
                            break;
                        }
                        row = row.parentElement;
                    }
                });

                // Hide ORDER SUMMARY section - target turbo-checkout-order-summary and its parent
                var orderSummary = document.querySelector('.turbo-checkout-order-summary');
                if (orderSummary) {
                    // Hide the parent div that contains both h4 and the summary
                    var parent = orderSummary.parentElement;
                    if (parent) {
                        parent.classList.add('sf-hidden-section');
                    }
                }
            });
        </script>
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

}
