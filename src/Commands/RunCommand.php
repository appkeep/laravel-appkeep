<?php

namespace Appkeep\Laravel\Commands;

use Illuminate\Console\Command;
use Appkeep\Laravel\Enums\Status;
use Appkeep\Laravel\Facades\Appkeep;
use Appkeep\Laravel\Health\Actions\RunChecks;
use Appkeep\Laravel\Health\Actions\SendHeartbeat;

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

        $results = (new RunChecks)($checks);

        $this->table(
            ['Check', 'Outcome', 'Message'],
            $results->map(fn ($result) => $this->toConsoleTableRow($result))
        );

        rescue(
            fn () => (new SendHeartbeat)($results),
            function (\Exception $e) {
                $this->warn('Failed to post results to Appkeep.');
                $this->line($e->getMessage());
            }
        );
    }

    private function toConsoleTableRow(array $result)
    {
        $status = [
            Status::CRASH => '❌',
            Status::OK => sprintf(
                '✅ %s',
                $result['result']['summary'] ?? 'OK'
            ),
            Status::WARN => '⚠️',
            Status::FAIL => '🚨',
        ];

        return [
            $result['check'],
            $status[$result['result']['status']],
            $result['result']['message'] ?? '',
        ];
    }
}
