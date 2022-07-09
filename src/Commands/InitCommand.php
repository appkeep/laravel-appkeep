<?php

namespace Appkeep\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InitCommand extends Command
{
    protected $signature = 'appkeep:init {--force}';
    protected $description = 'Publishes config file and service provider.';

    public function handle()
    {
        if ($this->option('force')) {
            unlink(config_path('appkeep.php'));
        }

        if (file_exists(config_path('appkeep.php'))) {
            $this->info('Config file is already published.');

            return;
        }

        $this->comment('Publishing Appkeep config file...');
        Artisan::call('vendor:publish', ['--provider' => 'Appkeep\Laravel\AppkeepProvider']);

        $this->info('Config file is published.');
    }
}
