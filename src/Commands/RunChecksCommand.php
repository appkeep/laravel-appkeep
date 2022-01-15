<?php

namespace Appkeep\Eye\Commands;

use Appkeep\Eye\Result;
use Appkeep\Eye\Appkeep;
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

        $output = [];

        foreach ($checks as $check) {
            try {
                $result = $check->run();
            } catch (\Exception $e) {
                $result = Result::crash($e->getMessage());
            } finally {
                $output[] = [
                    'check' => $check->name,
                    'server' => config('appkeep.server'),
                    'result' => [
                        'value' => $result->value,
                        'message' => $result->message,
                        'summary' => $result->summary,
                    ],
                ];
            }
        }

        Http::withHeaders([
            'Authorization' => sprintf('Bearer %s', config('appkeep.key')),
        ])
            ->post('https://appkeep.dev/api/v1/intake', [
                'checks' => $output,
            ]);
    }
}
