<?php
if (!defined('ABSPATH')) {
    exit;
}

$product = wc_get_product(get_the_ID());
if (!$product) {
    return;
}
?>
<div class="msf-product-card" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
    <?php if (!empty($settings['show_thumbnail']) && $settings['show_thumbnail'] === 'yes') : ?>
        <div class="msf-product-card__image">
            <a href="<?php echo esc_url($product->get_permalink()); ?>">
                <?php echo $product->get_image('woocommerce_thumbnail'); ?>
            </a>
            <?php if (!empty($settings['show_category_badge']) && $settings['show_category_badge'] === 'yes') : ?>
                <?php
                $terms = get_the_terms($product->get_id(), 'product_cat');
                if ($terms && !is_wp_error($terms)) :
                    $term = reset($terms);
                ?>
                    <span class="msf-product-card__badge"><?php echo esc_html($term->name); ?></span>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="msf-product-card__content">
        <?php if (!empty($settings['show_title']) && $settings['show_title'] === 'yes') : ?>
            <h3 class="msf-product-card__title">
                <a href="<?php echo esc_url($product->get_permalink()); ?>">
                    <?php echo esc_html($product->get_name()); ?>
                </a>
            </h3>
        <?php endif; ?>

        <?php if (!empty($settings['show_rating']) && $settings['show_rating'] === 'yes') : ?>
            <div class="msf-product-card__rating">
                <?php echo wc_get_rating_html($product->get_average_rating(), $product->get_rating_count()); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($settings['show_price']) && $settings['show_price'] === 'yes') : ?>
            <div class="msf-product-card__price">
                <?php echo $product->get_price_html(); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($settings['show_description']) && $settings['show_description'] === 'yes') : ?>
            <div class="msf-product-card__description">
                <?php echo wp_trim_words($product->get_short_description(), 15); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($settings['show_tags']) && $settings['show_tags'] === 'yes') : ?>
            <?php
            $tags = get_the_terms($product->get_id(), 'product_tag');
            if ($tags && !is_wp_error($tags)) :
            ?>
                <div class="msf-product-card__tags">
                    <?php foreach ($tags as $tag) : ?>
                        <span class="msf-product-card__tag"><?php echo esc_html($tag->name); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($settings['show_stock_status']) && $settings['show_stock_status'] === 'yes') : ?>
            <div class="msf-product-card__stock msf-product-card__stock--<?php echo esc_attr($product->get_stock_status()); ?>">
                <?php
                switch ($product->get_stock_status()) {
                    case 'instock':
                        esc_html_e('In Stock', 'sfilter');
                        break;
                    case 'outofstock':
                        esc_html_e('Out of Stock', 'sfilter');
                        break;
                    case 'onbackorder':
                        esc_html_e('On Backorder', 'sfilter');
                        break;
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($settings['show_add_to_cart']) && $settings['show_add_to_cart'] === 'yes') : ?>
            <div class="msf-product-card__actions">
                <?php if (!empty($settings['show_quantity_selector']) && $settings['show_quantity_selector'] === 'yes'
                    && $product->is_purchasable() && $product->is_in_stock() && $product->is_type('simple')) : ?>
                    <div class="msf-product-card__quantity">
                        <button type="button" class="msf-qty-btn msf-qty-minus">&minus;</button>
                        <input type="number" class="msf-qty-input" value="1" min="1" max="<?php echo esc_attr($product->get_max_purchase_quantity() > 0 ? $product->get_max_purchase_quantity() : ''); ?>" step="1" />
                        <button type="button" class="msf-qty-btn msf-qty-plus">+</button>
                    </div>
                <?php endif; ?>
                <?php
                $button_text = !empty($settings['button_text']) ? $settings['button_text'] : __('Add to Cart', 'sfilter');
                $button_type = !empty($settings['cart_button_type']) ? $settings['cart_button_type'] : 'text';

                if ($button_type === 'icon') {
                    $button_content = '<svg class="msf-cart-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>';
                } else {
                    $button_content = esc_html($button_text);
                }

                echo sprintf(
                    '<a href="%s" data-quantity="1" class="msf-product-card__button button %s %s" %s>%s</a>',
                    esc_url($product->add_to_cart_url()),
                    esc_attr(implode(' ', array_filter([
                        'product_type_' . $product->get_type(),
                        $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
                        $product->supports('ajax_add_to_cart') && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
                    ]))),
                    $button_type === 'icon' ? 'msf-product-card__button--icon' : '',
                    sprintf('data-product_id="%d" data-product_sku="%s"', $product->get_id(), esc_attr($product->get_sku())),
                    $button_content
                );
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>
