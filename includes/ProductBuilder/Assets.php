<?php

namespace SFilter\ProductBuilder;

class Assets
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue($hook)
    {
        global $post;

        if (!in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        if (!$post || $post->post_type !== 'product') {
            return;
        }

        wp_enqueue_style(
            'sf-pb-admin',
            SFILTER_ASSETS . '/ProductBuilder/css/admin.css',
            [],
            filemtime(SFILTER_PATH . '/assets/ProductBuilder/css/admin.css')
        );

        wp_enqueue_script(
            'sf-pb-admin',
            SFILTER_ASSETS . '/ProductBuilder/js/admin.js',
            ['jquery'],
            filemtime(SFILTER_PATH . '/assets/ProductBuilder/js/admin.js'),
            true
        );
    }
}
