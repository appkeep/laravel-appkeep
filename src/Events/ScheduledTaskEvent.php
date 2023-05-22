<?php

namespace Appkeep\Laravel\Events;

use Appkeep\Laravel\ScheduledTaskOutput;

class ScheduledTaskEvent extends AbstractEvent
{
    protected $name = 'scheduled-task';

    public function __construct(private ScheduledTaskOutput $output)
    {
        parent::__construct();
    }

    public function toArray()
    {
        return array_merge(
            parent::toArray(),
            [
                'output' => [
                    'id' => $this->output->id,
                    'success' => $this->output->success,
                    'duration' => $this->output->duration,
                    'output' => $this->output->output,
                    'started_at' => $this->output->startedAt,
                    'finished_at' => $this->output->finishedAt,
                ],
            ]
        );
    }
}
