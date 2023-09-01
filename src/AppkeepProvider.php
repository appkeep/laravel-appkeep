<?php

namespace Appkeep\Laravel;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Appkeep\Laravel\Commands\RunCommand;
use Illuminate\Console\Scheduling\Event;
use Appkeep\Laravel\Commands\InitCommand;
use Appkeep\Laravel\Commands\ListCommand;
use Appkeep\Laravel\Commands\LoginCommand;
use Illuminate\Console\Scheduling\Schedule;
use Appkeep\Laravel\Commands\PostDeployCommand;
use Appkeep\Laravel\Concerns\RegistersEventListeners;
use Appkeep\Laravel\Concerns\RegistersDefaultChecks;
use Appkeep\Laravel\Listeners\SlowQueryHandler;

class AppkeepProvider extends ServiceProvider
{
    use RegistersDefaultChecks, RegistersEventListeners;

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/appkeep.php', 'appkeep');

        $this->app->singleton('appkeep', function () {
            return new AppkeepService();
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
        DB::whenQueryingForLongerThan(500, function ($connection, $event) {
            dd($connection);
            SlowQueryHandler::handle(SlowQueryHandler::$fileName, $event);
        });
        $this->registerEventListeners();

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

            $this->watchScheduledTasks($schedule);
        });
    }

    protected function watchScheduledTasks(Schedule $schedule)
    {
        collect($schedule->events())
            ->filter(function ($event) {
                logger($event->command);
                // Don't monitor the Appkeep scheduled task itself.
                return $event->command && ! str_contains($event->command, 'appkeep:run');
            })
            ->each(function (Event $event) {
                /**
                 * @var AppkeepService
                 */
                $appkeep = app('appkeep');

                $event->before(fn () => $appkeep->scheduledTaskStarted($event));

                $event->onSuccessWithOutput(fn () => $appkeep->scheduledTaskCompleted($event));

                $event->onFailureWithOutput(fn () => $appkeep->scheduledTaskFailed($event));
            });
    }
}
