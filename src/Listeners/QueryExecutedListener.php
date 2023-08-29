<?php

namespace Appkeep\Laravel\Listeners;

use Appkeep\Laravel\AppkeepService;
use Appkeep\Laravel\Events\SlowQueryEvent;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\QueryException;

class QueryExecutedListener
{
    protected AppkeepService $service;
    public function __construct(private int $slowQueryThreshold)
    {
        $this->service = app('appkeep');
    }

    public function handle(QueryExecuted $event): void
    {
        if ($event->time > $this->slowQueryThreshold) {
            $this->service->slowQueryEvents[] = new SlowQueryEvent($event);
        }
    }
}
