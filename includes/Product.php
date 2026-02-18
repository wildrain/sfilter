<?php

namespace SFilter;

class Product
{
    public function __construct()
    {
        if (!is_admin()) {
            new Product\Tabs();
            new Product\Assets();
            new Product\Button();
        }
    }
}
