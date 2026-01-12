<?php

namespace SFilter;

class ProductBuilder
{
    public function __construct()
    {
        if (is_admin()) {
            new ProductBuilder\Metabox();
            new ProductBuilder\Assets();
        }
    }
}
