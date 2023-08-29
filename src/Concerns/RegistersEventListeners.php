<?php

namespace Appkeep\Laravel\Concerns;

use Illuminate\Support\Facades\Event;

trait RegisterEventListeners
{
    protected function registerEventListeners()
    {
        Event::listen(
            QueryExecuted::class,
            [QueryExecutedListener::class, 'handle']
        );

        Event::listen(
            SlowQueryEvent::class,
            [SlowQueryListener::class, 'handle']
        );
    }
}
