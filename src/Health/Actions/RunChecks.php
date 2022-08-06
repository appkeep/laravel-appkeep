<?php

namespace Appkeep\Laravel\Health\Actions;

use Appkeep\Laravel\Health\Result;
use Illuminate\Support\Collection;

class RunChecks
{
    public function __invoke(Collection $checks): Collection
    {
        return $checks
            ->map(fn ($check) => rescue(
                // Run the check...
                fn () => $this->toOutput($check, $check->run()),
                // If it crashes, catch and return the exception message
                fn ($e) => $this->toOutput(
                    $check,
                    Result::crash($e->getMessage())
                )
            ));
    }

    public function toOutput($check, $result): array
    {
        return [
            'check' => $check->name,
            'result' => [
                'status' => $result->status,
                'message' => $result->message,
                'summary' => $result->summary,
                'meta' => $result->meta,
            ],
        ];
    }
}
