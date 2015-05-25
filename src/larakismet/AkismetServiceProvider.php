<?php
namespace larakismet;

use Illuminate\Support\ServiceProvider;

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
        $this->app->singleton('larakismet', function($app)
        {
            return new Akismet($app['config']['akismet']);
        });
    }
}