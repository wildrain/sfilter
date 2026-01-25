<?php

/**
 * Elementor Part Search Widget.
 *
 * @package SFilter\Elementor\Widgets\PartSearch
 */

namespace SFilter\Elementor\Widgets\PartSearch;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Part Search Widget
 *
 * Elementor widget for tabbed search functionality.
 *
 * @since 1.0.0
 */
class PartSearch extends Widget_Base
{
    /**
     * Retrieve the widget name.
     *
     * @since 1.0.0
     * @access public
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'sfilter_part_search';
    }

    /**
     * Retrieve the widget title.
     *
     * @since 1.0.0
     * @access public
     * @return string Widget title.
     */
    public function get_title()
    {
        return __('Part Search', 'sfilter');
    }

    /**
     * Retrieve the widget icon.
     *
     * @since 1.0.0
     * @access public
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'eicon-search';
    }

    /**
     * Retrieve the list of categories the widget belongs to.
     *
     * @since 1.0.0
     * @access public
     * @return array Widget categories.
     */
    public function get_categories()
    {
        return ['sfilter-widgets'];
    }

    /**
     * Get script dependencies.
     *
     * @since 1.0.0
     * @access public
     * @return array Script dependencies.
     */
    public function get_script_depends()
    {
        return ['sfilter-part-search'];
    }

    /**
     * Get style dependencies.
     *
     * @since 1.0.0
     * @access public
     * @return array Style dependencies.
     */
    public function get_style_depends()
    {
        return ['sfilter-part-search'];
    }

    /**
     * Register widget controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls()
    {
        $this->register_content_controls();
        $this->register_tab_controls();
        $this->register_style_controls();
    }

    /**
     * Register content controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_content_controls()
    {
        $this->start_controls_section(
            'section_general',
            [
                'label' => __('General Settings', 'sfilter'),
            ]
        );

        $this->add_control(
            'shop_page_url',
            [
                'label' => __('Shop Page URL', 'sfilter'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => '/shop',
                'placeholder' => __('/shop or full URL', 'sfilter'),
                'description' => __('The URL where search results will be displayed. Search parameters will be appended.', 'sfilter'),
            ]
        );

        $this->add_control(
            'search_param',
            [
                'label' => __('Search Parameter Name', 'sfilter'),
                'type' => Controls_Manager::TEXT,
                'default' => 'item',
                'placeholder' => __('item', 'sfilter'),
                'description' => __('The URL parameter name for search query (e.g., "s" for ?s=searchterm)', 'sfilter'),
            ]
        );

        $this->end_controls_section();

        // Tab Labels Section
        $this->start_controls_section(
            'section_tab_labels',
            [
                'label' => __('Tab Labels', 'sfilter'),
            ]
        );

        $this->add_control(
            'tab1_label',
            [
                'label' => __('Tab 1 Label', 'sfilter'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Part Search', 'sfilter'),
            ]
        );

        $this->add_control(
            'tab1_enabled',
            [
                'label' => __('Enable Tab 1', 'sfilter'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'tab2_label',
            [
                'label' => __('Tab 2 Label', 'sfilter'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Multipart Search', 'sfilter'),
            ]
        );

        $this->add_control(
            'tab2_enabled',
            [
                'label' => __('Enable Tab 2', 'sfilter'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'tab3_label',
            [
                'label' => __('Tab 3 Label', 'sfilter'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Attribute Search', 'sfilter'),
            ]
        );

        $this->add_control(
            'tab3_enabled',
            [
                'label' => __('Enable Tab 3', 'sfilter'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'tab4_label',
            [
                'label' => __('Tab 4 Label', 'sfilter'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Equipment Search', 'sfilter'),
            ]
        );

        $this->add_control(
            'tab4_enabled',
            [
                'label' => __('Enable Tab 4', 'sfilter'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Placeholders Section
        $this->start_controls_section(
            'section_placeholders',
            [
                'label' => __('Placeholder Texts', 'sfilter'),
            ]
        );

        $this->add_control(
            'tab1_placeholder',
            [
                'label' => __('Part Search Placeholder', 'sfilter'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('Search Part Number, Cross Reference, Equipment or Description', 'sfilter'),
            ]
        );

        $this->add_control(
            'tab2_placeholder',
            [
                'label' => __('Multipart Search Placeholder', 'sfilter'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __('Enter or copy/paste up to 15 part numbers -- one part per line or separated by commas', 'sfilter'),
            ]
        );

        $this->add_control(
            'tab3_placeholder',
            [
                'label' => __('Attribute Search Placeholder', 'sfilter'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('Search by product attributes...', 'sfilter'),
            ]
        );

        $this->add_control(
            'tab4_placeholder',
            [
                'label' => __('Equipment Search Placeholder', 'sfilter'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('Search by equipment make, model or year...', 'sfilter'),
            ]
        );

        $this->end_controls_section();

        // Multipart Settings Section
        $this->start_controls_section(
            'section_multipart',
            [
                'label' => __('Multipart Settings', 'sfilter'),
            ]
        );

        $this->add_control(
            'max_parts',
            [
                'label' => __('Maximum Parts Allowed', 'sfilter'),
                'type' => Controls_Manager::NUMBER,
                'default' => 15,
                'min' => 1,
                'max' => 50,
            ]
        );

        $this->add_control(
            'max_parts_error',
            [
                'label' => __('Max Parts Error Message', 'sfilter'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('You have searched for too many part numbers, the limit is {max}.', 'sfilter'),
                'description' => __('Use {max} to display the maximum number.', 'sfilter'),
            ]
        );

        $this->end_controls_section();

        // Button Settings Section
        $this->start_controls_section(
            'section_button',
            [
                'label' => __('Button Settings', 'sfilter'),
            ]
        );

        $this->add_control(
            'search_button_text',
            [
                'label' => __('Search Button Text', 'sfilter'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Search', 'sfilter'),
            ]
        );

        $this->add_control(
            'show_search_icon',
            [
                'label' => __('Show Search Icon', 'sfilter'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'reset_button_text',
            [
                'label' => __('Reset Button Text', 'sfilter'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Reset', 'sfilter'),
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Register tab content controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_tab_controls()
    {
        // Attribute Search Fields
        $this->start_controls_section(
            'section_attribute_fields',
            [
                'label' => __('Attribute Search Fields', 'sfilter'),
            ]
        );

        $this->add_control(
            'attribute_fields_info',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __('Configure attribute search dropdown fields. These will appear as select dropdowns in the Attribute Search tab.', 'sfilter'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'field_label',
            [
                'label' => __('Field Label', 'sfilter'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Select Option', 'sfilter'),
            ]
        );

        $repeater->add_control(
            'field_param',
            [
                'label' => __('URL Parameter', 'sfilter'),
                'type' => Controls_Manager::TEXT,
                'default' => 'attribute',
                'description' => __('The parameter name to use in the URL', 'sfilter'),
            ]
        );

        $repeater->add_control(
            'field_options',
            [
                'label' => __('Options (one per line)', 'sfilter'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => "Option 1\nOption 2\nOption 3",
                'description' => __('Enter each option on a new line. Use format "value|label" for custom values.', 'sfilter'),
            ]
        );

        $this->add_control(
            'attribute_fields',
            [
                'label' => __('Attribute Fields', 'sfilter'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'field_label' => __('Category', 'sfilter'),
                        'field_param' => 'category',
                        'field_options' => "All Categories\nFilters\nParts\nAccessories",
                    ],
                    [
                        'field_label' => __('Brand', 'sfilter'),
                        'field_param' => 'brand',
                        'field_options' => "All Brands\nBrand A\nBrand B\nBrand C",
                    ],
                ],
                'title_field' => '{{{ field_label }}}',
            ]
        );

        $this->end_controls_section();

        // Equipment Search Fields
        $this->start_controls_section(
            'section_equipment_fields',
            [
                'label' => __('Equipment Search Fields', 'sfilter'),
            ]
        );

        $this->add_control(
            'equipment_make_label',
            [
                'label' => __('Make Field Label', 'sfilter'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Make', 'sfilter'),
            ]
        );

        $this->add_control(
            'equipment_make_options',
            [
                'label' => __('Make Options (one per line)', 'sfilter'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => "Select Make\nMake A\nMake B\nMake C",
            ]
        );

        $this->add_control(
            'equipment_model_label',
            [
                'label' => __('Model Field Label', 'sfilter'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Model', 'sfilter'),
            ]
        );

        $this->add_control(
            'equipment_model_options',
            [
                'label' => __('Model Options (one per line)', 'sfilter'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => "Select Model\nModel 1\nModel 2\nModel 3",
            ]
        );

        $this->add_control(
            'equipment_year_label',
            [
                'label' => __('Year Field Label', 'sfilter'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Year', 'sfilter'),
            ]
        );

        $this->add_control(
            'equipment_year_options',
            [
                'label' => __('Year Options (one per line)', 'sfilter'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => "Select Year\n2024\n2023\n2022\n2021\n2020",
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Register style controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_style_controls()
    {
        // Container Style
        $this->start_controls_section(
            'section_container_style',
            [
                'label' => __('Container', 'sfilter'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label' => __('Padding', 'sfilter'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .sf-search-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'container_bg_color',
            [
                'label' => __('Background Color', 'sfilter'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sf-search-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'container_border',
                'selector' => '{{WRAPPER}} .sf-search-wrapper',
            ]
        );

        $this->add_responsive_control(
            'container_border_radius',
            [
                'label' => __('Border Radius', 'sfilter'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .sf-search-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'container_box_shadow',
                'selector' => '{{WRAPPER}} .sf-search-wrapper',
            ]
        );

        $this->end_controls_section();

        // Tabs Style
        $this->start_controls_section(
            'section_tabs_style',
            [
                'label' => __('Tabs', 'sfilter'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'tabs_typography',
                'selector' => '{{WRAPPER}} .sf-search-tabs .sf-tab-button',
            ]
        );

        $this->start_controls_tabs('tabs_style_tabs');

        $this->start_controls_tab(
            'tab_normal',
            [
                'label' => __('Normal', 'sfilter'),
            ]
        );

        $this->add_control(
            'tab_text_color',
            [
                'label' => __('Text Color', 'sfilter'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sf-search-tabs .sf-tab-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'tab_bg_color',
            [
                'label' => __('Background Color', 'sfilter'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sf-search-tabs .sf-tab-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'tab_border_color',
            [
                'label' => __('Border Color', 'sfilter'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sf-search-tabs .sf-tab-button' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_active',
            [
                'label' => __('Active', 'sfilter'),
            ]
        );

        $this->add_control(
            'tab_active_text_color',
            [
                'label' => __('Text Color', 'sfilter'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sf-search-tabs .sf-tab-button.active' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'tab_active_bg_color',
            [
                'label' => __('Background Color', 'sfilter'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sf-search-tabs .sf-tab-button.active' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'tab_active_border_color',
            [
                'label' => __('Border Color', 'sfilter'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sf-search-tabs .sf-tab-button.active' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'tab_padding',
            [
                'label' => __('Padding', 'sfilter'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .sf-search-tabs .sf-tab-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Input Style
        $this->start_controls_section(
            'section_input_style',
            [
                'label' => __('Input Fields', 'sfilter'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'input_typography',
                'selector' => '{{WRAPPER}} .sf-search-input, {{WRAPPER}} .sf-search-textarea, {{WRAPPER}} .sf-search-select',
            ]
        );

        $this->add_control(
            'input_text_color',
            [
                'label' => __('Text Color', 'sfilter'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sf-search-input, {{WRAPPER}} .sf-search-textarea, {{WRAPPER}} .sf-search-select' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_placeholder_color',
            [
                'label' => __('Placeholder Color', 'sfilter'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sf-search-input::placeholder, {{WRAPPER}} .sf-search-textarea::placeholder' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_bg_color',
            [
                'label' => __('Background Color', 'sfilter'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sf-search-input, {{WRAPPER}} .sf-search-textarea, {{WRAPPER}} .sf-search-select' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'input_border',
                'selector' => '{{WRAPPER}} .sf-search-input, {{WRAPPER}} .sf-search-textarea, {{WRAPPER}} .sf-search-select, {{WRAPPER}} .sf-input-wrapper',
            ]
        );

        $this->add_responsive_control(
            'input_border_radius',
            [
                'label' => __('Border Radius', 'sfilter'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .sf-search-input, {{WRAPPER}} .sf-search-textarea, {{WRAPPER}} .sf-search-select, {{WRAPPER}} .sf-input-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'input_padding',
            [
                'label' => __('Padding', 'sfilter'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .sf-search-input, {{WRAPPER}} .sf-search-textarea, {{WRAPPER}} .sf-search-select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'textarea_height',
            [
                'label' => __('Textarea Height', 'sfilter'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .sf-search-textarea' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Button Style
        $this->start_controls_section(
            'section_button_style',
            [
                'label' => __('Search Button', 'sfilter'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .sf-search-button',
            ]
        );

        $this->start_controls_tabs('button_style_tabs');

        $this->start_controls_tab(
            'button_normal',
            [
                'label' => __('Normal', 'sfilter'),
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => __('Text Color', 'sfilter'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sf-search-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_color',
            [
                'label' => __('Background Color', 'sfilter'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sf-search-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'button_hover',
            [
                'label' => __('Hover', 'sfilter'),
            ]
        );

        $this->add_control(
            'button_hover_text_color',
            [
                'label' => __('Text Color', 'sfilter'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sf-search-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_bg_color',
            [
                'label' => __('Background Color', 'sfilter'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sf-search-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __('Padding', 'sfilter'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .sf-search-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => __('Border Radius', 'sfilter'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .sf-search-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Reset Link Style
        $this->start_controls_section(
            'section_reset_style',
            [
                'label' => __('Reset Link', 'sfilter'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'reset_typography',
                'selector' => '{{WRAPPER}} .sf-reset-link',
            ]
        );

        $this->add_control(
            'reset_text_color',
            [
                'label' => __('Text Color', 'sfilter'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sf-reset-link' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'reset_disabled_color',
            [
                'label' => __('Disabled Color', 'sfilter'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sf-reset-link.disabled' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        include __DIR__ . '/views/part-search.php';
    }

    /**
     * Render shortcode widget as plain content.
     *
     * @since 1.0.0
     * @access public
     */
    public function render_plain_content()
    {
        echo '';
    }

    /**
     * Render shortcode widget output in the editor.
     *
     * @since 1.3.0
     * @access protected
     */
    protected function content_template()
    {
    }
}
