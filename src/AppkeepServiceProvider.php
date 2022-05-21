<?php

namespace Appkeep\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Appkeep\Laravel\Commands\RunChecksCommand;
use Appkeep\Laravel\Commands\ListChecksCommand;

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
            ListChecksCommand::class,
            RunChecksCommand::class,
        ]);

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);

            $schedule->command('eye:check')
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
