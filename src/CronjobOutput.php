<?php

namespace Appkeep\Laravel;

use Illuminate\Console\Scheduling\Event;
use Appkeep\Laravel\Support\ScheduledTaskId;

class CronjobOutput
{
    public bool $success = true;
    public ?float $duration = null;
    public ?string $output = null;

    public function __construct(public string $id)
    {
    }

    public static function fromScheduledTask(Event $task)
    {
        return new self(
            ScheduledTaskId::get($task)
        );
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
}
