<?php namespace Label305\AujaLaravel;

use Illuminate\Support\ServiceProvider;
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

        $app->bind('auja', 'Label305\AujaLaravel\Auja');
        $app->singleton('Label305\AujaLaravel\Auja', function ($app) {
            return new Auja($app, $this->getModelNames());
        });

        $app->bind('Label305\AujaLaravel\Logging\Logger', 'Label305\AujaLaravel\Logging\LaravelLogger');


                $app->bind('Label305\AujaLaravel\Database\DatabaseHelper', function() use ($app) {

                    $config = $app['config']['auja-laravel'] ?: $app['config']['auja-laravel::config'];

                    dd($config);

                    switch ($config['database']) {
                        case 'mysql':
                            return new \Label305\AujaLaravel\Database\MySQLDatabaseHelper();
                            break;
                        default:
                            throw new NoDatabaseHelperException('No Auja database helper for ' . $config['database']);
                            break;
                    }
                });

        $app->singleton('Label305\AujaLaravel\Config\AujaConfigurator');

        $app->bind('AujaRouter', 'Label305\AujaLaravel\Routing\AujaRouter');
        $app->singleton('aujarouter', function($app) {
            return new AujaRouter($app['auja'], $app['Illuminate\Routing\Router']);
        });
    }

    public function provides() {
        return ['auja', 'aujarouter'];
    }

    /**
     * Returns a String array of model names, e.g. ['Club', 'Team'].
     *
     * @return String[] The model names.
     */
    public function getModelNames() {

        $config = $this->getConfig();
        if (!array_key_exists('models', $config)) {
            return [];
        }

        return $config['models'];
    }

    /**
     * Get config values
     * Allows for both /app/config/auja.php as well as /app/config/packages/auja-laravel/config.php
     *
     * @return array
     */
    public function getConfig() {
        $config = $this->app['config']['auja-laravel'] ?: $this->app['config']['auja-laravel::config'];
        return $config;
    }

    private function checkForMisconfiguredConfigFile() {
        if (php_sapi_name() == 'cli') {
            return;
        }

        $config = $this->getConfig();

        foreach ($this->mandatoryConfigKeys() as $key) {
            if (!array_key_exists($key, $config)) {
                throw new MandatoryConfigKeyMisconfiguredException('Could not found config value for ' . $key);
            }
        }
    }

    private function mandatoryConfigKeys() {
        return [
            'database',
            'models',
            'route',
            'configurations'
        ];
    }

}
