<?php

namespace Appkeep\Eye;

use Appkeep\Commands\InitCommand;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Appkeep\Subscribers\ScheduledCommandSubscriber;

class EyeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'appkeep-laravel');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'appkeep-laravel');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('appkeep.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/appkeep-laravel'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/appkeep-laravel'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/appkeep-laravel'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
            $this->commands([
                InitCommand::class,
            ]);

            Event::subscribe(ScheduledCommandSubscriber::class);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'appkeep');

        // Register the main class to use with the facade
        $this->app->singleton('appkeep', function () {
            return new Appkeep();
        });
    }
}
