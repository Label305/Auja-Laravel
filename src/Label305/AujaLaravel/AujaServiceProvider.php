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
        $this->app->singleton('Label305\AujaLaravel\Auja', function ($app) {
            return new Auja($app, $this->getModelNames());
        });

        $this->app->bind('Label305\AujaLaravel\Database\DatabaseHelper', 'Label305\AujaLaravel\Database\MySQLDatabaseHelper');
        $this->app->bind('Label305\AujaLaravel\Logging\Logger', 'Label305\AujaLaravel\Logging\LaravelLogger');

        $this->app->singleton('Label305\AujaLaravel\Config\AujaConfigurator');

        $this->app->bind('AujaRouter', 'Label305\AujaLaravel\Routing\AujaRouter');

        $this->app->singleton('aujarouter', function($app){
            return new AujaRouter($app['auja'], $app['Illuminate\Routing\Router']);
        });
    }

    /**
     * Returns a String array of model names, e.g. ['Club', 'Team'].
     *
     * @return String[] The model names.
     */
    abstract function getModelNames();

}
