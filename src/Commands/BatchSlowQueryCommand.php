<?php

namespace Appkeep\Laravel\Commands;

use Illuminate\Console\Command;
use Appkeep\Laravel\Facades\Appkeep;
use Appkeep\Laravel\Events\BatchSlowQueryEvent;
use Appkeep\Laravel\Listeners\SlowQueryHandler;
use Exception;

class BatchSlowQueryCommand extends Command
{
    protected $signature = 'appkeep:batch-slow-queries
                            {filename : Name of the file where slow queries are kept}
                            {--clear : Truncates the slow query file}';
    protected $description = 'Batch slow query events together';

    public function handle()
    {
        $filename = $this->argument('filename');
        $clear = $this->option('clear');
        $slowQueries = SlowQueryHandler::collectSlowQueriesFromFile($filename, $clear);
        if (count($slowQueries) > 0) {
            $event = new BatchSlowQueryEvent($slowQueries);
        } else {
            throw new Exception("There aren't any slow queries detected.");
        }

        if (app()->runningInConsole() || app()->runningUnitTests()) {
            $this->table(['SQL', 'Connection', 'Time', 'Context'], $this->createOutputTable($slowQueries));
        } else {
            // TODO Test this
            $this->postResultsToAppkeep($event);
        }
    }

    private function postResultsToAppkeep(BatchSlowQueryEvent $event)
    {
        Appkeep::client()->sendEvent($event)->throw();
    }

    private function createOutputTable($slowQueries)
    {
        $table = [];
        foreach ($slowQueries as $query) {
            $table[] = [
                $query['event']['sql'],
                $query['event']['connectionName'],
                $query['event']['time'],
                json_encode($query['context']),
            ];
        }

        return $table;
    }
}
