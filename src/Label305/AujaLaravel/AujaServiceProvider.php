<?php namespace Label305\AujaLaravel;

use Illuminate\Support\ServiceProvider;
use Label305\AujaLaravel\Routing\AujaRouter;

abstract class AujaServiceProvider extends ServiceProvider {

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
        $this->app->bind('auja', 'Label305\AujaLaravel\Auja');
        $this->app->bind('Label305\AujaLaravel\Database\DatabaseHelper', 'Label305\AujaLaravel\Database\MySQLDatabaseHelper');
        $this->app->bind('Label305\AujaLaravel\Logging\Logger', 'Label305\AujaLaravel\Logging\LaravelLogger');

        $this->app->singleton('Label305\AujaLaravel\Config\AujaConfigurator');

        $this->app->bind('AujaRouter', 'Label305\AujaLaravel\Routing\AujaRouter');

        $this->app->singleton('Label305\AujaLaravel\Auja', function () {
            return new Auja($this->app, $this->getModelNames());
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return array();
    }

    /**
     * Returns a String array of model names, e.g. ['Club', 'Team'].
     *
     * @return String[] The model names.
     */
    abstract function getModelNames();

}
