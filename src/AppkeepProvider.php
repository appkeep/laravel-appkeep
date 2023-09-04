<?php

namespace Appkeep\Laravel;

use Appkeep\Laravel\Facades\Appkeep;
use Illuminate\Support\ServiceProvider;
use Appkeep\Laravel\Commands\RunCommand;
use Appkeep\Laravel\Commands\InitCommand;
use Appkeep\Laravel\Commands\ListCommand;
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

        $this->app->singleton(EventCollector::class, function () {
            return new EventCollector();
        });

        $this->app->bind(HttpClient::class, function () {
            return new HttpClient(config('appkeep.key'));
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

        $this->bootForConsole();

        $this->app->booted(function () {
            // Don't do anything if project key is not set.
            if (! config('appkeep.key')) {
                return;
            }

            if ($this->app->runningInConsole()) {
                $this->scheduleRunCommand();
                $this->scheduleBackups();
            }

            // Watch slow queries, scheduled tasks, etc.
            Appkeep::watch($this->app);
        });

        $this->app->terminating(function () {
            // Write in-memory events to cache.
            $this->app->make(EventCollector::class)->persist();
        });
    }

    public function bootForConsole()
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
            BackupCommand::class,
            PostDeployCommand::class,
        ]);
    }

    protected function scheduleRunCommand()
    {
        $schedule = $this->app->make(Schedule::class);

        $schedule->command('appkeep:run')
            ->everyMinute()
            ->runInBackground()
            ->evenInMaintenanceMode();
    }

    protected function scheduleBackups()
    {
        if (! config('appkeep.backups.enabled')) {
            return;
        }

        $backups = $this->app->make(BackupService::class);
        $backups->applyConfig();
        $backups->scheduleBackups();
    }
}
