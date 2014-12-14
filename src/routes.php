<?php

$prefix = $app['config']['auja.route'] ?: $app['config']['auja-laravel::config.route'];

if ($prefix !== null) {

    Route::group(['prefix' => $prefix], function () {
        AujaRoute::support('Label305\AujaLaravel\Controllers\DefaultSupportController');
    });
}