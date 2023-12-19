<?php

namespace Appkeep\Laravel\Concerns;

use Appkeep\Laravel\ScheduledTaskOutput;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Foundation\Application;

trait WatchesScheduledTasks
{
    public $watchScheduledTasks = true;

    private $scheduledTaskStartMs;

    private $scheduledTaskStartedAt;

    /**
     * Disable monitoring of scheduled tasks.
     */
    public function dontWatchScheduledTasks()
    {
        $this->watchScheduledTasks = false;

        return $this;
    }

    protected function watchScheduledTasks(Application $app)
    {
        if (! $this->watchScheduledTasks) {
            return;
        }

        if (! $app->runningInConsole()) {
            return;
        }

        $schedule = $app->make(Schedule::class);

        collect($schedule->events())
            ->filter(function ($event) {
                // Don't monitor the Appkeep scheduled task itself.
                return $event->command && ! str_contains($event->command, 'appkeep:run');
            })
            ->each(function (Event $event) {
                $event->before(fn () => $this->scheduledTaskStarted($event));

                $event->onSuccessWithOutput(fn () => $this->scheduledTaskCompleted($event));

                $event->onFailureWithOutput(fn () => $this->scheduledTaskFailed($event));
            });
    }

    public function scheduledTaskStarted(Event $task)
    {
        $this->scheduledTaskStartMs = hrtime(true);
        $this->scheduledTaskStartedAt = now();
    }

    /**
     * Get the duration of the scheduled task run in milliseconds
     */
    private function getScheduledTaskRunDuration(): int
    {
        return round((hrtime(true) - $this->scheduledTaskStartMs) / 1e+6);
    }

    public function scheduledTaskFailed(Event $task)
    {
        $duration = $this->getScheduledTaskRunDuration();
        $finishedAt = now();

        $output = ScheduledTaskOutput::fromScheduledTask($task)
            ->failed()
            ->setDuration($duration)
            ->setStartedAt($this->scheduledTaskStartedAt)
            ->setFinishedAt($finishedAt);

        try {
            $this->client()->sendScheduledTaskOutput($output);
        } catch (\Exception $e) {
            report($e);
        }
    }

    public function scheduledTaskCompleted(Event $task)
    {
        $duration = $this->getScheduledTaskRunDuration();
        $finishedAt = now();

        $output = ScheduledTaskOutput::fromScheduledTask($task)
            ->succeeded()
            ->setDuration($duration)
            ->setStartedAt($this->scheduledTaskStartedAt)
            ->setFinishedAt($finishedAt);

        try {
            $this->client()->sendScheduledTaskOutput($output);
        } catch (\Exception $e) {
            report($e);
        }
    }
}
