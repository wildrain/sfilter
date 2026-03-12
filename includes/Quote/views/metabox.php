<?php
/**
 * Quote metabox template
 *
 * @var \WC_Order $order
 * @var string $pdf_url
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="sf-quote-metabox">
    <?php if (!empty($pdf_url)) : ?>
        <a href="<?php echo esc_url($pdf_url); ?>" target="_blank" class="sf-download-quote-btn">
            <span class="dashicons dashicons-pdf"></span>
            <?php esc_html_e('Download Quote PDF', 'sfilter'); ?>
        </a>
    <?php else : ?>
        <p class="sf-no-pdf"><?php esc_html_e('No PDF available', 'sfilter'); ?></p>
    <?php endif; ?>
</div>
