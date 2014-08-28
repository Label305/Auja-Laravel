<?php namespace Label305\AujaLaravel;

use Illuminate\Support\ServiceProvider;

class AujaLaravelServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        // TODO think of something to easily create custom implementations.
        $this->app->bind('Label305\AujaLaravel\Repositories\DatabaseRepository', 'Label305\AujaLaravel\Repositories\DefaultDatabaseRepository');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return array();
    }

}
