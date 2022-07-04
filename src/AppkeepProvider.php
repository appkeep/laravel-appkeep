<?php

namespace Appkeep\Laravel;

use Illuminate\Support\ServiceProvider;
use Appkeep\Laravel\Commands\RunCommand;
use Appkeep\Laravel\Commands\InitCommand;
use Appkeep\Laravel\Commands\ListCommand;
use Appkeep\Laravel\Commands\LoginCommand;
use Illuminate\Console\Scheduling\Schedule;
use Appkeep\Laravel\Concerns\RegistersDefaultChecks;

class AppkeepProvider extends ServiceProvider
{
    use RegistersDefaultChecks;

    /**
     * Register the application services.
     */
    public function register()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/appkeep.php', 'appkeep');

        $this->app->singleton('appkeep', function () {
            return new AppkeepService();
        });

        $this->registerDefaultChecks();
    }

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

        $this->publishes([
            __DIR__ . '/../config/appkeep.php' => config_path('appkeep.php'),
        ], 'config');

        $this->commands([
            RunCommand::class,
            ListCommand::class,
            InitCommand::class,
            LoginCommand::class,
        ]);

        $this->scheduleRunCommand();
    }

    protected function scheduleRunCommand()
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);

            $schedule->command('appkeep:run')
                ->everyMinute()
                ->runInBackground();
        });
    }
}
