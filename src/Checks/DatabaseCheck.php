<?php

namespace Appkeep\Laravel\Checks;

use Exception;
use Appkeep\Laravel\Check;
use Appkeep\Laravel\Result;
use Appkeep\Laravel\Enums\Status;
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

        $result = (new Result(Status::OK))->meta([
            'connection_name' => $connectionName,
        ]);

        try {
            DB::connection($connectionName)->getPdo();

            return $result;
        } catch (Exception $exception) {
            return $result->failWith(
                "Could not connect to the database: `{$exception->getMessage()}`"
            );
        }
    }
}
