<?php

namespace SFilter;

/**
 * Quote module - Renames WooCommerce Orders to Quotes and adds quote management features
 */
class Quote
{
    public function __construct()
    {
        if (is_admin()) {
            new Quote\Labels();
            new Quote\OrderColumns();
            new Quote\Metabox();
            new Quote\CustomerFields();
            new Quote\Assignee();
            new Quote\Assets();
        }
    }
}
