<?php

namespace SFilter;

class Product
{
    public function __construct()
    {
        if (!is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {
            new Product\Tabs();
            new Product\Assets();
            new Product\Button();
        }
    }
}
