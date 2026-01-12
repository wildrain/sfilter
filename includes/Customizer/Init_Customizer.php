<?php

namespace SFilter\Customizer;

use Kirki;

if (!defined('ABSPATH')) {
    exit;
}

class Init_Customizer
{
    public function __construct()
    {
        $this->initPanel();
    }

    /**
     * Init panel
     *
     * @return void
     */
    public function initPanel()
    {
        Kirki::add_config('SFILTER_config', [
            'capability'  => 'edit_theme_options',
            'option_type' => 'theme_mod',
        ]);

        Kirki::add_panel('SFILTER_config_panel', [
            'priority'    => 10,
            'title'       => esc_html__('SFilter Options', 'sfilter'),
            'description' => esc_html__('SFilter Options description', 'sfilter'),
        ]);
    }
}
