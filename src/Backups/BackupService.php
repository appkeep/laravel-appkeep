<?php

namespace Appkeep\Laravel\Backups;

use Appkeep\Laravel\Backups\Concerns\AppliesConfig;

class BackupService
{
    use AppliesConfig;

    public function applyConfig()
    {
        $this->applyFileConfig();

        $this->applyDatabaseConfig();

        $this->applyDestinationConfig();

        $this->applyCleanUpConfig();

        $this->disableNotifications();
    }

    public function scheduleCleanUp()
    {
        $schedule = app()->make(Schedule::class);

        $schedule->command('backup:clean')
            ->everyMinute()
            ->runInBackground();
    }
}
