<?php

namespace SFilter;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Ajax class
 */
class API
{

    /**
     * Initialize ajax class
     */
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'sfilter_api_init']);
    }

    public function sfilter_api_init()
    {
        register_rest_route('sfilter/v1', '/test', array(
            'methods' => 'GET',
            'callback' => [$this, 'sfilter_test'],
        ));
    }

    public function sfilter_test(WP_REST_Request $request)
    {
        $response = new WP_REST_Response([
            'message' => 'Hello World',
        ]);

        return $response;
    }
}
