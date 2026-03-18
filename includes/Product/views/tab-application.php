<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="sf-product-application">
    <?php if (!empty($applications)) : ?>
        <table class="sf-application-table">
            <thead>
                <tr>
                    <th><?php esc_html_e('Make', 'sfilter'); ?></th>
                    <th><?php esc_html_e('Model', 'sfilter'); ?></th>
                    <th><?php esc_html_e('Year', 'sfilter'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $app) : ?>
                    <tr>
                        <td><?php echo esc_html($app['make']); ?></td>
                        <td><?php echo esc_html($app['model']); ?></td>
                        <td>
                            <?php
                            if ($app['year_from'] && $app['year_to']) {
                                echo esc_html($app['year_from'] . ' - ' . $app['year_to']);
                            } elseif ($app['year_from']) {
                                echo esc_html($app['year_from']);
                            } elseif ($app['year_to']) {
                                echo esc_html($app['year_to']);
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p><?php esc_html_e('No application data available.', 'sfilter'); ?></p>
    <?php endif; ?>
</div>
