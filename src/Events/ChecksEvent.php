<?php

namespace Appkeep\Laravel\Events;

use Appkeep\Laravel\Check;
use Appkeep\Laravel\Result;
use Appkeep\Laravel\Facades\Appkeep;

class ChecksEvent extends AbstractEvent
{
    protected $name = 'checks';

    public array $results = [];

    private function serializeResults()
    {
        $data = [];

        foreach ($this->results as $item) {
            list($check, $result) = $item;

            $data[] = [
                'check' => $check->name,
                'scope' => $check->scope,
                'result' => [
                    'status' => $result->status,
                    'message' => $result->message,
                    'summary' => $result->summary,
                    'meta' => $result->meta,
                ],
            ];
        }

        return $data;
    }

    public function toArray()
    {
        return array_merge(
            parent::toArray(),
            [
                'checks' => $this->serializeResults(),
                'all_checks' => Appkeep::registeredChecks(),
            ]
        );
    }

    public function addResult(Check $check, Result $result)
    {
        $this->results[] = [
            $check,
            $result,
        ];
    }
}
