<?php

namespace Appkeep\Laravel\Database;

use Illuminate\Database\MySqlConnection;

class MySqlInspector implements DatabaseInspector
{
    private MySqlConnection $connection;

    public function __construct(MySqlConnection $connection)
    {
        $this->connection = $connection;
    }

    public function maximumConnectionCount(): int
    {
        return (int) $this->connection->selectOne('SHOW VARIABLES LIKE "max_connections"')->Value;
    }

    public function currentConnectionCount(): int
    {
        return (int) $this->connection->selectOne('SHOW STATUS LIKE "threads_connected"')->Value;
    }
}
