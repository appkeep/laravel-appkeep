<?php

namespace Appkeep\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InitCommand extends Command
{
    protected $name = 'appkeep:init';
    protected $description = 'Publishes config file and service provider.';

    public function handle()
    {
        Artisan::call('vendor:publish', ['--provider' => 'Appkeep\Laravel\AppkeepProvider']);
    }
}
