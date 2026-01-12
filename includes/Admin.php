<?php

namespace SFilter;

class Admin
{
    /**
     * Class initialize
     */
    function __construct()
    {
        new Admin\Menu();
        new Admin\Handler();
        new Admin\TestBgJob();
    }
}
