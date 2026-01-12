<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="sf-product-cross-reference">
    <?php if (!empty($cross_refs)) : ?>
        <table class="sf-cross-reference-table">
            <thead>
                <tr>
                    <th><?php esc_html_e('Manufacturer', 'sfilter'); ?></th>
                    <th><?php esc_html_e('Codes', 'sfilter'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cross_refs as $ref) : ?>
                    <tr>
                        <td><?php echo esc_html($ref['manufacturer']); ?></td>
                        <td><?php echo esc_html(implode(', ', $ref['codes'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p><?php esc_html_e('No cross references available.', 'sfilter'); ?></p>
    <?php endif; ?>
</div>
