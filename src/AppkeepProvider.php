<?php

namespace Appkeep\Laravel;

use Illuminate\Support\ServiceProvider;
use Appkeep\Laravel\Commands\RunCommand;
use Illuminate\Console\Scheduling\Event;
use Appkeep\Laravel\Commands\InitCommand;
use Appkeep\Laravel\Commands\ListCommand;
use Appkeep\Laravel\Commands\LoginCommand;
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
            PostDeployCommand::class,
        ]);

        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);

            $schedule->command('appkeep:run')
                ->everyMinute()
                ->runInBackground()
                ->evenInMaintenanceMode();

            collect($schedule->events())
                ->filter(function ($event) {
                    return $event->command;
                })
                ->each(function (Event $event) {
                    /**
                     * @var AppkeepService
                     */
                    $appkeep = app('appkeep');

                    $event->before(fn () => $appkeep->scheduledTaskStarted($event));

                    $event->onFailureWithOutput(
                        fn ($output) => $appkeep->scheduledTaskFailed($event, $output)
                    );

                    $event->onSuccessWithOutput(
                        fn ($output) => $appkeep->scheduledTaskCompleted($event, $output)
                    );
                });
        });
    }
}
