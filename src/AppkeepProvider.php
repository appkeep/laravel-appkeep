<?php

namespace Appkeep\Laravel;

use Illuminate\Support\ServiceProvider;
use Appkeep\Laravel\Commands\RunCommand;
use Appkeep\Laravel\Commands\InitCommand;
use Appkeep\Laravel\Commands\ListCommand;
use Appkeep\Laravel\Backups\BackupService;
use Appkeep\Laravel\Commands\LoginCommand;
use Appkeep\Laravel\Commands\BackupCommand;
use Illuminate\Console\Scheduling\Schedule;
use Appkeep\Laravel\Commands\PostDeployCommand;
use Appkeep\Laravel\Concerns\RegistersDefaultChecks;

class AppkeepProvider extends ServiceProvider
{
    use RegistersDefaultChecks;

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/appkeep.php', 'appkeep');

        $this->app->singleton('appkeep', function () {
            return new AppkeepService();
        });

        if ($this->app->runningInConsole()) {
            $this->registerDefaultChecks();
        }
    }

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    public function bootForConsole()
    {
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
            BackupCommand::class,
            PostDeployCommand::class,
        ]);

        $this->app->booted(function () {
            // Don't schedule anything if project key is not set.
            if (!config('appkeep.key')) {
                return;
            }

            $schedule = $this->app->make(Schedule::class);

            $schedule->command('appkeep:run')
                ->everyMinute()
                ->runInBackground();

            // Schedule backup tasks, if it's enabled.
            if (config('appkeep.backups.enabled')) {
                $backups = $this->app->make(BackupService::class);
                $backups->applyConfig();
                $backups->scheduleBackups();
            }
        });
    }
}
