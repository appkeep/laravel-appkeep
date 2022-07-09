<?php

namespace Appkeep\Laravel\Commands;

use Appkeep\Laravel\Result;
use Illuminate\Console\Command;
use Appkeep\Laravel\Enums\Status;
use Appkeep\Laravel\Diagnostics\Git;
use Appkeep\Laravel\Facades\Appkeep;
use Illuminate\Support\Facades\Http;
use Appkeep\Laravel\Diagnostics\Server;
use Appkeep\Laravel\Diagnostics\Laravel;

class RunCommand extends Command
{
    protected $signature = 'appkeep:run {--all}';
    protected $description = 'Run all Appkeep checks';

    public function handle()
    {
        $checks = Appkeep::checks();

        // Unless it's in force mode, only run the checks that are due.
        if (! $this->option('all')) {
            $checks = $checks->filter->isDue();
        }

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
        Http::withHeaders([
            'Authorization' => sprintf('Bearer %s', config('appkeep.key')),
            'Accept' => 'application/json',
        ])
            ->post(config('appkeep.endpoint'), [
                'server' => [
                    'uid' => Server::uniqueIdentifier(),
                    'name' => Server::name(),
                    'os' => Server::os(),
                ],
                'laravel' => [
                    'version' => Laravel::version(),
                ],
                'git' => ($hash = Git::shortCommitHash()) ? [
                    'commit' => $hash,
                    'url' => Git::remoteUrl(),
                ] : null,
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
