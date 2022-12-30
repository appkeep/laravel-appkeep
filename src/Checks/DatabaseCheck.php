<?php

namespace Appkeep\Laravel\Checks;

use PDO;
use Exception;
use Appkeep\Laravel\Check;
use Appkeep\Laravel\Result;
use Illuminate\Support\Facades\DB;

class DatabaseCheck extends Check
{
    private $connection;

    /**
     * Check a different connection
     */
    public function connection($connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @var \Appkeep\Laravel\Result
     */
    public function run()
    {
        $connection = $this->connection ?? config('database.default');
        $meta = ['connection' => $connection];

        // Reduce connection timeout to 2 seconds.
        $this->setPdoTimeout($connection, 2);

        try {
            DB::connection($connection)->getPdo();
        } catch (Exception $exception) {
            return Result::fail('Could not connect to DB: ' . $exception->getMessage())
                ->meta($meta);
        }

        return Result::ok()->meta($meta);
    }

    private function setPdoTimeout($connection, $timeout)
    {
        $optionsKey = 'database.connections.' . $connection . '.options';

        app('config')->set(
            $optionsKey,
            app('config')->get($optionsKey, []) + [PDO::ATTR_TIMEOUT => $timeout]
        );
    }
}
