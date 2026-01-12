<?php

namespace SFilter;

/**
 * Ajax class
 */
class Ajax
{
    /**
     * Initialize ajax class
     */
    public function __construct()
    {
        add_action('wp_ajax_sf_enquiry', [$this, 'sf_enquiry']);
        add_action('wp_ajax_nopriv_sf_enquiry', [$this, 'sf_enquiry']);
    }

    /**
     * Perform enquiry operation
     *
     * @return array
     */
    public function sf_enquiry()
    {
        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'sf-enquiry-form')) {
            wp_send_json_error([
                'message' => __('Nonce verification failed!', 'sfilter')
            ]);
        }

        wp_send_json_success([
            'message' => __('Perform your operation', 'sfilter'),
            'data'    => $_REQUEST,
        ]);
    }
}
