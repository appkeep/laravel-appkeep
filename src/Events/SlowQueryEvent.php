<?php

namespace Appkeep\Laravel\Events;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Events\Dispatchable;

class SlowQueryEvent extends AbstractEvent
{
    use Dispatchable;

    public function __construct(public QueryExecuted $event)
    {
    }
}
