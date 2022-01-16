<?php

namespace Appkeep\Eye\Commands;

use Appkeep\Eye\Appkeep;
use Illuminate\Console\Command;

class ListChecksCommand extends Command
{
    protected $name = 'eye:list';
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
