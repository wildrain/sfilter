<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="msf-products msf-products--list">
    <?php while ($query->have_posts()) : $query->the_post(); ?>
        <?php include __DIR__ . '/product-card.php'; ?>
    <?php endwhile; ?>
    <?php wp_reset_postdata(); ?>
</div>
