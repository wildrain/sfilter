<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="sf-product-specs">
    <table class="sf-specs-table">
        <tbody>
            <?php foreach ($taxonomies as $taxonomy => $labels) : ?>
                <?php
                $terms = wp_get_post_terms($product_id, $taxonomy);
                if (!empty($terms) && !is_wp_error($terms)) :
                    $term_names = wp_list_pluck($terms, 'name');
                ?>
                    <tr>
                        <th><?php echo esc_html($labels['singular']); ?></th>
                        <td><?php echo esc_html(implode(', ', $term_names)); ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (empty(array_filter(array_map(function($tax) use ($product_id) {
        $terms = wp_get_post_terms($product_id, $tax);
        return !empty($terms) && !is_wp_error($terms);
    }, array_keys($taxonomies))))) : ?>
        <p><?php esc_html_e('No specifications available.', 'sfilter'); ?></p>
    <?php endif; ?>
</div>
