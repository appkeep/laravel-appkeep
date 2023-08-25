<?php

namespace Appkeep\Laravel\Checks;

use Exception;
use Appkeep\Laravel\Check;
use Appkeep\Laravel\Result;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Connectors\SqlServerConnector;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PDO\SqlServerDriver;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\SqlServerConnection;
use UnhandledMatchError;

define('MAX_CONNECTIONS', 50);

class DBConnectionCountCheck extends Check
{

    private $connectionName;

    protected $warnAt;
    protected $failAt;

    public function warnIfConnectionCountIsAbove($value)
    {
        $this->warnAt = (int) $value;

        return $this;
    }

    public function failIfConnectionCountIsAbove($percent)
    {
        $this->failAt = (int) $percent;

        return $this;
    }

    /**
     * Set a different connection name
     */
    public function setConnectionName($connectionName)
    {
        $this->connectionName = $connectionName;

        return $this;
    }

    private function getMaxConnectionCount(ConnectionInterface $connection)
    {
        // TODO Check laravel cache first

        return match (true) { // TODO Create queries for getting active connection counts
            $connection instanceof MySqlConnection => (int) $connection->selectOne('')->Value,
            $connection instanceof PostgresConnection => (int) $connection->selectOne('')->connections,
            $connection instanceof SqlServerConnection => (int) $connection->selectOne('')->connections,
            default => MAX_CONNECTIONS,
        };
    }

    private function getConnectionCount(ConnectionInterface $connection)
    {
        return match (true) {
            $connection instanceof MySqlConnection => (int) $connection->selectOne('show status where variable_name = "threads_connected"')->Value,
            $connection instanceof PostgresConnection => (int) $connection->selectOne('select count(*) as connections from pg_stat_activity')->connections,
            $connection instanceof SqlServerConnection => (int) $connection->selectOne('select COUNT(dbid) as connections FROM sys.sysprocesses WHERE dbid > 0')->connections,
            default => throw new UnhandledMatchError(),
        };
    }

    /**
     * @var Result
     */
    public function run()
    {
        $connection = app(ConnectionResolverInterface::class)->connection($this->connectionName);
        $maxConnectionCount = $this->getMaxConnectionCount($connection);
        $connectionCount = $this->getConnectionCount($connection);
        $meta = ['connection' => $connection];

        try {
            // print_r(config('database.connections'));
        } catch (Exception $exception) {
            return Result::fail('Could not connect to DB: ' . $exception->getMessage())
                ->meta($meta);
        }

        return Result::ok()->meta($meta);
    }
}
