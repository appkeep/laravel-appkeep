<?php

namespace Appkeep\Laravel\Commands;

use Appkeep\Laravel\Result;
use Appkeep\Laravel\Appkeep;
use Illuminate\Console\Command;
use Appkeep\Laravel\Enums\Status;
use Illuminate\Support\Facades\Http;

class RunChecksCommand extends Command
{
    protected $name = 'appkeep:run';
    protected $description = 'Run all Appkeep checks';

    public function handle()
    {
        $appkeep = resolve(Appkeep::class);
        $checks = collect($appkeep->checks)->filter->isDue();

        if ($checks->isEmpty()) {
            $this->info('No checks are due to run.');

            return;
        }

        $results = [];
        $consoleOutput = [];

        foreach ($checks as $check) {
            try {
                $result = $check->run();
            } catch (\Exception $e) {
                $result = Result::crash($e->getMessage());
            } finally {
                $results[] = [
                    'check' => $check->name,
                    'server' => config('appkeep.server'),
                    'result' => [
                        'status' => $result->status,
                        'message' => $result->message,
                        'summary' => $result->summary,
                        'meta' => $result->meta,
                    ],
                ];

                $consoleOutput[] = $this->toConsoleTableRow($check->name, $result);
            }
        }

        try {
            $this->postResultsToAppkeep($results);
        } catch (\Exception $e) {
            $this->warn('Failed to post results to Appkeep.');
            $this->line($e->getMessage());
        }

        $this->table(['Check', 'Outcome', 'Message'], $consoleOutput);
    }

    private function postResultsToAppkeep($results)
    {
        Http::withHeaders(['Authorization' => sprintf('Bearer %s', config('appkeep.key'))])
            ->post(config('appkeep.endpoint'), [
                'checks' => $results,
            ])
            ->throw();
    }

    private function toConsoleTableRow($name, Result $result)
    {
        $status = [
            Status::CRASH => '❌',
            Status::OK => sprintf('✅ %s', $result->summary ?? 'OK'),
            Status::WARN => '⚠️',
            Status::FAIL => '🚨',
        ];

        return [
            $name,
            $status[$result->status],
            $result->message ?? '',
        ];
    }
}
