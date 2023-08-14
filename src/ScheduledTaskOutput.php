<?php

namespace Appkeep\Laravel;

use DateTime;
use Illuminate\Console\Scheduling\Event;
use Appkeep\Laravel\Support\ScheduledTaskId;

class ScheduledTaskOutput
{
    public string $id;
    public bool $success = true;
    public ?float $duration = null;
    public ?string $output = null;
    public ?DateTime $startedAt = null;
    public ?DateTime $finishedAt = null;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromScheduledTask(Event $task)
    {
        $output = new self(ScheduledTaskId::get($task));

        return $output->setOutput(self::getEventOutput($task));
    }

    private static function getEventOutput(Event $event)
    {
        if (
            ! $event->output ||
            $event->output === $event->getDefaultOutput() ||
            $event->shouldAppendOutput ||
            ! file_exists($event->output)
        ) {
            return '';
        }

        return trim(file_get_contents($event->output));
    }

    public function succeeded()
    {
        $this->success = true;

        return $this;
    }

    public function failed()
    {
        $this->success = false;

        return $this;
    }

    public function setDuration(float $duration)
    {
        $this->duration = $duration;

        return $this;
    }

    public function setOutput(string $output)
    {
        $this->output = $output;

        return $this;
    }

    public function setStartedAt(DateTime $startedAt)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function setFinishedAt(DateTime $finishedAt)
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }
}
