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
