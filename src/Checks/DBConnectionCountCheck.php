<?php

namespace Appkeep\Laravel\Checks;

use Exception;
use Appkeep\Laravel\Check;
use Appkeep\Laravel\Result;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\SqlServerConnection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use UnhandledMatchError;

const DEFAULT_MAX_CONNECTIONS = 50;
const CACHE_TTL = 24;
const BASE_CACHE_KEY = 'AppkeepDBConnectionCounts';

class DBConnectionCountCheck extends Check
{
    private $connectionName;
    protected $warnAt;
    protected $failAt;

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
     * Set a different connection name
     */
    public function setConnectionName($connectionName)
    {
        $this->connectionName = $connectionName;

        return $this;
    }

    private function getCacheKey()
    {
        return  BASE_CACHE_KEY . $this->connectionName;
    }

    private function getMaxConnectionCountFromCache()
    {
        return Cache::get($this->getCacheKey());
    }

    private function setMaxConnectionCountCache(int $maxConnectionCount)
    {
        Cache::put($this->getCacheKey(), $maxConnectionCount, now()->addHours(CACHE_TTL));
    }

    private function getMaxConnectionCount(ConnectionInterface $connection): int
    {
        $cachedMaxConnectionCount = $this->getMaxConnectionCountFromCache();
        if (!is_null($cachedMaxConnectionCount)) {
            return $cachedMaxConnectionCount;
        }

        $maxConnectionCount = NULL;
        switch (true) {
            case $connection instanceof MySqlConnection:
                $maxConnectionCount = (int) $connection->selectOne("show variables like 'max_connections'")['Value'];
                break;
            case $connection instanceof PostgresConnection:
                $maxConnectionCount = (int) $connection->selectOne("SHOW max_connections;")['max_connections'];
                break;
            case $connection instanceof SqlServerConnection:
                $maxConnectionCount = (int) $connection->selectOne("SELECT @@MAX_CONNECTIONS AS 'max_connections'")['max_connections'];
                break;
        };

        if (!is_null($maxConnectionCount)) {
            $this->setMaxConnectionCountCache($maxConnectionCount);
            return $maxConnectionCount;
        }

        if ($this->failAt) {
            return $this->failAt;
        }

        return DEFAULT_MAX_CONNECTIONS;
    }

    private function getConnectionCount(ConnectionInterface $connection)
    {
        switch (true) {
            case $connection instanceof MySqlConnection:
                return (int) $connection->selectOne('show status where variable_name = "threads_connected"')['Value'];
            case $connection instanceof PostgresConnection:
                return (int) $connection->selectOne('select count(*) as connections from pg_stat_activity')['connections'];
            case $connection instanceof SqlServerConnection:
                return (int) $connection->selectOne('select COUNT(dbid) as connections FROM sys.sysprocesses WHERE dbid > 0')['connections'];
            default:
                throw new UnhandledMatchError();
        }
    }

    /**
     * @var Result
     */
    public function run()
    {
        $meta = ['connection' => $this->connectionName];
        try {
            // $connection = app(ConnectionResolverInterface::class)->connection($this->connectionName);
            $connection = DB::connection($this->connectionName);
        } catch (Exception $exception) {
            return Result::fail("Could not find connection with name {$this->connectionName}: " . $exception->getMessage())
                ->meta($meta);
        }

        try {
            $connectionCount = $this->getConnectionCount($connection);
        } catch (Exception $exception) {
            return Result::fail("Could not retrieve the number of active connections for '{$this->connectionName}': " . $exception->getMessage())
                ->meta($meta);
        }

        $maxConnectionCount = $this->getMaxConnectionCount($connection);

        if ($connectionCount >= $maxConnectionCount) {
            return Result::fail("Connection count has reached the limit {$maxConnectionCount} for {$this->connectionName}")
                ->meta($meta);
        }

        if ($this->warnAt && $connectionCount >= $this->warnAt) {
            return Result::warn("Connection count for {$this->connectionName} is at or above {$this->warnAt}")
                ->meta($meta);
        }

        return Result::ok()->meta($meta);
    }
}
