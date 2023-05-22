<?php

namespace Appkeep\Laravel\Concerns;

use Appkeep\Laravel\CronjobOutput;
use Illuminate\Console\Scheduling\Event;

trait ReportsScheduledTaskOutputs
{
    private $scheduledTaskStartMs;
    private $scheduledTaskStartedAt;

    public function scheduledTaskStarted(Event $task)
    {
        $this->scheduledTaskStartMs = microtime(true);
        $this->scheduledTaskStartedAt = now();
    }

    private function getScheduledTaskRunDuration()
    {
        return microtime(true) - $this->scheduledTaskStartMs;
    }

    public function scheduledTaskFailed(Event $task, $output)
    {
        $duration = $this->getScheduledTaskRunDuration();
        $finishedAt = now();

        $output = CronjobOutput::fromScheduledTask($task)
            ->failed()
            ->setDuration($duration)
            ->setStartedAt($this->scheduledTaskStartedAt)
            ->setFinishedAt($finishedAt)
            ->setOutput($output);

        try {
            $this->client()->sendCronjobOutput($output);
        } catch (\Exception $e) {
            report($e);
        }
    }

    public function scheduledTaskCompleted(Event $task, $output)
    {
        $duration = $this->getScheduledTaskRunDuration();
        $finishedAt = now();

        $output = CronjobOutput::fromScheduledTask($task)
            ->succeeded()
            ->setDuration($duration)
            ->setStartedAt($this->scheduledTaskStartedAt)
            ->setFinishedAt($finishedAt)
            ->setOutput($output);

        try {
            $this->client()->sendCronjobOutput($output);
        } catch (\Exception $e) {
            report($e);
        }
    }
}
