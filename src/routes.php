<?php

$prefix = $app['config']['auja.route'] ?: $app['config']['auja-laravel::config.route'];

if ($prefix !== null) {

    AujaRoute::group([], function () {
        AujaRoute::support('Label305\AujaLaravel\Controllers\DefaultSupportController');
    });
}