<?php

namespace Appkeep\Laravel\Events;

use Appkeep\Laravel\Contexts\RequestContext;
use Appkeep\Laravel\Contexts\DatabaseContext;
use Illuminate\Database\Events\QueryExecuted;
use Appkeep\Laravel\Events\Contracts\CollectableEvent;

class SlowQueryEvent extends AbstractEvent implements CollectableEvent
{
    protected $name = 'slow-query';

    private $queryExecutedEvent;

    public function __construct(QueryExecuted $event)
    {
        parent::__construct();

        $this->queryExecutedEvent = $event;

        $this->setContext('database', new DatabaseContext($event->connection));
        $this->setContext('request', new RequestContext());
    }

    /**
     * This hash will help us group the same query across multiple requests on Appkeep side.
     */
    public function dedupeHash(): string
    {
        return md5($this->queryExecutedEvent->sql);
    }

    public function toArray()
    {
        return array_merge(
            parent::toArray(),
            [
                'query' => [
                    'hash' => $this->dedupeHash(),
                    'sql' => $this->queryExecutedEvent->sql,
                ],
                'time' => $this->queryExecutedEvent->time,
            ]
        );
    }
}
