<?php

namespace Appkeep\Laravel\Events;

use Illuminate\Database\Connection;
use Appkeep\Laravel\Contexts\DatabaseContext;
use Illuminate\Database\Events\QueryExecuted;

class SlowQueryEvent extends AbstractEvent
{
    protected $name = 'slow-query';

    private $queryExecutedEvent;

    public function __construct(Connection $connection, QueryExecuted $event)
    {
        parent::__construct();

        $this->queryExecutedEvent = $event;

        $this->setContext('database', new DatabaseContext($connection));
    }

    /**
     * This hash will help us group the same query across multiple requests on Appkeep side.
     */
    protected function queryHash()
    {
        return md5($this->queryExecutedEvent->sql);
    }

    public function toArray()
    {
        return array_merge(
            parent::toArray(),
            [
                'query' => [
                    'hash' => $this->queryHash(),
                    'sql' => $this->queryExecutedEvent->sql,
                ],
                'time' => $this->queryExecutedEvent->time,
            ]
        );
    }
}
