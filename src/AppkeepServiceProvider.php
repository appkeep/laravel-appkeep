<?php

namespace Appkeep\Laravel;

use Illuminate\Support\ServiceProvider;
use Appkeep\Laravel\Commands\RunCommand;
use Appkeep\Laravel\Commands\ListCommand;
use Illuminate\Console\Scheduling\Schedule;

class AppkeepServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/appkeep.php' => config_path('appkeep.php'),
        ], 'config');

        $this->commands([
            ListCommand::class,
            RunCommand::class,
        ]);

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);

            $schedule->command('appkeep:run')
                ->everyMinute()
                ->runInBackground();
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/appkeep.php', 'appkeep');

        $this->app->singleton(Appkeep::class, function () {
            return new Appkeep();
        });
    }
}
