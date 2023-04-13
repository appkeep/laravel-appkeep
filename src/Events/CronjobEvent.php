<?php

namespace Appkeep\Laravel\Events;

use Appkeep\Laravel\CronjobOutput;

class CronjobEvent extends AbstractEvent
{
    protected $name = 'cronjob';

    public function __construct(private CronjobOutput $output)
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
                ],
            ]
        );
    }
}
