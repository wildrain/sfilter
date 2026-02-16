<?php

namespace SFilter;

class Generator
{
    private $taxonomies = [
        'sf_type' => [
            'singular' => 'Type',
            'plural'   => 'Types',
            'slug'     => 'filter-type'
        ],
        'sf_outer_dimension' => [
            'singular' => 'Outer Diameter',
            'plural'   => 'Outer Diameters',
            'slug'     => 'outer-diameter'
        ],
        'sf_inner_diameter' => [
            'singular' => 'Inner Diameter',
            'plural'   => 'Inner Diameters',
            'slug'     => 'inner-diameter'
        ],
        'sf_thread_size' => [
            'singular' => 'Thread Size',
            'plural'   => 'Thread Sizes',
            'slug'     => 'thread-size'
        ],
        'sf_length' => [
            'singular' => 'Length',
            'plural'   => 'Lengths',
            'slug'     => 'filter-length'
        ],
        'sf_overall_length' => [
            'singular' => 'Overall Length',
            'plural'   => 'Overall Lengths',
            'slug'     => 'overall-length'
        ],
        'sf_width' => [
            'singular' => 'Width',
            'plural'   => 'Widths',
            'slug'     => 'filter-width'
        ],
        'sf_height' => [
            'singular' => 'Height',
            'plural'   => 'Heights',
            'slug'     => 'filter-height'
        ],
        'sf_thickness' => [
            'singular' => 'Thickness',
            'plural'   => 'Thicknesses',
            'slug'     => 'filter-thickness'
        ],
        'sf_bolt_hole_diameter' => [
            'singular' => 'Bolt Hole Diameter',
            'plural'   => 'Bolt Hole Diameters',
            'slug'     => 'bolt-hole-diameter'
        ],
        'sf_gasket_od' => [
            'singular' => 'Gasket OD',
            'plural'   => 'Gasket ODs',
            'slug'     => 'gasket-od'
        ],
        'sf_collapse_burst' => [
            'singular' => 'Collapse Burst',
            'plural'   => 'Collapse Bursts',
            'slug'     => 'collapse-burst'
        ],
        'sf_media_material' => [
            'singular' => 'Media Material',
            'plural'   => 'Media Materials',
            'slug'     => 'media-material'
        ],
        'sf_structure_material' => [
            'singular' => 'Structure Material',
            'plural'   => 'Structure Materials',
            'slug'     => 'structure-material'
        ],
        'sf_filter_type' => [
            'singular' => 'Type of Filter',
            'plural'   => 'Types of Filter',
            'slug'     => 'type-of-filter'
        ],
        'sf_style' => [
            'singular' => 'Style',
            'plural'   => 'Styles',
            'slug'     => 'filter-style'
        ],
        'sf_bypass_valve' => [
            'singular' => 'Bypass Valve',
            'plural'   => 'Bypass Valves',
            'slug'     => 'bypass-valve'
        ],
        'sf_bypass_valve_setting' => [
            'singular' => 'Bypass Valve Setting',
            'plural'   => 'Bypass Valve Settings',
            'slug'     => 'bypass-valve-setting'
        ],
    ];

    public function __construct()
    {
        add_action('init', [$this, 'register_taxonomies']);
        add_action('init', [$this, 'register_term_meta']);
    }

    public function register_taxonomies()
    {
        foreach ($this->taxonomies as $taxonomy => $labels) {
            register_extended_taxonomy(
                $taxonomy,
                'product',
                [
                    'dashboard_glance' => false,
                    'show_in_rest'     => true,
                    'hierarchical'     => false,
                ],
                [
                    'singular' => $labels['singular'],
                    'plural'   => $labels['plural'],
                    'slug'     => $labels['slug'],
                ]
            );
        }
    }

    public function register_term_meta()
    {
        foreach (array_keys($this->taxonomies) as $taxonomy) {
            register_term_meta($taxonomy, 'translation', [
                'type'              => 'string',
                'single'            => true,
                'show_in_rest'      => true,
                'sanitize_callback' => 'sanitize_text_field',
            ]);
        }
    }

    public function get_taxonomies()
    {
        return $this->taxonomies;
    }
}
