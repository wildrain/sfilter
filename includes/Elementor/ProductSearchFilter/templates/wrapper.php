<?php
if (!defined('ABSPATH')) {
    exit;
}

$filter_position = !empty($settings['filter_position']) ? $settings['filter_position'] : 'top';
$position_class  = 'msf-wrapper--filter-' . esc_attr($filter_position);
?>
<div class="msf-wrapper <?php echo $position_class; ?>"
     data-widget-id="<?php echo esc_attr($widget_id); ?>"
     data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
     data-nonce="<?php echo esc_attr(wp_create_nonce('sf_product_search')); ?>"
     data-settings="<?php echo esc_attr(wp_json_encode($js_settings)); ?>">

    <?php include __DIR__ . '/filter-bar.php'; ?>

    <div class="msf-content-area">
        <div class="msf-results">
            <?php
            if ($query->have_posts()) {
                $view = !empty($settings['default_view']) ? $settings['default_view'] : 'grid';
                $template = ($view === 'list') ? 'product-list.php' : 'product-grid.php';
                include __DIR__ . '/' . $template;
            } else {
                include __DIR__ . '/no-results.php';
            }
            ?>
        </div>

        <div class="msf-pagination-container">
            <?php
            $pagination_type = !empty($settings['pagination_type']) ? $settings['pagination_type'] : 'numbered';
            $load_more_text  = !empty($settings['load_more_text']) ? $settings['load_more_text'] : __('Load More', 'sfilter');
            $paged           = 1;
            $max_pages       = $query->max_num_pages;
            include __DIR__ . '/pagination.php';
            ?>
        </div>
    </div>

    <div class="msf-loading-overlay" style="display:none;">
        <div class="msf-spinner"></div>
    </div>
</div>
