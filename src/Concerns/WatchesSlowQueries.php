<?php

namespace Appkeep\Laravel\Concerns;

use Illuminate\Support\Facades\DB;
use Appkeep\Laravel\EventCollector;
use Illuminate\Database\Connection;
use Appkeep\Laravel\Events\SlowQueryEvent;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Contracts\Foundation\Application;

trait WatchesSlowQueries
{
    public $watchSlowQueries = true;

    public $slowQueryThreshold = 500;

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
        $this->slowQueryThreshold = $threshold;

        return $this;
    }

    public function watchSlowQueries(Application $app)
    {
        if (! $this->watchSlowQueries) {
            return;
        }

        // DB::whenQueryingForLongerThan was added in Laravel 9.18.0
        // Check if the method exists before using it.
        if (version_compare(app()->version(), '9.18.0', '<')) {
            return;
        }

        DB::whenQueryingForLongerThan(
            $this->slowQueryThreshold,
            function (Connection $connection, QueryExecuted $event) use ($app) {
                $app->make(EventCollector::class)->push(
                    new SlowQueryEvent($connection, $event)
                );
            }
        );
    }
}
