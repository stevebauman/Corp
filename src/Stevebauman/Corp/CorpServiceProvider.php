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
     * The configuration separator for packages.
     * Allows compatibility with Laravel 4 and 5.
     *
     * @var string
     */
    public static $configSeparator = '::';

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        if (method_exists($this, 'package')) {
            /*
             * Looks like we're using Laravel 4, let's use the
             * package method to easily register everything
             */
            $this->package('stevebauman/corp');
        } else {
            /*
             * Looks like we're using Laravel 5, let's set
             * our configuration file to be publishable
             */
            $this->publishes([
                __DIR__.'/../../config/config.php' => config_path('corp.php'),
            ], 'config');

            $this::$configSeparator = '.';
        }
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
