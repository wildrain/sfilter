<?php
/**
 * Custom Checkout Form Template
 *
 * @package SFilter
 */

if (!defined('ABSPATH')) {
    exit;
}

$fields = \SFilter\Checkout\Fields::get_fields();
?>

<div class="sf-checkout-form-wrapper">
    <h3><?php esc_html_e('Quotation Information', 'sfilter'); ?></h3>

    <div class="sf-checkout-form" id="sf-checkout-form">
        <div class="sf-checkout-fields">
            <?php foreach ($fields as $key => $field) : ?>
                <div class="sf-field sf-field-<?php echo esc_attr($field['type']); ?> <?php echo $field['required'] ? 'sf-field-required' : ''; ?>">
                    <label for="<?php echo esc_attr($key); ?>">
                        <?php echo esc_html($field['label']); ?>
                        <?php if ($field['required']) : ?>
                            <span class="required">*</span>
                        <?php endif; ?>
                    </label>

                    <?php if ($field['type'] === 'textarea') : ?>
                        <textarea
                            name="<?php echo esc_attr($key); ?>"
                            id="<?php echo esc_attr($key); ?>"
                            placeholder="<?php echo esc_attr($field['placeholder'] ?? ''); ?>"
                            <?php echo $field['required'] ? 'required' : ''; ?>
                            rows="3"
                        ></textarea>

                    <?php elseif ($field['type'] === 'select') : ?>
                        <select
                            name="<?php echo esc_attr($key); ?>"
                            id="<?php echo esc_attr($key); ?>"
                            <?php echo $field['required'] ? 'required' : ''; ?>
                        >
                            <?php foreach ($field['options'] as $value => $label) : ?>
                                <option value="<?php echo esc_attr($value); ?>">
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    <?php else : ?>
                        <input
                            type="<?php echo esc_attr($field['type']); ?>"
                            name="<?php echo esc_attr($key); ?>"
                            id="<?php echo esc_attr($key); ?>"
                            placeholder="<?php echo esc_attr($field['placeholder'] ?? ''); ?>"
                            <?php echo $field['required'] ? 'required' : ''; ?>
                        />
                    <?php endif; ?>

                    <span class="sf-field-error" id="<?php echo esc_attr($key); ?>-error"></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="sf-checkout-actions">
            <?php wp_nonce_field('sf_checkout_quotation', 'sf_checkout_nonce'); ?>
            <input type="hidden" name="action" value="sf_checkout_quotation" />

            <button type="button" id="sf-download-quotation" class="button sf-quotation-btn">
                <?php esc_html_e('Download Quotation', 'sfilter'); ?>
            </button>

            <div class="sf-checkout-loading" style="display: none;">
                <span class="spinner"></span>
                <?php esc_html_e('Processing...', 'sfilter'); ?>
            </div>
        </div>

        <div class="sf-checkout-messages"></div>
    </div>
</div>
