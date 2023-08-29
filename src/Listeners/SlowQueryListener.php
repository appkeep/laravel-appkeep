<?php

namespace Appkeep\Laravel\Listeners;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SlowQueryListener implements ShouldQueue
{
    use Batchable;
    public function __construct()
    {
    }

    public function handle($event): void
    {
    }
}
