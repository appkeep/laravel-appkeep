<?php

namespace Appkeep\Laravel\Checks;

use Exception;
use Appkeep\Laravel\Check;
use Appkeep\Laravel\Result;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Appkeep\Laravel\Database\MySqlInspector;
use Appkeep\Laravel\Database\PostgresInspector;

class DatabaseConnectionCountCheck extends Check
{
    protected $warnAt;

    protected $failAt;

    private $connection;

    private $inspectors = [
        'mysql' => MySqlInspector::class,
        'pgsql' => PostgresInspector::class,
    ];

    /**
     * Check a different connection
     */
    public function connection($connection)
    {
        $this->connection = $connection;

        return $this;
    }

    public function warnIfConnectionCountIsAbove($count)
    {
        $this->warnAt = (int) $count;

        return $this;
    }

    public function failIfConnectionCountIsAbove($count)
    {
        $this->failAt = (int) $count;

        return $this;
    }

    /**
     * @var Result
     */
    public function run()
    {
        $connection = $this->connection ?? config('database.default');
        $meta = ['connection' => $connection];

        $db = DB::connection($connection);
        $driver = $db->getDriverName();

        // If there's no inspector for this connection, we can't check it.
        // In that case, we'll just skip this check.
        if (! isset($this->inspectors[$driver])) {
            return Result::ok()->meta($meta);
        }

        /**
         * @var DatabaseIn
         */
        $inspector = app($this->inspectors[$driver], [
            'connection' => $db,
        ]);

        $cacheKey = 'appkeep.db.' . $connection . '.connection_count';

        $maxConnections = Cache::remember(
            $cacheKey,
            now()->addHours(2),
            function () use ($inspector) {
                try {
                    return $inspector->maximumConnectionCount();
                } catch (Exception $e) {
                    // Failed to get the maximum connection count.
                    report($e);

                    // Let's just assume the default.
                    return 100;
                }
            }
        );

        $currentConnections = $inspector->currentConnectionCount();

        if (is_null($this->warnAt)) {
            $this->warnAt = floor($maxConnections * 0.8);
        }

        if (is_null($this->failAt)) {
            $this->failAt = $maxConnections - 1;
        }

        if ($currentConnections >= $this->failAt) {
            return Result::fail("Too many active database connections.")
                ->summary($currentConnections)
                ->meta($meta);
        }

        if ($currentConnections >= $this->warnAt) {
            return Result::warn("Approaching the maximum number of database connections.")
                ->summary($currentConnections)
                ->meta($meta);
        }

        return Result::ok()
            ->summary($currentConnections)
            ->meta($meta);
    }
}
