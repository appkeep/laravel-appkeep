<?php

namespace Appkeep\Laravel\Commands;

use RuntimeException;
use Illuminate\Console\Command;
use Appkeep\Laravel\Diagnostics\Laravel;
use Appkeep\Laravel\Commands\Concerns\InteractsWithComposer;

class BackupCommand extends Command
{
    use InteractsWithComposer;

    protected $signature = 'appkeep:backups';
    protected $description = 'Configure backups for your project.';

    public function handle()
    {
        $this->requireLocalEnvironment();

        $this->ensureSpatieBackupIsInstalled();

        $this->overrideSpatieBackupConfig();

        $this->info('Awesome!');
        $this->line('You have `spatie/laravel-backup` package installed and ready to go.');
        $this->line('Appkeep will perform backups and clean old ones automatically.');
        $this->line('In case something goes wrong, you will be notified according to your project\'s notification settings.');
        $this->line('');
        $this->line('To configure your backup settings, see:');
        $this->line(config_path('appkeep.php'));
        $this->line('');
        $this->warn('IMPORTANT');
        $this->line('Don\'t forget to set `APPKEEP_BACKUPS_ENABLED=true` in your production environment.');
        $this->line('To encrypt your backups, see: https://spatie.be/docs/laravel-backup/v6/advanced-usage/encrypt-backup-archives');
    }

    private function requireLocalEnvironment()
    {
        if (app()->environment('local')) {
            return;
        }

        $this->line('');
        $this->warn('APP_ENV != local');
        $this->line('');
        $this->line('It seems you\'re in a non-local environment.');
        $this->line('Run `php artisan appkeep:backups` locally.');
        $this->line('');
        $this->line('If you are on a local environment, make sure APP_ENV is set to local in your .env file.');
        $this->line('');

        exit;
    }

    private function overrideSpatieBackupConfig()
    {
        if (!file_exists(config_path('backup.php'))) {
            return;
        }

        $this->line('');
        $this->warn('Backup config already exists.');
        $this->line('');
        $this->line('Appkeep will override your backup settings and manage it internally.');
        $this->line('To avoid conflicts / confusion, we will delete your backup config.');


        if (!$this->confirm('Delete ' . config_path('backup.php') . '?')) {
            $this->line('');
            $this->line('Aborting.');
            $this->line('');

            return;
        }

        unlink(config_path('backup.php'));
    }

    private function ensureSpatieBackupIsInstalled()
    {
        if (class_exists('\Spatie\Backup\BackupServiceProvider')) {
            return;
        }

        $version = $this->decideWhichVersionToInstall();
        $package = 'spatie/laravel-backup:' . $version;

        $confirm = $this->confirm('Appkeep will install ' . $package . ' now. Do you want to continue?');

        if (!$confirm) {
            $this->line('Aborting...');
            exit;
        }

        $this->requireComposerPackage($package);

        $this->line('');
        $this->line('');
        $this->line('------------------------------------------------------------');
        $this->line('Run `php artisan appkeep:backups` again.');
    }

    private function decideWhichVersionToInstall()
    {
        // spatie/laravel-backup 7.x and 8.x both require PHP 8.0 or newer.
        if (version_compare(PHP_VERSION, '8.0.0', '<')) {
            return '^6.0';
        } elseif (version_compare(Laravel::version(), '9.0.0', '>=')) {
            return '^8.0';
        } elseif (version_compare(Laravel::version(), '8.0.0', '>=')) {
            return '^7.0';
        }

        throw new RuntimeException('Unable to find a compatible version of spatie/laravel-backup.');
    }
}
