<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="sf-pb-metabox">
    <table class="sf-pb-table widefat">
        <thead>
            <tr>
                <th><?php esc_html_e('Manufacturer Name', 'sfilter'); ?></th>
                <th><?php esc_html_e('Codes (comma separated)', 'sfilter'); ?></th>
                <th class="sf-pb-col-action"></th>
            </tr>
        </thead>
        <tbody id="sf-pb-rows">
            <?php if (!empty($data)) : ?>
                <?php foreach ($data as $index => $row) : ?>
                    <tr class="sf-pb-row">
                        <td>
                            <input type="text" name="sf_cross_refs[<?php echo $index; ?>][manufacturer]" value="<?php echo esc_attr($row['manufacturer']); ?>" class="widefat">
                        </td>
                        <td>
                            <textarea name="sf_cross_refs[<?php echo $index; ?>][codes]" class="widefat" rows="3"><?php echo esc_textarea(implode(', ', $row['codes'])); ?></textarea>
                        </td>
                        <td class="sf-pb-col-action">
                            <button type="button" class="button sf-pb-remove-row">&times;</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr class="sf-pb-row">
                    <td>
                        <input type="text" name="sf_cross_refs[0][manufacturer]" value="" class="widefat">
                    </td>
                    <td>
                        <textarea name="sf_cross_refs[0][codes]" class="widefat" rows="3"></textarea>
                    </td>
                    <td class="sf-pb-col-action">
                        <button type="button" class="button sf-pb-remove-row">&times;</button>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <p>
        <button type="button" id="sf-pb-add-row" class="button"><?php esc_html_e('+ Add Row', 'sfilter'); ?></button>
    </p>
</div>
