<?php

namespace Appkeep\Laravel\Commands;

use RuntimeException;
use Appkeep\Laravel\Composer;
use Illuminate\Console\Command;
use Appkeep\Laravel\Diagnostics\Laravel;

class BackupCommand extends Command
{
    protected $signature = 'appkeep:backups';
    protected $description = 'Configure backups for your project.';

    public function handle()
    {
        if (! class_exists('\Spatie\Backup\BackupServiceProvider')) {
            // Don't install composer packages on a non-local environment.
            if (! app()->environment('local')) {
                $this->line('');
                $this->warn('APP_ENV != local');
                $this->line('');
                $this->line('To run backups, you need spatie/laravel-backup installed.');
                $this->line('It seems you\'re in a non-local environment.');
                $this->line('Run `php artisan appkeep:backups` locally.');
                $this->line('');
                $this->line('If you are on a local environment, make sure APP_ENV is set to local in your .env file.');
                $this->line('');

                return;
            }


            $this->installSpatieBackup();
        }
    }

    private function installSpatieBackup()
    {
        $version = $this->decideWhichVersionToInstall();
        $package = 'spatie/laravel-backup:' . $version;

        $confirm = $this->confirm('Appkeep will install ' . $package . ' now. Do you want to continue?');

        if (! $confirm) {
            $this->line('Aborting...');
            exit;
        }

        $composer = new Composer(app()['files'], base_path());
        $composer->require(['require', $package], fn ($type, $buffer) => $this->line($buffer));

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
        }

        if (version_compare(Laravel::version(), '9.0.0', '>=')) {
            return '^8.0';
        }

        if (version_compare(Laravel::version(), '8.0.0', '>=')) {
            return '^7.0';
        }

        throw new RuntimeException('Unable to find a compatible version of spatie/laravel-backup.');
    }
}
