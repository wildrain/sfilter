<?php

namespace SFilter\Customizer;

use Kirki;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Header_Settings
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->initHeaderSettings();
        $this->HeaderSettings();
    }

    /**
     * initHeaderSettings
     *
     * @return void
     */
    public function initHeaderSettings()
    {
        Kirki::add_section('sfilter_header_section', [
            'title'       => esc_html__('Header', 'sfilter'),
            'description' => esc_html__('Global settings for header located here', 'sfilter'),
            'panel'       => 'SFILTER_config_panel',
            'priority'    => 160,
        ]);
    }

    /**
     * HeaderSettings
     *
     * @return void
     */
    public function HeaderSettings()
    { // section choosing key : sfilter_header_section

        Kirki::add_field('SFILTER_config', [
            'type'        => 'image',
            'settings'    => 'SFILTER_header_logo',
            'label'       => esc_html__('Main Logo', 'sfilter'),
            'section'     => 'sfilter_header_section',
            'default'     => '',
        ]);
    }
}
