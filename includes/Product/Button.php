<?php

namespace SFilter\Product;

class Button
{
    public function __construct()
    {
        // Change button text to "Add Quote"
        add_filter('woocommerce_product_single_add_to_cart_text', [$this, 'change_button_text']);

        // Make products without price purchasable
        add_filter('woocommerce_is_purchasable', [$this, 'make_purchasable'], 10, 2);

        // Redirect directly to checkout after adding to cart
        add_filter('woocommerce_add_to_cart_redirect', [$this, 'redirect_to_checkout']);
    }

    public function change_button_text($text)
    {
        return __('Add Quote', 'sfilter');
    }

    /**
     * Make products purchasable even without a price
     */
    public function make_purchasable($purchasable, $product)
    {
        return true;
    }

    /**
     * Redirect to checkout page after adding to cart
     */
    public function redirect_to_checkout($url)
    {
        return wc_get_checkout_url();
    }
}
