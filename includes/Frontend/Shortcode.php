<?php

namespace SFilter\Frontend;

/**
 * Shortcode class
 */
class Shortcode
{
    /**
     * Initialize class
     */
    public function __construct()
    {
        add_shortcode('sf_shortcode', [$this, 'sf_shortcode']);
        add_shortcode('sf_enquiry', [$this, 'sf_enquiry']);
    }

    /**
     * Shortcode
     *
     * @param array $atts
     * @param string $content
     * @return string
     */
    public function sf_shortcode($atts, $content = null)
    {
        wp_enqueue_script('sfilter-script');
        wp_enqueue_style('sfilter-style');

        ob_start();

        include __DIR__ . '/views/shortcode.php';

        return ob_get_clean();
    }

    /**
     * Shortcode
     *
     * @param array $atts
     * @param string $content
     * @return string
     */
    public function sf_enquiry($atts, $content = null)
    {
        wp_enqueue_script('sfilter-enquiry-script');
        wp_enqueue_style('sfilter-style');

        // wp_localize_script('sfilter-enquiry-script', 'sfilter_data', [
        //     'ajax_url' => admin_url('admin-ajax.php'),
        //     'message' => __('Message from enquiry form', 'sfilter'),
        // ]);

        ob_start();

        include __DIR__ . '/views/enquiry.php';

        return ob_get_clean();
    }
}
