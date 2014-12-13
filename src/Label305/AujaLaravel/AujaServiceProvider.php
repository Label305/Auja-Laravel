<?php namespace Label305\AujaLaravel;

use Illuminate\Support\ServiceProvider;
use Label305\AujaLaravel\Exceptions\NoDatabaseHelperException;
use Label305\AujaLaravel\Routing\AujaRouter;

class AujaServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the package
     */
    public function boot() {

        $this->package('label305/auja-laravel');

        // Include the routes file located in src/routes.php of this package
        include __DIR__.'/../../routes.php';
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {

        $app = $this->app;
        $config = $this->getConfig();

        $app->bind('auja', 'Label305\AujaLaravel\Auja');
        $app->singleton('Label305\AujaLaravel\Auja', function ($app) {
            return new Auja($app, $this->getModelNames());
        });

        $app->bind('Label305\AujaLaravel\Logging\Logger', 'Label305\AujaLaravel\Logging\LaravelLogger');

        switch ($config['database']) {
            case 'mysql':
                $app->bind('Label305\AujaLaravel\Database\DatabaseHelper', 'Label305\AujaLaravel\Database\MySQLDatabaseHelper');
                break;
            default:
                throw new NoDatabaseHelperException('No Auja DatabaseHelper for ' . $config['database']);
                break;
        }

        $app->singleton('Label305\AujaLaravel\Config\AujaConfigurator');

        $app->bind('AujaRouter', 'Label305\AujaLaravel\Routing\AujaRouter');
        $app->singleton('aujarouter', function($app) {
            return new AujaRouter($app['auja'], $app['Illuminate\Routing\Router']);
        });
    }

    /**
     * Returns a String array of model names, e.g. ['Club', 'Team'].
     *
     * @return String[] The model names.
     */
    function getModelNames() {

        $config = $this->getConfig();
        return $config['models'];
    }

    /**
     * Get config values
     * Allows for both /app/config/auja.php as well as /app/config/packages/auja-laravel/config.php
     *
     * @return array
     */
    public function getConfig() {
        return $this->app['config']['auja'] ?: $this->app['config']['auja-laravel::config'];
    }

}
