<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="sf-pb-metabox sf-pb-application">
    <table class="sf-pb-table widefat">
        <thead>
            <tr>
                <th><?php esc_html_e('Make', 'sfilter'); ?></th>
                <th><?php esc_html_e('Model', 'sfilter'); ?></th>
                <th class="sf-pb-col-year"><?php esc_html_e('Year From', 'sfilter'); ?></th>
                <th class="sf-pb-col-year"><?php esc_html_e('Year To', 'sfilter'); ?></th>
                <th class="sf-pb-col-action"></th>
            </tr>
        </thead>
        <tbody id="sf-pb-app-rows">
            <?php if (!empty($data)) : ?>
                <?php foreach ($data as $index => $row) : ?>
                    <tr class="sf-pb-row">
                        <td>
                            <input type="text" name="sf_applications[<?php echo $index; ?>][make]" value="<?php echo esc_attr($row['make']); ?>" class="widefat">
                        </td>
                        <td>
                            <input type="text" name="sf_applications[<?php echo $index; ?>][model]" value="<?php echo esc_attr($row['model']); ?>" class="widefat">
                        </td>
                        <td>
                            <input type="number" name="sf_applications[<?php echo $index; ?>][year_from]" value="<?php echo esc_attr($row['year_from']); ?>" class="widefat" min="1900" max="2100">
                        </td>
                        <td>
                            <input type="number" name="sf_applications[<?php echo $index; ?>][year_to]" value="<?php echo esc_attr($row['year_to']); ?>" class="widefat" min="1900" max="2100">
                        </td>
                        <td class="sf-pb-col-action">
                            <button type="button" class="button sf-pb-remove-row">&times;</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr class="sf-pb-row">
                    <td>
                        <input type="text" name="sf_applications[0][make]" value="" class="widefat">
                    </td>
                    <td>
                        <input type="text" name="sf_applications[0][model]" value="" class="widefat">
                    </td>
                    <td>
                        <input type="number" name="sf_applications[0][year_from]" value="" class="widefat" min="1900" max="2100">
                    </td>
                    <td>
                        <input type="number" name="sf_applications[0][year_to]" value="" class="widefat" min="1900" max="2100">
                    </td>
                    <td class="sf-pb-col-action">
                        <button type="button" class="button sf-pb-remove-row">&times;</button>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <p>
        <button type="button" id="sf-pb-add-app-row" class="button"><?php esc_html_e('+ Add Row', 'sfilter'); ?></button>
    </p>
</div>
