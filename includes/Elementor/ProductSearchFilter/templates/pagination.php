<?php
if (!defined('ABSPATH')) {
    exit;
}

if ($max_pages <= 1) {
    return;
}
?>
<div class="msf-pagination msf-pagination--<?php echo esc_attr($pagination_type); ?>">
    <?php if ($pagination_type === 'numbered') : ?>
        <div class="msf-pagination__numbers">
            <?php for ($i = 1; $i <= $max_pages; $i++) : ?>
                <button type="button" class="msf-pagination__page <?php echo $i === intval($paged) ? 'msf-pagination__page--active' : ''; ?>" data-page="<?php echo esc_attr($i); ?>">
                    <?php echo esc_html($i); ?>
                </button>
            <?php endfor; ?>
        </div>

    <?php elseif ($pagination_type === 'load_more') : ?>
        <?php if (intval($paged) < $max_pages) : ?>
            <button type="button" class="msf-pagination__load-more" data-page="<?php echo esc_attr(intval($paged) + 1); ?>">
                <?php echo esc_html($load_more_text); ?>
            </button>
        <?php endif; ?>

    <?php elseif ($pagination_type === 'infinite_scroll') : ?>
        <?php if (intval($paged) < $max_pages) : ?>
            <div class="msf-pagination__sentinel" data-page="<?php echo esc_attr(intval($paged) + 1); ?>"></div>
        <?php endif; ?>
    <?php endif; ?>
</div>
