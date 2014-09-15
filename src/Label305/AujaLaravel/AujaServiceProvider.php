<?php namespace Label305\AujaLaravel;

use Illuminate\Support\ServiceProvider;

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
        $this->app->bind('Label305\AujaLaravel\Database\DatabaseHelper', 'Label305\AujaLaravel\Database\MySQLDatabaseHelper');
        $this->app->bind('Label305\AujaLaravel\I18N\Translator', 'Label305\AujaLaravel\I18N\LaravelTranslator');
        $this->app->bind('Label305\AujaLaravel\Logging\Logger', 'Label305\AujaLaravel\Logging\LaravelLogger');
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
