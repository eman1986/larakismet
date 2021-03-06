<?php
/**
 * Larakismet
 *
 * Akismet Client for Laravel 5.
 *
 * Ed Lomonaco
 * https://github.com/eman1986/larakismet
 * MIT License
 */

namespace larakismet\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use larakismet\Akismet;

class AkismetServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../Config/akismet.php' => config_path('akismet.php')
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('Akismet', function($app)
        {
            return new Akismet($app['config']['akismet']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['larakismet\Akismet'];
    }
}