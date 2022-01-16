<?php

namespace Appkeep\Eye\Commands;

use Appkeep\Eye\Result;
use Appkeep\Eye\Appkeep;
use Appkeep\Eye\Enums\Status;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RunChecksCommand extends Command
{
    protected $name = 'eye:check';
    protected $description = 'Run all Appkeep checks';

    public function handle()
    {
        $appkeep = resolve(Appkeep::class);
        $checks = collect($appkeep->checks)->filter->isDue();

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
                        'value' => $result->value,
                        'message' => $result->message,
                        'summary' => $result->summary,
                    ],
                ];

                $consoleOutput[] = $this->toConsoleTableRow($check->name, $result);
            }
        }

        Http::withHeaders([
            'Authorization' => sprintf('Bearer %s', config('appkeep.key')),
        ])
            ->post('https://appkeep.dev/api/v1/intake', [
                'checks' => $results,
            ]);

        $this->table(['Check', 'Outcome', 'Message'], $consoleOutput);
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
            $status[$result->value],
            $result->message ?? '',
        ];
    }
}
