<?php

namespace Appkeep\Laravel\Commands;

use Illuminate\Console\Command;
use Appkeep\Laravel\Facades\Appkeep;
use Appkeep\Laravel\Events\PostDeployEvent;

class PostDeployCommand extends Command
{
    protected $signature = 'appkeep:post-deploy';
    protected $description = 'Notify Appkeep that you deployed a new version.';

    public function handle()
    {
        $event = new PostDeployEvent();
        Appkeep::client()->sendEvent($event)->throw();

        $this->info('Post deploy event sent!');
    }
}
