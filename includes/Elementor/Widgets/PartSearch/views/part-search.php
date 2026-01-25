<?php
/**
 * Part Search Widget Template
 *
 * @package SFilter\Elementor\Widgets\PartSearch
 */

if (!defined('ABSPATH')) {
    exit;
}

$widget_id = $this->get_id();
$shop_url = !empty($settings['shop_page_url']) ? esc_url($settings['shop_page_url']) : '/shop';
$search_param = !empty($settings['search_param']) ? esc_attr($settings['search_param']) : 'item';
$max_parts = !empty($settings['max_parts']) ? intval($settings['max_parts']) : 15;
$max_parts_error = !empty($settings['max_parts_error']) ? str_replace('{max}', $max_parts, $settings['max_parts_error']) : '';

// Tab visibility
$tab1_enabled = $settings['tab1_enabled'] === 'yes';
$tab2_enabled = $settings['tab2_enabled'] === 'yes';
$tab3_enabled = $settings['tab3_enabled'] === 'yes';
$tab4_enabled = $settings['tab4_enabled'] === 'yes';

// Find first enabled tab
$first_active = 1;
if (!$tab1_enabled && $tab2_enabled) $first_active = 2;
elseif (!$tab1_enabled && !$tab2_enabled && $tab3_enabled) $first_active = 3;
elseif (!$tab1_enabled && !$tab2_enabled && !$tab3_enabled && $tab4_enabled) $first_active = 4;

// Parse attribute fields
$attribute_fields = !empty($settings['attribute_fields']) ? $settings['attribute_fields'] : [];

// Parse equipment options
$make_options = !empty($settings['equipment_make_options']) ? explode("\n", $settings['equipment_make_options']) : [];
$model_options = !empty($settings['equipment_model_options']) ? explode("\n", $settings['equipment_model_options']) : [];
$year_options = !empty($settings['equipment_year_options']) ? explode("\n", $settings['equipment_year_options']) : [];
?>

<div class="sf-search-wrapper"
     data-widget-id="<?php echo esc_attr($widget_id); ?>"
     data-shop-url="<?php echo esc_attr($shop_url); ?>"
     data-search-param="<?php echo esc_attr($search_param); ?>"
     data-max-parts="<?php echo esc_attr($max_parts); ?>">

    <!-- Tabs Navigation -->
    <div class="sf-search-tabs">
        <?php if ($tab1_enabled) : ?>
            <button type="button" class="sf-tab-button <?php echo $first_active === 1 ? 'active' : ''; ?>" data-tab="part-search">
                <?php echo esc_html($settings['tab1_label']); ?>
            </button>
        <?php endif; ?>

        <?php if ($tab2_enabled) : ?>
            <button type="button" class="sf-tab-button <?php echo $first_active === 2 ? 'active' : ''; ?>" data-tab="multipart-search">
                <?php echo esc_html($settings['tab2_label']); ?>
            </button>
        <?php endif; ?>

        <?php if ($tab3_enabled) : ?>
            <button type="button" class="sf-tab-button <?php echo $first_active === 3 ? 'active' : ''; ?>" data-tab="attribute-search">
                <?php echo esc_html($settings['tab3_label']); ?>
            </button>
        <?php endif; ?>

        <?php if ($tab4_enabled) : ?>
            <button type="button" class="sf-tab-button <?php echo $first_active === 4 ? 'active' : ''; ?>" data-tab="equipment-search">
                <?php echo esc_html($settings['tab4_label']); ?>
            </button>
        <?php endif; ?>
    </div>

    <!-- Tab Content -->
    <div class="sf-search-content">

        <!-- Part Search Tab -->
        <?php if ($tab1_enabled) : ?>
            <div class="sf-tab-content <?php echo $first_active === 1 ? 'active' : ''; ?>" data-tab-content="part-search">
                <form class="sf-search-form" data-search-type="part">
                    <div class="sf-input-wrapper sf-input-with-button">
                        <input type="text"
                               class="sf-search-input"
                               name="search_term"
                               placeholder="<?php echo esc_attr($settings['tab1_placeholder']); ?>"
                               autocomplete="off">
                        <button type="submit" class="sf-search-button">
                            <?php if ($settings['show_search_icon'] === 'yes') : ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.35-4.35"></path>
                                </svg>
                            <?php else : ?>
                                <?php echo esc_html($settings['search_button_text']); ?>
                            <?php endif; ?>
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Multipart Search Tab -->
        <?php if ($tab2_enabled) : ?>
            <div class="sf-tab-content <?php echo $first_active === 2 ? 'active' : ''; ?>" data-tab-content="multipart-search">
                <form class="sf-search-form sf-multipart-form" data-search-type="multipart">
                    <span class="sf-max-parts-error" hidden><?php echo esc_html($max_parts_error); ?></span>
                    <div class="sf-textarea-wrapper">
                        <textarea class="sf-search-textarea sf-multipart-input"
                                  name="search_terms"
                                  rows="4"
                                  placeholder="<?php echo esc_attr($settings['tab2_placeholder']); ?>"></textarea>
                    </div>
                    <div class="sf-form-actions">
                        <a href="#" class="sf-reset-link disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                <path d="M3 3v5h5"></path>
                            </svg>
                            <?php echo esc_html($settings['reset_button_text']); ?>
                        </a>
                        <button type="submit" class="sf-search-button">
                            <?php echo esc_html($settings['search_button_text']); ?>
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Attribute Search Tab -->
        <?php if ($tab3_enabled) : ?>
            <div class="sf-tab-content <?php echo $first_active === 3 ? 'active' : ''; ?>" data-tab-content="attribute-search">
                <form class="sf-search-form sf-attribute-form" data-search-type="attribute">
                    <div class="sf-attribute-fields">
                        <?php foreach ($attribute_fields as $field) :
                            $options = !empty($field['field_options']) ? explode("\n", $field['field_options']) : [];
                        ?>
                            <div class="sf-field-group">
                                <label class="sf-field-label"><?php echo esc_html($field['field_label']); ?></label>
                                <select class="sf-search-select" name="<?php echo esc_attr($field['field_param']); ?>">
                                    <?php foreach ($options as $option) :
                                        $option = trim($option);
                                        if (strpos($option, '|') !== false) {
                                            list($value, $label) = explode('|', $option, 2);
                                        } else {
                                            $value = $label = $option;
                                        }
                                    ?>
                                        <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="sf-form-actions">
                        <a href="#" class="sf-reset-link disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                <path d="M3 3v5h5"></path>
                            </svg>
                            <?php echo esc_html($settings['reset_button_text']); ?>
                        </a>
                        <button type="submit" class="sf-search-button">
                            <?php echo esc_html($settings['search_button_text']); ?>
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Equipment Search Tab -->
        <?php if ($tab4_enabled) : ?>
            <div class="sf-tab-content <?php echo $first_active === 4 ? 'active' : ''; ?>" data-tab-content="equipment-search">
                <form class="sf-search-form sf-equipment-form" data-search-type="equipment">
                    <div class="sf-equipment-fields">
                        <div class="sf-field-group">
                            <label class="sf-field-label"><?php echo esc_html($settings['equipment_make_label']); ?></label>
                            <select class="sf-search-select" name="make">
                                <?php foreach ($make_options as $option) :
                                    $option = trim($option);
                                ?>
                                    <option value="<?php echo esc_attr($option); ?>"><?php echo esc_html($option); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="sf-field-group">
                            <label class="sf-field-label"><?php echo esc_html($settings['equipment_model_label']); ?></label>
                            <select class="sf-search-select" name="model">
                                <?php foreach ($model_options as $option) :
                                    $option = trim($option);
                                ?>
                                    <option value="<?php echo esc_attr($option); ?>"><?php echo esc_html($option); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="sf-field-group">
                            <label class="sf-field-label"><?php echo esc_html($settings['equipment_year_label']); ?></label>
                            <select class="sf-search-select" name="year">
                                <?php foreach ($year_options as $option) :
                                    $option = trim($option);
                                ?>
                                    <option value="<?php echo esc_attr($option); ?>"><?php echo esc_html($option); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="sf-form-actions">
                        <a href="#" class="sf-reset-link disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                <path d="M3 3v5h5"></path>
                            </svg>
                            <?php echo esc_html($settings['reset_button_text']); ?>
                        </a>
                        <button type="submit" class="sf-search-button">
                            <?php echo esc_html($settings['search_button_text']); ?>
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

    </div>
</div>
