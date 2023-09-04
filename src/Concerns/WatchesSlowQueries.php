<?php

namespace Appkeep\Laravel\Concerns;

use Appkeep\Laravel\EventCollector;
use Illuminate\Support\Facades\Event;
use Appkeep\Laravel\Events\SlowQueryEvent;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Contracts\Foundation\Application;

trait WatchesSlowQueries
{
    public $watchSlowQueries = true;

    public static $slowQueryThreshold = 500;

    /**
     * Disable monitoring of scheduled tasks.
     */
    public function dontWatchSlowQueries()
    {
        $this->watchSlowQueries = false;

        return $this;
    }

    /**
     * Change the threshold for slow queries in milliseconds.
     * By default, Appkeep will report queries that take longer than 500ms.
     */
    public function reportQueriesSlowerThan($threshold)
    {
        static::$slowQueryThreshold = $threshold;

        return $this;
    }

    public function watchSlowQueries(Application $app)
    {
        if (! $this->watchSlowQueries) {
            return;
        }

        Event::listen(QueryExecuted::class, function (QueryExecuted $event) use ($app) {
            if ($event->time < static::$slowQueryThreshold) {
                return;
            }

            $app->make(EventCollector::class)->push(
                new SlowQueryEvent($event)
            );
        });
    }
}
