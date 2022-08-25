<?php

namespace Appkeep\Laravel\Checks;

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
     * @var Result
     */
    public function run()
    {
        $connection = $this->connection ?? config('database.default');
        $meta = ['connection' => $connection];

        try {
            DB::connection($connection)->getPdo();
        } catch (Exception $exception) {
            return Result::fail('Could not connect to DB: ' . $exception->getMessage())
                ->meta($meta);
        }

        return Result::ok()->meta($meta);
    }
}
