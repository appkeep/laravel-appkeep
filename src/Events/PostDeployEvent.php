<?php

namespace Appkeep\Laravel\Events;

use Appkeep\Laravel\Facades\Appkeep;
use Appkeep\Laravel\Contexts\GitContext;
use Appkeep\Laravel\Contexts\SpecsContext;
use Illuminate\Console\Scheduling\Schedule;
use Appkeep\Laravel\Support\ScheduledEventId;

/**
 * This event sends data that changes with every deployment to Appkeep.
 * It includes things like scheduled tasks, queue config, deployed commit hash, etc.
 *
 * We recommend running "php artisan appkeep:post-deploy" after every deployment.
 */
class PostDeployEvent extends AbstractEvent
{
    protected $name = 'post-deploy';

    public function __construct()
    {
        parent::__construct();

        $this->addDependenciesContext()
            ->setContext('git', new GitContext)
            ->setContext('specs', new SpecsContext)
            ->setContext('appkeep', [
                'url' => route('appkeep.explore'),
            ]);
    }

    public function toArray()
    {
        return array_merge(
            parent::toArray(),
            [
                'schedule' => $this->getScheduledTasks(),
            ]
        );
    }

    private function getScheduledTasks()
    {
        $events = app(Schedule::class)->events();

        return collect($events)
            // We can't track scheduled events without a signature (callbacks)
            // We also don't want to track scheduled events that won't fire off in this environment anyway.
            ->filter(function ($event) {
                return $event->command && empty($event->environments) || in_array(
                    app()->environment(),
                    $event->environments
                );
            })
            ->map(function ($event) {
                return [
                    'id' => ScheduledEventId::get($event),
                    'command' => $event->command,
                    'description' => $event->description,
                    'expression' => $event->expression,
                    'timezone' => $event->timezone,
                    'user' => $event->user,
                    'evenInMaintenanceMode' => $event->evenInMaintenanceMode,
                    'withoutOverlapping' => $event->withoutOverlapping,
                    'onOneServer' => $event->onOneServer,
                    'runInBackground' => $event->runInBackground,
                ];
            });
    }

    private function addDependenciesContext()
    {
        return $this->setContext('dependencies', [
            'laravel/framework' => app()->version(),
            'appkeep/laravel-appkeep' => Appkeep::version(),
        ]);
    }
}
