<?php
if (!defined('ABSPATH')) {
    exit;
}

$columns = !empty($settings['grid_columns']) ? intval($settings['grid_columns']) : 3;
?>
<div class="msf-products msf-products--grid msf-products--cols-<?php echo esc_attr($columns); ?>">
    <?php while ($query->have_posts()) : $query->the_post(); ?>
        <?php include __DIR__ . '/product-card.php'; ?>
    <?php endwhile; ?>
    <?php wp_reset_postdata(); ?>
</div>
