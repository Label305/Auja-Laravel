<?php namespace Label305\AujaLaravel;

use Illuminate\Support\ServiceProvider;
use Label305\AujaLaravel\Routing\AujaRouter;
use Label305\AujaLaravel\Exception\NoDatabaseHelperException;

class AujaServiceProvider extends ServiceProvider {

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

        $app = $this->app;
        $config = $app['config']['auja'] ?: $app['config']['auja::config'];

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

        $config = $this->app['config']['auja'] ?: $this->app['config']['auja::config'];
        return $config['models'];
    }

}
