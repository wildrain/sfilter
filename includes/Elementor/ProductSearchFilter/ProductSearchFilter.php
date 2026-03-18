<?php

namespace SFilter\Elementor\ProductSearchFilter;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH')) {
    exit;
}

class ProductSearchFilter extends Widget_Base
{
    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);

        $base_url  = SFILTER_URL . '/includes/Elementor/ProductSearchFilter/assets';
        $base_path = SFILTER_PATH . '/includes/Elementor/ProductSearchFilter/assets';

        wp_register_style(
            'msf-product-search-filter',
            $base_url . '/css/product-search-filter.css',
            [],
            file_exists($base_path . '/css/product-search-filter.css') ? filemtime($base_path . '/css/product-search-filter.css') : SFILTER_VERSION
        );

        wp_register_script(
            'msf-product-search-filter',
            $base_url . '/js/product-search-filter.js',
            ['jquery'],
            file_exists($base_path . '/js/product-search-filter.js') ? filemtime($base_path . '/js/product-search-filter.js') : SFILTER_VERSION,
            true
        );
    }

    public function get_name()
    {
        return 'sfilter-product-search-filter';
    }

    public function get_title()
    {
        return __('Product Search Filter', 'sfilter');
    }

    public function get_icon()
    {
        return 'eicon-search';
    }

    public function get_categories()
    {
        return ['sfilter-widgets'];
    }

    public function get_style_depends()
    {
        return ['msf-product-search-filter'];
    }

    public function get_script_depends()
    {
        return ['msf-product-search-filter'];
    }

    protected function register_controls()
    {
        $this->register_content_controls();
        $this->register_style_controls();
    }

    protected function register_content_controls()
    {
        // --- Search & Filters Section ---
        $this->start_controls_section('section_search_filters', [
            'label' => __('Search & Filters', 'sfilter'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('filter_position', [
            'label'   => __('Filter Position', 'sfilter'),
            'type'    => Controls_Manager::SELECT,
            'options' => [
                'top'   => __('Top (Horizontal)', 'sfilter'),
                'left'  => __('Left Sidebar', 'sfilter'),
                'right' => __('Right Sidebar', 'sfilter'),
            ],
            'default' => 'top',
        ]);

        $this->add_control('enable_search', [
            'label'        => __('Enable Search', 'sfilter'),
            'type'         => Controls_Manager::SWITCHER,
            'default'      => 'yes',
        ]);

        $this->add_control('search_placeholder', [
            'label'     => __('Search Placeholder', 'sfilter'),
            'type'      => Controls_Manager::TEXT,
            'default'   => __('Search products...', 'sfilter'),
            'condition' => ['enable_search' => 'yes'],
        ]);

        $this->add_control('enable_taxonomy_filters', [
            'label'   => __('Enable Taxonomy Filters', 'sfilter'),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $taxonomy_options = $this->get_taxonomy_options();

        $repeater = new \Elementor\Repeater();

        $repeater->add_control('taxonomy', [
            'label'   => __('Taxonomy', 'sfilter'),
            'type'    => Controls_Manager::SELECT,
            'options' => $taxonomy_options,
            'default' => 'product_cat',
        ]);

        $repeater->add_control('display_type', [
            'label'   => __('Display Type', 'sfilter'),
            'type'    => Controls_Manager::SELECT,
            'options' => [
                'select'   => __('Dropdown', 'sfilter'),
                'radio'    => __('Radio Buttons', 'sfilter'),
                'checkbox' => __('Checkboxes', 'sfilter'),
            ],
            'default' => 'select',
        ]);

        $repeater->add_control('label', [
            'label'   => __('Label', 'sfilter'),
            'type'    => Controls_Manager::TEXT,
            'default' => __('Category', 'sfilter'),
        ]);

        $this->add_control('taxonomy_filters', [
            'label'     => __('Taxonomy Filters', 'sfilter'),
            'type'      => Controls_Manager::REPEATER,
            'fields'    => $repeater->get_controls(),
            'default'   => [
                [
                    'taxonomy'     => 'product_cat',
                    'display_type' => 'select',
                    'label'        => __('Category', 'sfilter'),
                ],
            ],
            'title_field' => '{{{ label }}}',
            'condition'   => ['enable_taxonomy_filters' => 'yes'],
        ]);

        $this->add_control('toggle_icon_position', [
            'label'   => __('Toggle Icon Position', 'sfilter'),
            'type'    => Controls_Manager::SELECT,
            'options' => [
                'left'  => __('Left', 'sfilter'),
                'right' => __('Right', 'sfilter'),
            ],
            'default'   => 'right',
            'condition' => ['enable_taxonomy_filters' => 'yes'],
        ]);

        $this->add_control('enable_price_filter', [
            'label'   => __('Enable Price Filter', 'sfilter'),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('price_step', [
            'label'     => __('Price Step', 'sfilter'),
            'type'      => Controls_Manager::NUMBER,
            'default'   => 1,
            'min'       => 1,
            'condition' => ['enable_price_filter' => 'yes'],
        ]);

        $this->add_control('enable_sort', [
            'label'   => __('Enable Sort', 'sfilter'),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('sort_options', [
            'label'    => __('Sort Options', 'sfilter'),
            'type'     => Controls_Manager::SELECT2,
            'multiple' => true,
            'options'  => [
                'date'       => __('Latest', 'sfilter'),
                'price_asc'  => __('Price: Low to High', 'sfilter'),
                'price_desc' => __('Price: High to Low', 'sfilter'),
                'title_asc'  => __('Name: A to Z', 'sfilter'),
                'title_desc' => __('Name: Z to A', 'sfilter'),
                'popularity' => __('Popularity', 'sfilter'),
                'rating'     => __('Average Rating', 'sfilter'),
            ],
            'default'   => ['date', 'price_asc', 'price_desc', 'title_asc', 'popularity', 'rating'],
            'condition' => ['enable_sort' => 'yes'],
        ]);

        $this->add_control('default_sort', [
            'label'   => __('Default Sort', 'sfilter'),
            'type'    => Controls_Manager::SELECT,
            'options' => [
                'date'       => __('Latest', 'sfilter'),
                'price_asc'  => __('Price: Low to High', 'sfilter'),
                'price_desc' => __('Price: High to Low', 'sfilter'),
                'title_asc'  => __('Name: A to Z', 'sfilter'),
                'title_desc' => __('Name: Z to A', 'sfilter'),
                'popularity' => __('Popularity', 'sfilter'),
                'rating'     => __('Average Rating', 'sfilter'),
            ],
            'default'   => 'date',
            'condition' => ['enable_sort' => 'yes'],
        ]);

        $this->add_control('enable_reset_button', [
            'label'   => __('Enable Reset Button', 'sfilter'),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('reset_button_text', [
            'label'     => __('Reset Button Text', 'sfilter'),
            'type'      => Controls_Manager::TEXT,
            'default'   => __('Reset Filters', 'sfilter'),
            'condition' => ['enable_reset_button' => 'yes'],
        ]);

        $this->add_control('reset_button_style', [
            'label'   => __('Reset Button Style', 'sfilter'),
            'type'    => Controls_Manager::SELECT,
            'options' => [
                'button' => __('Button', 'sfilter'),
                'text'   => __('Underlined Text', 'sfilter'),
            ],
            'default'   => 'button',
            'condition' => ['enable_reset_button' => 'yes'],
        ]);

        $this->end_controls_section();

        // --- Product Display Section ---
        $this->start_controls_section('section_product_display', [
            'label' => __('Product Display', 'sfilter'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('default_view', [
            'label'   => __('Default View', 'sfilter'),
            'type'    => Controls_Manager::SELECT,
            'options' => [
                'grid' => __('Grid', 'sfilter'),
                'list' => __('List', 'sfilter'),
            ],
            'default' => 'grid',
        ]);

        $this->add_control('enable_view_toggle', [
            'label'   => __('Enable View Toggle', 'sfilter'),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('grid_columns', [
            'label'   => __('Grid Columns', 'sfilter'),
            'type'    => Controls_Manager::SELECT,
            'options' => [
                '2' => '2',
                '3' => '3',
                '4' => '4',
            ],
            'default' => '3',
        ]);

        $this->add_control('show_thumbnail', [
            'label'   => __('Show Thumbnail', 'sfilter'),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('show_title', [
            'label'   => __('Show Title', 'sfilter'),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('show_price', [
            'label'   => __('Show Price', 'sfilter'),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('show_description', [
            'label'   => __('Show Description', 'sfilter'),
            'type'    => Controls_Manager::SWITCHER,
            'default' => '',
        ]);

        $this->add_control('show_category_badge', [
            'label'   => __('Show Category Badge', 'sfilter'),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('show_tags', [
            'label'   => __('Show Tags', 'sfilter'),
            'type'    => Controls_Manager::SWITCHER,
            'default' => '',
        ]);

        $this->add_control('show_rating', [
            'label'   => __('Show Rating', 'sfilter'),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('show_stock_status', [
            'label'   => __('Show Stock Status', 'sfilter'),
            'type'    => Controls_Manager::SWITCHER,
            'default' => '',
        ]);

        $this->add_control('show_add_to_cart', [
            'label'   => __('Show Add to Cart', 'sfilter'),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

        $this->add_control('cart_button_type', [
            'label'   => __('Cart Button Type', 'sfilter'),
            'type'    => Controls_Manager::SELECT,
            'options' => [
                'text' => __('Text', 'sfilter'),
                'icon' => __('Icon', 'sfilter'),
            ],
            'default'   => 'text',
            'condition' => ['show_add_to_cart' => 'yes'],
        ]);

        $this->add_control('button_text', [
            'label'     => __('Button Text', 'sfilter'),
            'type'      => Controls_Manager::TEXT,
            'default'   => __('Add to Cart', 'sfilter'),
            'condition' => ['show_add_to_cart' => 'yes', 'cart_button_type' => 'text'],
        ]);

        $this->add_control('show_quantity_selector', [
            'label'     => __('Show Quantity Selector', 'sfilter'),
            'type'      => Controls_Manager::SWITCHER,
            'default'   => '',
            'condition' => ['show_add_to_cart' => 'yes'],
        ]);

        $this->add_control('button_position', [
            'label'   => __('Button Position', 'sfilter'),
            'type'    => Controls_Manager::SELECT,
            'options' => [
                'left'  => __('Left', 'sfilter'),
                'right' => __('Right', 'sfilter'),
            ],
            'default'   => 'right',
            'condition' => ['show_add_to_cart' => 'yes'],
        ]);

        $this->end_controls_section();

        // --- Pagination Section ---
        $this->start_controls_section('section_pagination', [
            'label' => __('Pagination', 'sfilter'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('pagination_type', [
            'label'   => __('Pagination Type', 'sfilter'),
            'type'    => Controls_Manager::SELECT,
            'options' => [
                'numbered'        => __('Numbered', 'sfilter'),
                'load_more'       => __('Load More Button', 'sfilter'),
                'infinite_scroll' => __('Infinite Scroll', 'sfilter'),
            ],
            'default' => 'numbered',
        ]);

        $this->add_control('posts_per_page', [
            'label'   => __('Products Per Page', 'sfilter'),
            'type'    => Controls_Manager::NUMBER,
            'default' => 12,
            'min'     => 1,
            'max'     => 100,
        ]);

        $this->add_control('load_more_text', [
            'label'     => __('Load More Text', 'sfilter'),
            'type'      => Controls_Manager::TEXT,
            'default'   => __('Load More', 'sfilter'),
            'condition' => ['pagination_type' => 'load_more'],
        ]);

        $this->end_controls_section();

        // --- URL Sync Section ---
        $this->start_controls_section('section_url_sync', [
            'label' => __('URL Sync', 'sfilter'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('enable_url_sync', [
            'label'   => __('Enable URL Sync', 'sfilter'),
            'type'    => Controls_Manager::SWITCHER,
            'default' => '',
        ]);

        $this->end_controls_section();
    }

    protected function register_style_controls()
    {
        // --- Filter Bar Style ---
        $this->start_controls_section('style_filter_bar', [
            'label' => __('Filter Bar', 'sfilter'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('filter_sidebar_width', [
            'label'      => __('Sidebar Width', 'sfilter'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range'      => [
                'px' => ['min' => 200, 'max' => 600],
                '%'  => ['min' => 15, 'max' => 40],
            ],
            'default'    => ['size' => 280, 'unit' => 'px'],
            'selectors'  => [
                '{{WRAPPER}} .msf-wrapper--filter-left' => 'grid-template-columns: {{SIZE}}{{UNIT}} 1fr;',
                '{{WRAPPER}} .msf-wrapper--filter-right' => 'grid-template-columns: 1fr {{SIZE}}{{UNIT}};',
            ],
            'condition'  => [
                'filter_position' => ['left', 'right'],
            ],
        ]);

        $this->add_control('filter_bar_bg', [
            'label'     => __('Background Color', 'sfilter'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .msf-filter-bar' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('dropdown_bg_color', [
            'label'     => __('Dropdown Background', 'sfilter'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .msf-wrapper--filter-top .msf-term-list--children' => 'background-color: {{VALUE}};',
            ],
            'condition' => ['filter_position' => 'top'],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'filter_typography',
            'label'    => __('Typography', 'sfilter'),
            'selector' => '{{WRAPPER}} .msf-filter-bar',
        ]);

        $this->add_control('filter_label_color', [
            'label'     => __('Label Color', 'sfilter'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .msf-filter-bar__label' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('filter_input_bg', [
            'label'     => __('Input Background', 'sfilter'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .msf-filter-bar input, {{WRAPPER}} .msf-filter-bar select' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('filter_input_color', [
            'label'     => __('Input Text Color', 'sfilter'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .msf-filter-bar input, {{WRAPPER}} .msf-filter-bar select' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('filter_input_border_radius', [
            'label'      => __('Input Border Radius', 'sfilter'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors'  => [
                '{{WRAPPER}} .msf-filter-bar input, {{WRAPPER}} .msf-filter-bar select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('filter_bar_padding', [
            'label'      => __('Container Padding', 'sfilter'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em'],
            'selectors'  => [
                '{{WRAPPER}} .msf-filter-bar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        // --- Product Card Style ---
        $this->start_controls_section('style_product_card', [
            'label' => __('Product Card', 'sfilter'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('card_bg', [
            'label'     => __('Background', 'sfilter'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .msf-product-card' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            'name'     => 'card_border',
            'selector' => '{{WRAPPER}} .msf-product-card',
        ]);

        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name'     => 'card_shadow',
            'selector' => '{{WRAPPER}} .msf-product-card',
        ]);

        $this->add_control('card_border_radius', [
            'label'      => __('Border Radius', 'sfilter'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors'  => [
                '{{WRAPPER}} .msf-product-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('card_padding', [
            'label'      => __('Card Padding', 'sfilter'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em'],
            'selectors'  => [
                '{{WRAPPER}} .msf-product-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('card_gap', [
            'label'      => __('Gap Between Cards', 'sfilter'),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range'      => ['px' => ['min' => 0, 'max' => 60]],
            'selectors'  => [
                '{{WRAPPER}} .msf-products' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('card_image_border_radius', [
            'label'      => __('Image Border Radius', 'sfilter'),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors'  => [
                '{{WRAPPER}} .msf-product-card__image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'card_title_typography',
            'label'    => __('Title Typography', 'sfilter'),
            'selector' => '{{WRAPPER}} .msf-product-card__title',
        ]);

        $this->add_control('card_title_color', [
            'label'     => __('Title Color', 'sfilter'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .msf-product-card__title a' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'card_price_typography',
            'label'    => __('Price Typography', 'sfilter'),
            'selector' => '{{WRAPPER}} .msf-product-card__price',
        ]);

        $this->add_control('card_price_color', [
            'label'     => __('Price Color', 'sfilter'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .msf-product-card__price' => 'color: {{VALUE}};',
                '{{WRAPPER}} .msf-product-card__quantity' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'card_desc_typography',
            'label'    => __('Description Typography', 'sfilter'),
            'selector' => '{{WRAPPER}} .msf-product-card__description',
        ]);

        $this->add_control('card_desc_color', [
            'label'     => __('Description Color', 'sfilter'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .msf-product-card__description' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('card_badge_bg', [
            'label'     => __('Badge Background', 'sfilter'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .msf-product-card__badge' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('card_badge_color', [
            'label'     => __('Badge Text Color', 'sfilter'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .msf-product-card__badge' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('card_button_bg', [
            'label'     => __('Button Background', 'sfilter'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .msf-product-card__button' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('card_button_color', [
            'label'     => __('Button Text Color', 'sfilter'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .msf-product-card__button' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        // --- Pagination Style ---
        $this->start_controls_section('style_pagination', [
            'label' => __('Pagination', 'sfilter'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name'     => 'pagination_typography',
            'label'    => __('Typography', 'sfilter'),
            'selector' => '{{WRAPPER}} .msf-pagination',
        ]);

        $this->add_control('pagination_color', [
            'label'     => __('Color', 'sfilter'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .msf-pagination__page, {{WRAPPER}} .msf-pagination__load-more' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('pagination_active_color', [
            'label'     => __('Active Color', 'sfilter'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .msf-pagination__page--active' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('pagination_active_bg', [
            'label'     => __('Active Background', 'sfilter'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .msf-pagination__page--active' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('pagination_button_bg', [
            'label'     => __('Button Background', 'sfilter'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .msf-pagination__page, {{WRAPPER}} .msf-pagination__load-more' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        if (!class_exists('WooCommerce')) {
            echo '<p>' . esc_html__('WooCommerce is required for this widget.', 'sfilter') . '</p>';
            return;
        }

        $settings  = $this->get_settings_for_display();
        $widget_id = $this->get_id();

        $query_params = [
            'search'         => isset($_GET['msf_search']) ? sanitize_text_field($_GET['msf_search']) : '',
            'search_type'    => isset($_GET['msf_search_type']) ? sanitize_text_field($_GET['msf_search_type']) : '',
            'price_min'      => isset($_GET['msf_price_min']) ? sanitize_text_field($_GET['msf_price_min']) : '',
            'price_max'      => isset($_GET['msf_price_max']) ? sanitize_text_field($_GET['msf_price_max']) : '',
            'orderby'        => isset($_GET['msf_sort']) ? sanitize_text_field($_GET['msf_sort']) : (!empty($settings['default_sort']) ? $settings['default_sort'] : 'date'),
            'posts_per_page' => !empty($settings['posts_per_page']) ? intval($settings['posts_per_page']) : 12,
            'paged'          => isset($_GET['msf_page']) ? intval($_GET['msf_page']) : 1,
        ];

        // Restore taxonomy filters from URL
        $url_taxonomies = [];
        foreach ($_GET as $key => $value) {
            if (strpos($key, 'msf_tax_') === 0 && !empty($value)) {
                $tax_name = sanitize_text_field(substr($key, 8));
                $terms = array_map('sanitize_text_field', explode(',', $value));
                $url_taxonomies[$tax_name] = $terms;
            }
        }
        if (!empty($url_taxonomies)) {
            $query_params['taxonomies'] = $url_taxonomies;
        }

        $query = ProductSearchFilter_Query::query($query_params);

        // Settings to pass to JS
        $js_settings = [
            'posts_per_page'   => $query_params['posts_per_page'],
            'default_sort'     => $query_params['orderby'],
            'default_view'     => !empty($settings['default_view']) ? $settings['default_view'] : 'grid',
            'grid_columns'     => !empty($settings['grid_columns']) ? $settings['grid_columns'] : '3',
            'pagination_type'  => !empty($settings['pagination_type']) ? $settings['pagination_type'] : 'numbered',
            'load_more_text'   => !empty($settings['load_more_text']) ? $settings['load_more_text'] : __('Load More', 'sfilter'),
            'enable_url_sync'  => !empty($settings['enable_url_sync']) && $settings['enable_url_sync'] === 'yes',
            'filter_position'  => !empty($settings['filter_position']) ? $settings['filter_position'] : 'top',
            'show_quantity_selector' => !empty($settings['show_quantity_selector']) && $settings['show_quantity_selector'] === 'yes' ? 'yes' : '',
            'cart_button_type'      => !empty($settings['cart_button_type']) ? $settings['cart_button_type'] : 'text',
            'toggle_icon_position'  => !empty($settings['toggle_icon_position']) ? $settings['toggle_icon_position'] : 'right',
            'button_text'           => !empty($settings['button_text']) ? $settings['button_text'] : __('Add to Cart', 'sfilter'),
            'button_position'       => !empty($settings['button_position']) ? $settings['button_position'] : 'right',
        ];

        include __DIR__ . '/templates/wrapper.php';

        wp_reset_postdata();
    }

    private function get_taxonomy_options()
    {
        if (!function_exists('get_object_taxonomies')) {
            return [];
        }

        $taxonomies = get_object_taxonomies('product', 'objects');
        $options = [];

        foreach ($taxonomies as $taxonomy) {
            if (!$taxonomy->public || $taxonomy->name === 'product_type' || $taxonomy->name === 'product_visibility') {
                continue;
            }
            $options[$taxonomy->name] = $taxonomy->label;
        }

        return $options;
    }
}
