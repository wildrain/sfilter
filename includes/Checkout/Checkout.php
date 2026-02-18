<?php

namespace SFilter\Checkout;

class Checkout
{
    /**
     * Initialize the checkout module
     */
    public function __construct()
    {
        // Only load on frontend and admin
        if (defined('DOING_AJAX') && DOING_AJAX) {
            new Ajax();
            return;
        }

        // Load AJAX handler for both admin and frontend
        new Ajax();

        if (!is_admin()) {
            // Frontend components
            new Assets();
            new Hooks();
        }
    }
}
