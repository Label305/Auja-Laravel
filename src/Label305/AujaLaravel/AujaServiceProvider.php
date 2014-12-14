<?php namespace Label305\AujaLaravel;

use Illuminate\Support\ServiceProvider;
use Label305\AujaLaravel\Config\AujaConfigurator;
use Label305\AujaLaravel\Database\MySQLDatabaseHelper;
use Label305\AujaLaravel\Exceptions\MandatoryConfigKeyMisconfiguredException;
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

        $app = $this->app;

        // Include the routes file located in src/routes.php of this package
        include __DIR__.'/../../routes.php';
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {

        $this->registerConfigurator();

        $this->registerManager();

        $this->registerRouter();
    }

    /**
     * Register the manager.
     */
    protected function registerManager()
    {
        $this->app->singleton('auja', function ($app) {
            $config = $app['config']['auja-laravel'] ?: $app['config']['auja-laravel::config'];
            return new Auja($app, $app['auja.configurator'], $config['models']);
        });
    }

    /**
     * Register the auja configurator
     */
    protected function registerConfigurator()
    {
        $this->app->bind('auja.database', function($app) {

            $config = $app['config']['auja-laravel'] ?: $app['config']['auja-laravel::config'];

            switch ($config['database']) {
                case 'mysql':
                    return new MySQLDatabaseHelper();
                    break;
                default:
                    throw new NoDatabaseHelperException('No Auja database helper for ' . $config['database']);
                    break;
            }
        });

        $this->app->singleton('auja.configurator', function($app) {
            return new AujaConfigurator($app, $app['auja.database']);
        });
    }

    /**
     * Register the router
     */
    protected function registerRouter()
    {
        $this->app->singleton('auja.router', function($app) {
            return new AujaRouter($app['auja'], $app['router']);
        });
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['auja', 'auja.router', 'auja.database', 'auja.configurator'];
    }

}
