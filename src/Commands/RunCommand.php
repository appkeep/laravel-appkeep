<?php

namespace Appkeep\Laravel\Commands;

use Appkeep\Laravel\Result;
use Illuminate\Console\Command;
use Appkeep\Laravel\Enums\Status;
use Appkeep\Laravel\Facades\Appkeep;
use Appkeep\Laravel\Events\ChecksEvent;

class RunCommand extends Command
{
    protected $signature = 'appkeep:run {--all}';
    protected $description = 'Run all Appkeep checks';

    public function handle()
    {
        $checks = Appkeep::checks();

        // Unless it's in force mode, only run the checks that are due.
        if (!$this->option('all')) {
            $checks = $checks->filter->isDue();
        }

        if ($checks->isEmpty()) {
            $this->info('No checks are due to run.');

            return;
        }

        $event = new ChecksEvent();

        $consoleOutput = [];

        foreach ($checks as $check) {
            try {
                $result = $check->run();
            } catch (\Exception $e) {
                $result = Result::crash($e->getMessage());
            } finally {
                $event->addResult($check, $result);
                $consoleOutput[] = $this->toConsoleTableRow($check->name, $result);
            }
        }

        try {
            $this->postResultsToAppkeep($event);
        } catch (\Exception $e) {
            $this->warn('Failed to post results to Appkeep.');
            $this->line($e->getMessage());
        }

        $this->table(['Check', 'Outcome', 'Message'], $consoleOutput);
    }

    private function postResultsToAppkeep(ChecksEvent $event)
    {
        Appkeep::client()->sendEvent($event)->throw();
    }

    private function toConsoleTableRow($name, Result $result)
    {
        $status = [
            Status::CRASH => '❌',
            Status::OK => sprintf('✅ %s', $result->summary ?? 'OK'),
            Status::WARN => sprintf('⚠️  %s', $result->summary ?? ''),
            Status::FAIL => sprintf('🚨  %s', $result->summary ?? ''),
        ];

        return [
            $name,
            $status[$result->status],
            $result->message ?? '',
        ];
    }
}
