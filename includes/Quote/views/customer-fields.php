<?php
/**
 * Customer fields template
 *
 * @var \WC_Order $order
 * @var array $fields
 * @var array $regions
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="sf-customer-fields">
    <h4><?php esc_html_e('Quote Information', 'sfilter'); ?></h4>
    <div class="sf-customer-fields-grid">
        <?php foreach ($fields as $key => $field) :
            $value = $order->get_meta('_' . $key);
            if (empty($value)) {
                continue;
            }

            // Convert region code to label
            if ($key === 'sf_region' && isset($regions[$value])) {
                $value = $regions[$value];
            }
        ?>
            <div class="sf-customer-field">
                <label><?php echo esc_html($field['label']); ?></label>
                <span><?php echo esc_html($value); ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
