<?php

namespace Appkeep\Laravel\Concerns;

use Appkeep\Laravel\ScheduledTaskOutput;
use Illuminate\Console\Scheduling\Event;

trait ReportsScheduledTaskOutputs
{
    public $scheduledTaskMonitoringEnabled = true;

    private $scheduledTaskStartMs;
    private $scheduledTaskStartedAt;

    /**
     * Disables scheduled task monitoring.
     */
    public function dontMonitorScheduledTasks()
    {
        $this->scheduledTaskMonitoringEnabled = false;

        return $this;
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
        if (! app('appkeep')->scheduledTaskMonitoringEnabled) {
            // If monitoring is disabled, don't send the output
            return;
        }

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
        if (! app('appkeep')->scheduledTaskMonitoringEnabled) {
            // If monitoring is disabled, don't send the output
            return;
        }

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
