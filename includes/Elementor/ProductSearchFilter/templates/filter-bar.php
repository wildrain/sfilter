<?php
if (!defined('ABSPATH')) {
    exit;
}

// --- Hierarchical term helper functions ---
if (!function_exists('msf_build_term_tree')) {
    /**
     * Build a nested tree from a flat array of term objects.
     */
    function msf_build_term_tree($terms, $parent_id = 0) {
        $tree = [];
        foreach ($terms as $term) {
            if ((int) $term->parent === (int) $parent_id) {
                $term->children = msf_build_term_tree($terms, $term->term_id);
                $tree[] = $term;
            }
        }
        return $tree;
    }
}

if (!function_exists('msf_render_select_options')) {
    /**
     * Recursively render <option> elements with depth-based indentation.
     */
    function msf_render_select_options($terms, $depth = 0) {
        $prefix = str_repeat('— ', $depth);
        foreach ($terms as $term) : ?>
            <option value="<?php echo esc_attr($term->slug); ?>"><?php echo esc_html($prefix . $term->name); ?> (<?php echo esc_html($term->count); ?>)</option>
            <?php if (!empty($term->children)) {
                msf_render_select_options($term->children, $depth + 1);
            }
        endforeach;
    }
}

if (!function_exists('msf_render_radio_options')) {
    /**
     * Recursively render radio options as ul > li tree with collapsible children.
     */
    function msf_render_radio_options($terms, $taxonomy, $toggle_position = 'right', $depth = 0) {
        echo '<ul class="msf-term-list' . ($depth > 0 ? ' msf-term-list--children' : '') . '">';
        foreach ($terms as $term) :
            $has_children = !empty($term->children);
            $option_class = 'msf-filter-bar__option';
            if ($has_children && $toggle_position === 'right') {
                $option_class .= ' msf-filter-bar__option--toggle-right';
            }
        ?>
            <li class="msf-term-item<?php echo $has_children ? ' msf-term-item--has-children' : ''; ?>">
                <label class="<?php echo esc_attr($option_class); ?>">
                    <?php if ($has_children && $toggle_position === 'left') : ?>
                        <span class="msf-term-toggle">+</span>
                    <?php endif; ?>
                    <input type="radio" name="msf_tax_<?php echo esc_attr($taxonomy); ?>" class="msf-taxonomy-filter" data-taxonomy="<?php echo esc_attr($taxonomy); ?>" value="<?php echo esc_attr($term->slug); ?>" />
                    <span class="msf-filter-bar__option-text"><?php echo esc_html($term->name); ?> (<?php echo esc_html($term->count); ?>)</span>
                    <?php if ($has_children && $toggle_position === 'right') : ?>
                        <span class="msf-term-toggle">+</span>
                    <?php endif; ?>
                </label>
                <?php if ($has_children) {
                    msf_render_radio_options($term->children, $taxonomy, $toggle_position, $depth + 1);
                } ?>
            </li>
        <?php endforeach;
        echo '</ul>';
    }
}

if (!function_exists('msf_render_checkbox_options')) {
    /**
     * Recursively render checkbox options as ul > li tree with collapsible children.
     */
    function msf_render_checkbox_options($terms, $taxonomy, $toggle_position = 'right', $depth = 0) {
        echo '<ul class="msf-term-list' . ($depth > 0 ? ' msf-term-list--children' : '') . '">';
        foreach ($terms as $term) :
            $has_children = !empty($term->children);
            $option_class = 'msf-filter-bar__option';
            if ($has_children && $toggle_position === 'right') {
                $option_class .= ' msf-filter-bar__option--toggle-right';
            }
        ?>
            <li class="msf-term-item<?php echo $has_children ? ' msf-term-item--has-children' : ''; ?>">
                <label class="<?php echo esc_attr($option_class); ?>">
                    <?php if ($has_children && $toggle_position === 'left') : ?>
                        <span class="msf-term-toggle">+</span>
                    <?php endif; ?>
                    <input type="checkbox" class="msf-taxonomy-filter" data-taxonomy="<?php echo esc_attr($taxonomy); ?>" value="<?php echo esc_attr($term->slug); ?>" />
                    <span class="msf-filter-bar__option-text"><?php echo esc_html($term->name); ?> (<?php echo esc_html($term->count); ?>)</span>
                    <?php if ($has_children && $toggle_position === 'right') : ?>
                        <span class="msf-term-toggle">+</span>
                    <?php endif; ?>
                </label>
                <?php if ($has_children) {
                    msf_render_checkbox_options($term->children, $taxonomy, $toggle_position, $depth + 1);
                } ?>
            </li>
        <?php endforeach;
        echo '</ul>';
    }
}

$filter_position   = !empty($settings['filter_position']) ? $settings['filter_position'] : 'top';
$toggle_position   = !empty($settings['toggle_icon_position']) ? $settings['toggle_icon_position'] : 'right';
?>
<div class="msf-filter-bar">
    <?php if (!empty($settings['enable_taxonomy_filters']) && $settings['enable_taxonomy_filters'] === 'yes' && !empty($settings['taxonomy_filters'])) : ?>
        <?php foreach ($settings['taxonomy_filters'] as $filter) :
            $taxonomy    = !empty($filter['taxonomy']) ? $filter['taxonomy'] : '';
            $display     = !empty($filter['display_type']) ? $filter['display_type'] : 'select';
            $label       = !empty($filter['label']) ? $filter['label'] : '';
            if (empty($taxonomy)) continue;

            $terms = get_terms([
                'taxonomy'   => $taxonomy,
                'hide_empty' => true,
            ]);
            if (is_wp_error($terms) || empty($terms)) continue;

            $term_tree = msf_build_term_tree($terms);
        ?>
            <div class="msf-filter-bar__taxonomy" data-taxonomy="<?php echo esc_attr($taxonomy); ?>">
                <?php if ($label) : ?>
                    <label class="msf-filter-bar__label"><?php echo esc_html($label); ?></label>
                <?php endif; ?>

                <?php if ($display === 'select') : ?>
                    <select class="msf-taxonomy-filter" data-taxonomy="<?php echo esc_attr($taxonomy); ?>">
                        <option value=""><?php printf(esc_html__('All %s', 'sfilter'), esc_html($label ?: get_taxonomy($taxonomy)->label)); ?></option>
                        <?php msf_render_select_options($term_tree); ?>
                    </select>

                <?php elseif ($display === 'radio') : ?>
                    <div class="msf-taxonomy-radios">
                        <ul class="msf-term-list">
                            <li class="msf-term-item">
                                <label class="msf-filter-bar__option">
                                    <input type="radio" name="msf_tax_<?php echo esc_attr($taxonomy); ?>" class="msf-taxonomy-filter" data-taxonomy="<?php echo esc_attr($taxonomy); ?>" value="" checked />
                                    <?php printf(esc_html__('All', 'sfilter')); ?>
                                </label>
                            </li>
                        </ul>
                        <?php msf_render_radio_options($term_tree, $taxonomy, $toggle_position); ?>
                    </div>

                <?php elseif ($display === 'checkbox') : ?>
                    <div class="msf-taxonomy-checkboxes">
                        <?php msf_render_checkbox_options($term_tree, $taxonomy, $toggle_position); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($settings['enable_price_filter']) && $settings['enable_price_filter'] === 'yes') :
        $price_range = \SFilter\Elementor\ProductSearchFilter\ProductSearchFilter_Query::get_price_range();
        $step = !empty($settings['price_step']) ? intval($settings['price_step']) : 1;
        $price_min = intval($price_range['min']);
        $price_max = intval($price_range['max']);
        $currency_symbol = function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : 'kr';
    ?>
        <div class="msf-filter-bar__price">
            <label class="msf-filter-bar__label"><?php esc_html_e('Price', 'sfilter'); ?></label>
            <div class="msf-price-range-wrapper" data-min="<?php echo esc_attr($price_min); ?>" data-max="<?php echo esc_attr($price_max); ?>" data-step="<?php echo esc_attr($step); ?>">
                <div class="msf-price-range-track">
                    <div class="msf-price-range-fill"></div>
                </div>
                <input type="range" class="msf-price-range msf-price-range-min" min="<?php echo esc_attr($price_min); ?>" max="<?php echo esc_attr($price_max); ?>" value="<?php echo esc_attr($price_min); ?>" step="<?php echo esc_attr($step); ?>" />
                <input type="range" class="msf-price-range msf-price-range-max" min="<?php echo esc_attr($price_min); ?>" max="<?php echo esc_attr($price_max); ?>" value="<?php echo esc_attr($price_max); ?>" step="<?php echo esc_attr($step); ?>" />
            </div>
            <div class="msf-price-range-values">
                <span class="msf-price-range-label-min"><?php echo esc_html($currency_symbol); ?> <?php echo esc_html($price_min); ?></span>
                <span class="msf-price-range-label-max"><?php echo esc_html($currency_symbol); ?> <?php echo esc_html($price_max); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($settings['enable_sort']) && $settings['enable_sort'] === 'yes') :
        $sort_options = !empty($settings['sort_options']) ? $settings['sort_options'] : ['date', 'price_asc', 'price_desc', 'title_asc', 'popularity', 'rating'];
        $default_sort = !empty($settings['default_sort']) ? $settings['default_sort'] : 'date';
        $sort_labels = [
            'date'       => __('Latest', 'sfilter'),
            'price_asc'  => __('Price: Low to High', 'sfilter'),
            'price_desc' => __('Price: High to Low', 'sfilter'),
            'title_asc'  => __('Name: A to Z', 'sfilter'),
            'title_desc' => __('Name: Z to A', 'sfilter'),
            'popularity' => __('Popularity', 'sfilter'),
            'rating'     => __('Average Rating', 'sfilter'),
        ];
    ?>
        <div class="msf-filter-bar__sort">
            <select class="msf-sort-select">
                <?php foreach ($sort_options as $option) :
                    if (!isset($sort_labels[$option])) continue;
                ?>
                    <option value="<?php echo esc_attr($option); ?>" <?php selected($option, $default_sort); ?>>
                        <?php echo esc_html($sort_labels[$option]); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endif; ?>

    <?php if (!empty($settings['enable_view_toggle']) && $settings['enable_view_toggle'] === 'yes') :
        $default_view = !empty($settings['default_view']) ? $settings['default_view'] : 'grid';
    ?>
        <div class="msf-filter-bar__view-toggle">
            <button type="button" class="msf-view-btn msf-view-btn--grid <?php echo $default_view === 'grid' ? 'msf-view-btn--active' : ''; ?>" data-view="grid" title="<?php esc_attr_e('Grid View', 'sfilter'); ?>">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><rect x="0" y="0" width="7" height="7"/><rect x="9" y="0" width="7" height="7"/><rect x="0" y="9" width="7" height="7"/><rect x="9" y="9" width="7" height="7"/></svg>
            </button>
            <button type="button" class="msf-view-btn msf-view-btn--list <?php echo $default_view === 'list' ? 'msf-view-btn--active' : ''; ?>" data-view="list" title="<?php esc_attr_e('List View', 'sfilter'); ?>">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><rect x="0" y="0" width="16" height="4"/><rect x="0" y="6" width="16" height="4"/><rect x="0" y="12" width="16" height="4"/></svg>
            </button>
        </div>
    <?php endif; ?>

    <?php if (!empty($settings['enable_search']) && $settings['enable_search'] === 'yes') : ?>
        <div class="msf-filter-bar__search">
            <input
                type="text"
                class="msf-search-input"
                placeholder="<?php echo esc_attr(!empty($settings['search_placeholder']) ? $settings['search_placeholder'] : __('Search products...', 'sfilter')); ?>"
                value=""
            />
        </div>
    <?php endif; ?>

    <?php if (!empty($settings['enable_reset_button']) && $settings['enable_reset_button'] === 'yes') :
        $reset_style = !empty($settings['reset_button_style']) ? $settings['reset_button_style'] : 'button';
    ?>
        <div class="msf-filter-bar__reset">
            <button type="button" class="msf-reset-btn<?php echo $reset_style === 'text' ? ' msf-reset-btn--text' : ''; ?>">
                <?php echo esc_html(!empty($settings['reset_button_text']) ? $settings['reset_button_text'] : __('Reset Filters', 'sfilter')); ?>
            </button>
        </div>
    <?php endif; ?>
</div>
