<?php

function sf_log($message, $source, $data = [])
{
    $logger = wc_get_logger();
    $context = ['source' => $source];
    if (!empty($data)) {
        $context['data'] = $data;
    }
    $logger->info($message, $context);
}

add_filter('tm_generate_cache_key', function ($cache_key, $args, $identifier) {
    return 'sf_transient_disabled';
}, 10, 3);

add_filter('transient_sf_transient_disabled', function ($value) {
    return false;
});
