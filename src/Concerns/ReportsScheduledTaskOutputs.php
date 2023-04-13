<?php

namespace Appkeep\Laravel\Concerns;

use Appkeep\Laravel\CronjobOutput;
use Illuminate\Console\Scheduling\Event;

trait ReportsScheduledTaskOutputs
{
    private $scheduledTaskStart;

    public function scheduledTaskStarted(Event $task)
    {
        $this->scheduledTaskStart = microtime(true);
    }

    private function getScheduledTaskRunDuration()
    {
        return microtime(true) - $this->scheduledTaskStart;
    }

    public function scheduledTaskFailed(Event $task, $output)
    {
        $duration = $this->getScheduledTaskRunDuration();

        $output = CronjobOutput::fromScheduledTask($task)
            ->failed()
            ->setDuration($duration)
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

        $output = CronjobOutput::fromScheduledTask($task)
            ->succeeded()
            ->setDuration($duration)
            ->setOutput($output);

        try {
            $this->client()->sendCronjobOutput($output);
        } catch (\Exception $e) {
            report($e);
        }
    }
}
