<?php

namespace Appkeep\Laravel\Concerns;

use Appkeep\Laravel\ScheduledTaskOutput;
use Illuminate\Console\Scheduling\Event;

trait ReportsScheduledTaskOutputs
{
    private $scheduledTaskStartMs;
    private $scheduledTaskStartedAt;

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

    public function scheduledTaskFailed(Event $task, $output)
    {
        $duration = $this->getScheduledTaskRunDuration();
        $finishedAt = now();

        $output = ScheduledTaskOutput::fromScheduledTask($task)
            ->failed()
            ->setDuration($duration)
            ->setStartedAt($this->scheduledTaskStartedAt)
            ->setFinishedAt($finishedAt)
            ->setOutput($output);

        try {
            $this->client()->sendScheduledTaskOutput($output);
        } catch (\Exception $e) {
            report($e);
        }
    }

    public function scheduledTaskCompleted(Event $task, $output)
    {
        $duration = $this->getScheduledTaskRunDuration();
        $finishedAt = now();

        $output = ScheduledTaskOutput::fromScheduledTask($task)
            ->succeeded()
            ->setDuration($duration)
            ->setStartedAt($this->scheduledTaskStartedAt)
            ->setFinishedAt($finishedAt)
            ->setOutput($output);

        try {
            $this->client()->sendScheduledTaskOutput($output);
        } catch (\Exception $e) {
            report($e);
        }
    }
}
