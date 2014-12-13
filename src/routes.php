<?php

$prefix = Config::get('auja.route') ?: Config::get('auja-laravel::config.route');

if ($prefix !== null) {

    Route::group(['prefix' => $prefix, 'namespace' => 'Label305\AujaLaravel\Controllers'], function () {
        AujaRoute::support('DefaultSupportController');
    });
}