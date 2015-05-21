<?php

namespace Stevebauman\Corp;

use Illuminate\Support\ServiceProvider;

class CorpServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->package('stevebauman/corp');
    }
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app['corp'] = $this->app->share(function ($app) {
            return new Corp($app['config']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['corp'];
    }
}
