<?php
/**
 * PDF Quotation Template for Dompdf
 *
 * Variables available:
 * - $order (WC_Order)
 * - $this->customer_data (array)
 * - $this->order_id (int)
 *
 * @package SFilter
 */

if (!defined('ABSPATH')) {
    exit;
}

$fields = \SFilter\Checkout\Fields::get_fields();
$regions = \SFilter\Checkout\Fields::get_regions();

// Get site info
$site_name = get_bloginfo('name');
$site_url = home_url();

// Format date
$date = current_time('d/m/Y');
$quotation_number = str_pad($this->order_id, 6, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title><?php printf(__('Quotation #%s', 'sfilter'), $quotation_number); ?></title>
    <style>
        @page {
            margin: 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
            direction: rtl;
            text-align: right;
        }

        h1 {
            color: #333;
            font-size: 22pt;
            margin-bottom: 5px;
            text-align: center;
        }

        h2 {
            color: #0073aa;
            font-size: 14pt;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #0073aa;
            padding-bottom: 5px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 18pt;
            font-weight: bold;
            color: #333;
        }

        .quotation-info {
            background-color: #f5f5f5;
            padding: 10px;
            margin-bottom: 20px;
        }

        .quotation-info table {
            width: 100%;
        }

        .quotation-info td {
            padding: 5px 0;
        }

        .customer-info {
            margin-bottom: 20px;
        }

        .customer-info table {
            width: 100%;
        }

        .customer-info td {
            padding: 3px 0;
            vertical-align: top;
        }

        .customer-info .label {
            font-weight: bold;
            width: 30%;
            color: #555;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background-color: #0073aa;
            color: #fff;
            padding: 10px 8px;
            text-align: right;
            font-weight: bold;
        }

        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
        }

        .items-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .totals-table {
            width: 50%;
            margin-right: auto;
            margin-left: 0;
        }

        .totals-table td {
            padding: 8px;
        }

        .totals-table .label {
            text-align: right;
            font-weight: bold;
        }

        .totals-table .value {
            text-align: left;
        }

        .totals-table .total-row {
            background-color: #0073aa;
            color: #fff;
            font-size: 12pt;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }

        .note {
            background-color: #fffde7;
            border: 1px solid #fff59d;
            padding: 10px;
            margin-top: 20px;
            font-size: 9pt;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="company-name"><?php echo esc_html($site_name); ?></div>
    <div><?php echo esc_html($site_url); ?></div>
</div>

<h1><?php esc_html_e('QUOTATION', 'sfilter'); ?></h1>

<div class="quotation-info">
    <table>
        <tr>
            <td><strong><?php esc_html_e('Quotation Number:', 'sfilter'); ?></strong> #<?php echo esc_html($quotation_number); ?></td>
            <td class="text-left"><strong><?php esc_html_e('Date:', 'sfilter'); ?></strong> <?php echo esc_html($date); ?></td>
        </tr>
    </table>
</div>

<h2><?php esc_html_e('Customer Information', 'sfilter'); ?></h2>

<div class="customer-info">
    <table>
        <?php foreach ($fields as $key => $field) :
            $value = isset($this->customer_data[$key]) ? $this->customer_data[$key] : '';
            if (empty($value)) continue;

            // Convert region value to label
            if ($key === 'sf_region' && isset($regions[$value])) {
                $value = $regions[$value];
            }
        ?>
        <tr>
            <td class="label"><?php echo esc_html($field['label']); ?>:</td>
            <td><?php echo esc_html($value); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<h2><?php esc_html_e('Quotation Items', 'sfilter'); ?></h2>

<table class="items-table">
    <thead>
        <tr>
            <th style="width: 50%;"><?php esc_html_e('Product', 'sfilter'); ?></th>
            <th class="text-center" style="width: 15%;"><?php esc_html_e('SKU', 'sfilter'); ?></th>
            <th class="text-center" style="width: 10%;"><?php esc_html_e('Qty', 'sfilter'); ?></th>
            <th class="text-left" style="width: 12%;"><?php esc_html_e('Price', 'sfilter'); ?></th>
            <th class="text-left" style="width: 13%;"><?php esc_html_e('Total', 'sfilter'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($order->get_items() as $item_id => $item) :
            $product = $item->get_product();
            $sku = $product ? $product->get_sku() : '-';
        ?>
        <tr>
            <td><?php echo esc_html($item->get_name()); ?></td>
            <td class="text-center"><?php echo esc_html($sku ?: '-'); ?></td>
            <td class="text-center"><?php echo esc_html($item->get_quantity()); ?></td>
            <td class="text-left"><?php echo strip_tags(wc_price($order->get_item_subtotal($item, false, true))); ?></td>
            <td class="text-left"><?php echo strip_tags(wc_price($item->get_total())); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<table class="totals-table">
    <tr>
        <td class="label"><?php esc_html_e('Subtotal:', 'sfilter'); ?></td>
        <td class="value"><?php echo strip_tags(wc_price($order->get_subtotal())); ?></td>
    </tr>
    <?php if ($order->get_total_discount() > 0) : ?>
    <tr>
        <td class="label"><?php esc_html_e('Discount:', 'sfilter'); ?></td>
        <td class="value">-<?php echo strip_tags(wc_price($order->get_total_discount())); ?></td>
    </tr>
    <?php endif; ?>
    <?php if ($order->get_shipping_total() > 0) : ?>
    <tr>
        <td class="label"><?php esc_html_e('Shipping:', 'sfilter'); ?></td>
        <td class="value"><?php echo strip_tags(wc_price($order->get_shipping_total())); ?></td>
    </tr>
    <?php endif; ?>
    <?php if ($order->get_total_tax() > 0) : ?>
    <tr>
        <td class="label"><?php esc_html_e('Tax:', 'sfilter'); ?></td>
        <td class="value"><?php echo strip_tags(wc_price($order->get_total_tax())); ?></td>
    </tr>
    <?php endif; ?>
    <tr class="total-row">
        <td class="label"><?php esc_html_e('Total:', 'sfilter'); ?></td>
        <td class="value"><?php echo strip_tags(wc_price($order->get_total())); ?></td>
    </tr>
</table>

<div class="note">
    <strong><?php esc_html_e('Note:', 'sfilter'); ?></strong>
    <?php esc_html_e('This is a quotation and not a confirmed order. Prices are subject to change. Please contact us to confirm your order.', 'sfilter'); ?>
</div>

<div class="footer">
    <p><?php esc_html_e('Thank you for your interest in our products.', 'sfilter'); ?></p>
    <p><?php echo esc_html($site_name); ?> | <?php echo esc_html($site_url); ?></p>
</div>

</body>
</html>
