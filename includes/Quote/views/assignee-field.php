<?php
/**
 * Assignee field template
 *
 * @var \WC_Order $order
 * @var array $assignees
 * @var string $current_assignee
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="sf-assignee-field">
    <h4><?php esc_html_e('Quote Assignee', 'sfilter'); ?></h4>
    <div class="sf-assignee-field-row">
        <select name="sf_assignee" id="sf_assignee">
            <option value=""><?php esc_html_e('Select Assignee', 'sfilter'); ?></option>
            <?php foreach ($assignees as $key => $label) : ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($current_assignee, $key); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="button" id="sf_assignee_save" class="button button-primary">
            <span class="sf-btn-text"><?php esc_html_e('Save', 'sfilter'); ?></span>
            <span class="sf-btn-loader"></span>
        </button>
    </div>
    <?php wp_nonce_field('sf_save_assignee', 'sf_assignee_nonce'); ?>
    <input type="hidden" id="sf_assignee_order_id" value="<?php echo esc_attr($order->get_id()); ?>">
</div>
