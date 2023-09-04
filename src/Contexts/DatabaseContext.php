<?php

namespace Appkeep\Laravel\Contexts;

use Illuminate\Database\Connection;
use Illuminate\Contracts\Support\Arrayable;

class DatabaseContext implements Arrayable
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function toArray()
    {
        return [
            'name' => $this->connection->getName(),
            'driver' => $this->connection->getDriverName(),
        ];
    }
}
