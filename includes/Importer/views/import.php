<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<?php if (!empty($errors)): ?>
    <div class="notice notice-error">
        <?php foreach ($errors as $error): ?>
            <p><?php echo esc_html($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($messages)): ?>
    <div class="notice notice-success">
        <?php foreach ($messages as $message): ?>
            <p><?php echo esc_html($message); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="sf-import-sections" style="display: flex; gap: 30px; flex-wrap: wrap; margin-top: 20px;">
    <!-- Cross Reference Import -->
    <div class="sf-import-section" style="flex: 1; min-width: 400px; background: #fff; padding: 20px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
        <h2><?php esc_html_e('Cross Reference Import', 'sfilter'); ?></h2>

        <div class="sf-csv-format" style="background: #f9f9f9; padding: 15px; margin-bottom: 20px; border-left: 4px solid #0073aa;">
            <h4 style="margin-top: 0;"><?php esc_html_e('CSV Format:', 'sfilter'); ?></h4>
            <code style="display: block; white-space: pre; font-size: 12px;">sku,manufacturer,codes
FILTER-001,Fleetguard,"LF123,LF456,LF789"
FILTER-001,Baldwin,"B7,B8"
FILTER-002,Donaldson,"P550123"</code>
            <p style="margin-bottom: 0; font-size: 12px; color: #666;">
                <strong>sku:</strong> <?php esc_html_e('Product SKU', 'sfilter'); ?><br>
                <strong>manufacturer:</strong> <?php esc_html_e('Manufacturer name', 'sfilter'); ?><br>
                <strong>codes:</strong> <?php esc_html_e('Part codes (comma-separated within quotes)', 'sfilter'); ?>
            </p>
            <p style="margin-top: 10px; margin-bottom: 0;">
                <a href="<?php echo esc_url(SFILTER_URL . '/includes/Importer/samples/cross_references.csv'); ?>" class="button button-secondary" download>
                    <?php esc_html_e('Download Sample CSV', 'sfilter'); ?>
                </a>
            </p>
        </div>

        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('sf_import_action', 'sf_import_nonce'); ?>
            <input type="hidden" name="import_type" value="cross_reference">

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="cross_ref_file"><?php esc_html_e('CSV File', 'sfilter'); ?></label>
                    </th>
                    <td>
                        <input type="file" name="import_file" id="cross_ref_file" accept=".csv" required>
                    </td>
                </tr>
            </table>

            <?php submit_button(__('Import Cross References', 'sfilter'), 'primary', 'submit_cross_ref'); ?>
        </form>
    </div>

    <!-- Application Import -->
    <div class="sf-import-section" style="flex: 1; min-width: 400px; background: #fff; padding: 20px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
        <h2><?php esc_html_e('Application Import', 'sfilter'); ?></h2>

        <div class="sf-csv-format" style="background: #f9f9f9; padding: 15px; margin-bottom: 20px; border-left: 4px solid #0073aa;">
            <h4 style="margin-top: 0;"><?php esc_html_e('CSV Format:', 'sfilter'); ?></h4>
            <code style="display: block; white-space: pre; font-size: 12px;">sku,make,model,year_from,year_to
FILTER-001,Toyota,Camry,2015,2020
FILTER-001,Honda,Accord,2018,2023
FILTER-002,Ford,F-150,2017,2022</code>
            <p style="margin-bottom: 0; font-size: 12px; color: #666;">
                <strong>sku:</strong> <?php esc_html_e('Product SKU', 'sfilter'); ?><br>
                <strong>make:</strong> <?php esc_html_e('Vehicle/equipment make', 'sfilter'); ?><br>
                <strong>model:</strong> <?php esc_html_e('Vehicle/equipment model', 'sfilter'); ?><br>
                <strong>year_from:</strong> <?php esc_html_e('Starting year', 'sfilter'); ?><br>
                <strong>year_to:</strong> <?php esc_html_e('Ending year', 'sfilter'); ?>
            </p>
            <p style="margin-top: 10px; margin-bottom: 0;">
                <a href="<?php echo esc_url(SFILTER_URL . '/includes/Importer/samples/applications.csv'); ?>" class="button button-secondary" download>
                    <?php esc_html_e('Download Sample CSV', 'sfilter'); ?>
                </a>
            </p>
        </div>

        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('sf_import_action', 'sf_import_nonce'); ?>
            <input type="hidden" name="import_type" value="application">

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="app_file"><?php esc_html_e('CSV File', 'sfilter'); ?></label>
                    </th>
                    <td>
                        <input type="file" name="import_file" id="app_file" accept=".csv" required>
                    </td>
                </tr>
            </table>

            <?php submit_button(__('Import Applications', 'sfilter'), 'primary', 'submit_app'); ?>
        </form>
    </div>
</div>

<div style="margin-top: 30px; padding: 20px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;">
    <h3 style="margin-top: 0; color: #856404;"><?php esc_html_e('Import Notes', 'sfilter'); ?></h3>
    <ul style="margin-bottom: 0; color: #856404;">
        <li><?php esc_html_e('Products are matched by SKU. Make sure your products have SKUs assigned.', 'sfilter'); ?></li>
        <li><?php esc_html_e('Multiple rows with the same SKU will add multiple entries to that product.', 'sfilter'); ?></li>
        <li><?php esc_html_e('Import merges with existing data (does not replace).', 'sfilter'); ?></li>
        <li><?php esc_html_e('Rows with missing SKU or product not found will be skipped.', 'sfilter'); ?></li>
    </ul>
</div>
