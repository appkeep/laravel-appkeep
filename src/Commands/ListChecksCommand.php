<?php

namespace Appkeep\Laravel\Commands;

use Appkeep\Laravel\Appkeep;
use Illuminate\Console\Command;

class ListChecksCommand extends Command
{
    protected $name = 'appkeep:checks';
    protected $description = 'List all Appkeep checks';

    public function handle()
    {
        $appkeep = resolve(Appkeep::class);
        $checks = collect($appkeep->checks);

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
