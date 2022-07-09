<?php

namespace Appkeep\Laravel\Commands;

use Illuminate\Console\Command;
use Appkeep\Laravel\Facades\Appkeep;

class ListCommand extends Command
{
    protected $signature = 'appkeep:list';
    protected $description = 'List all Appkeep checks';

    public function handle()
    {
        $checks = Appkeep::checks();

        if ($checks->isEmpty()) {
            $this->info('No checks are set up.');

            return;
        }

        $this->table(['Check', 'Expression'], $checks->map(function ($check) {
            return [
                $check->name,
                $check->expression,
            ];
        }));
    }
}
