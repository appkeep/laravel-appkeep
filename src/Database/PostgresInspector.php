<?php

namespace Appkeep\Laravel\Database;

use Illuminate\Database\PostgresConnection;

class PostgresInspector implements DatabaseInspector
{
    private PostgresConnection $connection;

    public function __construct(PostgresConnection $connection)
    {
        $this->connection = $connection;
    }

    public function maximumConnectionCount(): int
    {
        return (int) $this->connection->selectOne('SHOW max_connections;')->max_connections;
    }

    public function currentConnectionCount(): int
    {
        return (int) $this->connection->selectOne('select count(*) as connections from pg_stat_activity')->connections;
    }
}
