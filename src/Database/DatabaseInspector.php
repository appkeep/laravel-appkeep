<?php

namespace Appkeep\Laravel\Database;

interface DatabaseInspector
{
    /**
     * The number of maximum allowed connections to the database.
     * This depends on your database configuration, and server capacity.
     */
    public function maximumConnectionCount(): int;

    /**
     * The number of current connections to the database.
     */
    public function currentConnectionCount(): int;
}
