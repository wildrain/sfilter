<?php

namespace SFilter\Admin;

class Handler
{
    /**
     * Class initialize
     */
    function __construct()
    {
        // add_filter('use_block_editor_for_post_type', [$this, 'disable_gutenberg_for_movie'], 10, 2);
        add_filter('turbo_tgmpa_configs_plugins', [$this, 'disable_tgm_notices']);
    }

    /**
     * Disable gutenberg editor for movie post
     *
     * @param boolean $current_status
     * @param string $post_type
     * @return boolean
     */
    public function disable_gutenberg_for_movie($current_status, $post_type)
    {
        return false;
    }

    public function disable_tgm_notices($config)
    {
        // Disable TGM plugin notices
        $config['has_notices'] = false;

        return $config;
    }
}
