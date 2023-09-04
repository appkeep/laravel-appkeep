<?php

namespace Appkeep\Laravel\Backups;

use Illuminate\Support\Str;
use Illuminate\Console\Scheduling\Schedule;
use Appkeep\Laravel\Backups\Concerns\AppliesConfig;

class BackupService
{
    use AppliesConfig;

    public function applyConfig()
    {
        config()->set(
            'backup.backup.name',
            Str::snake(config('app.name') . ' backups')
        );

        $this->applyFileConfig();

        $this->applyDatabaseConfig();

        $this->applyDestinationConfig();

        $this->applyCleanUpConfig();

        $this->disableNotifications();
    }

    public function scheduleBackups()
    {
        $schedule = app()->make(Schedule::class);

        $schedule->command('backup:clean')
            ->dailyAt(config('appkeep.backups.run_at'))
            ->runInBackground();

        $schedule->command('backup:run')
            ->dailyAt(config('appkeep.backups.run_at'))
            ->runInBackground();
    }
}
