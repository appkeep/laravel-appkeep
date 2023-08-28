<?php

namespace Appkeep\Laravel\Database;

use Illuminate\Database\SqlServerConnection;

class SqlServerInspector implements DatabaseInspector
{
    private SqlServerConnection $connection;

    public function __construct(SqlServerConnection $connection)
    {
        $this->connection = $connection;
    }

    public function maximumConnectionCount(): int
    {
        return $this->connection->selectOne("SELECT @@MAX_CONNECTIONS AS 'max_connections'")->max_connections;
    }

    public function currentConnectionCount(): int
    {
        return $this->connection->selectOne('SELECT COUNT(dbid) as connections FROM sys.sysprocesses WHERE dbid > 0')->connections;
    }
}
