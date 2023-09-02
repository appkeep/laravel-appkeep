<?php

namespace Appkeep\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;

class BatchSlowQueryEvent extends AbstractEvent
{
    use Dispatchable;

    protected $name = 'batch-slow-queries';
    protected $data = [];
    protected $filename;

    private $uniqueQueries = [];

    public function __construct($slowQueries)
    {
        parent::__construct();
        $this->uniqueQueries = $this->dedupeQueries($slowQueries);
    }

    private function dedupeQueries($slowQueries)
    {
        $uniqueQueries = [];
        foreach ($slowQueries as $query) {
            $event = (array)$query['event'];
            $key = $event['sql'];
            if (array_key_exists($key, $uniqueQueries)) {
                $uniqueQueries[$key][] =
                    array_intersect_key(
                        $event,
                        array_flip(['connectionName', 'time', 'context'])
                    );
            } else {
                $uniqueQueries[] = [$key => [
                    array_intersect_key(
                        $event,
                        array_flip(['connectionName', 'time', 'context'])
                    )
                ]];
            }
        }
        return $uniqueQueries;
    }
}
