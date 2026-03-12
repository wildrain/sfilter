<?php

namespace SFilter\Quote;

use SFilter\Checkout\Fields;

/**
 * Display customer information in order edit page
 */
class CustomerFields
{
    public function __construct()
    {
        add_action('woocommerce_admin_order_data_after_shipping_address', [$this, 'display_fields']);
    }

    /**
     * Display customer fields after shipping address
     *
     * @param \WC_Order $order
     */
    public function display_fields($order)
    {
        $fields = Fields::get_fields();
        $regions = Fields::get_regions();

        include __DIR__ . '/views/customer-fields.php';
    }
}
