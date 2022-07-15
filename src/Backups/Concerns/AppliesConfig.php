<?php

namespace Appkeep\Laravel\Backups\Concerns;

use Illuminate\Support\Arr;

trait AppliesConfig
{
    private function applyFileConfig()
    {
        if (! config('appkeep.backups.files.enabled')) {
            // Don't include any files.
            config()->set('backup.backup.source.files.include', []);

            return;
        }

        // Exclude files.
        config()->set(
            'backup.backup.source.files.exclude',
            config('appkeep.backups.files.exclude')
        );
    }

    private function applyDatabaseConfig()
    {
        $dbConfig = config('appkeep.backups.database');

        if (! $dbConfig['enabled']) {
            // Don't include any files.
            config()->set('backup.backup.source.databases', []);

            return;
        }

        // Set which connection to use.
        config()->set(
            'backup.backup.source.databases',
            Arr::wrap($dbConfig['connection']),
        );

        // Build dump options.
        // see https://github.com/spatie/db-dumper
        $dumpOptions = [];
        $connection = config('database.connections.' . $dbConfig['connection']);

        if (! $connection) {
            throw new \RuntimeException('Database connection not found (' . $dbConfig['connection'] . ').');
        }

        // Avoid locking the tables
        if ($connection['driver'] === 'mysql') {
            $dumpOptions['useSingleTransaction'] = true;
        }

        if (count($dbConfig['exclude'])) {
            $dumpOptions['excludeTables'] = $dbConfig['exclude'];
        }

        // Set dump options
        config()->set(
            'database.connections.' . $dbConfig['connection'] . '.dump',
            $dumpOptions
        );

        // Set dump compressor
        config()->set(
            'backup.database_dump_compressor',
            'Spatie\DbDumper\Compressors\GzipCompressor'
        );
    }

    protected function applyDestinationConfig()
    {
        config()->set(
            'backup.destination.disks',
            config('appkeep.backups.destination')
        );
    }

    protected function applyCleanUpConfig()
    {
        config()->set(
            'backup.cleanup.default_strategy.delete_oldest_backups_when_using_more_megabytes_than',
            config('appkeep.backups.delete_oldest_backups_when_using_more_megabytes_than')
        );
    }

    protected function disableNotifications()
    {
        config()->set(
            'backup.notifications.notifications',
            []
        );
    }
}
