<?php

namespace SFilter\Checkout;

class Assets
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
    }

    /**
     * Enqueue checkout assets
     */
    public function enqueue()
    {
        if (!is_checkout()) {
            return;
        }

        // Enqueue CSS
        wp_enqueue_style(
            'sf-checkout',
            plugins_url('assets/css/sf-checkout.css', __FILE__),
            [],
            SFILTER_VERSION
        );

        // Enqueue JS
        wp_enqueue_script(
            'sf-checkout',
            plugins_url('assets/js/sf-checkout.js', __FILE__),
            ['jquery'],
            SFILTER_VERSION,
            true
        );

        // Localize script
        wp_localize_script('sf-checkout', 'sfCheckout', [
            'ajaxUrl'  => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('sf_checkout_quotation'),
            'i18n'     => [
                'required'       => __('This field is required.', 'sfilter'),
                'invalidEmail'   => __('Please enter a valid email address.', 'sfilter'),
                'invalidPhone'   => __('Please enter a valid phone number.', 'sfilter'),
                'selectRegion'   => __('Please select a region.', 'sfilter'),
                'processing'     => __('Processing...', 'sfilter'),
                'downloadReady'  => __('Your quotation is ready!', 'sfilter'),
                'error'          => __('An error occurred. Please try again.', 'sfilter'),
            ],
        ]);
    }
}
