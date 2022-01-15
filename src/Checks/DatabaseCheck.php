<?php

namespace Appkeep\Eye\Checks;

use Exception;
use Appkeep\Eye\Check;
use Appkeep\Eye\Result;
use Illuminate\Support\Facades\DB;

class DatabaseCheck extends Check
{
    private $connectionName;

    public function connectionName($connectionName)
    {
        $this->connectionName = $connectionName;

        return $this;
    }

    /**
     * @var Result
     */
    public function run()
    {
        $connectionName = $this->connectionName ?? config('database.default');

        $result = Result::ok()->meta([
            'connection_name' => $connectionName,
        ]);

        try {
            DB::connection($connectionName)->getPdo();

            return $result->ok();
        } catch (Exception $exception) {
            return $result->fail("Could not connect to the database: `{$exception->getMessage()}`");
        }
    }
}
