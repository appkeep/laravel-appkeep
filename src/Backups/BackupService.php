<?php

namespace Appkeep\Laravel\Backups;

use Illuminate\Support\Arr;
use RuntimeException;

class BackupService
{
    public function applyConfig()
    {
        $this->applyFileConfig();

        $this->applyDatabaseConfig();

        $this->applyDestinationConfig();

        $this->applyCleanUpConfig();
    }

    private function applyFileConfig()
    {
        if (!config('appkeep.backups.files.enabled')) {
            // Don't include any files.
            config('backups.backup.source.files.include', []);
            return;
        }

        // Exclude files.
        config(
            'backups.backup.source.files.exclude',
            config('appkeep.backups.files.exclude')
        );
    }

    private function applyDatabaseConfig()
    {
        $dbConfig = config('appkeep.backups.database');

        if (!$dbConfig['enabled']) {
            // Don't include any files.
            config('backups.backup.source.databases', []);
            return;
        }

        // Set which connection to use.
        config(
            'backups.backup.source.databases',
            $dbConfig['connection'],
        );

        // Build dump options.
        // see https://github.com/spatie/db-dumper
        $dumpOptions = [];
        $connection = config('database.connections.' . $dbConfig['connection']);

        // Avoid locking the tables
        if ($connection['driver'] === 'mysql') {
            $dumpOptions['useSingleTransaction'] = true;
        }

        if (count($dbConfig['exclude'])) {
            $dumpOptions['excludeTables'] = $dbConfig['exclude'];
        }

        // Set dump options
        config(
            'database.connections.' . $dbConfig['connection'] . '.dump',
            $dumpOptions
        );

        // Set dump compressor
        config(
            'backups.database_dump_compressor',
            '\Spatie\DbDumper\Compressors\GzipCompressor'
        );
    }

    protected function applyDestinationConfig()
    {
        config(
            'backups.destination.disks',
            config('appkeep.backups.destination')
        );
    }

    protected function applyCleanUpConfig()
    {
        config(
            'backups.cleanup.default_strategy.delete_oldest_backups_when_using_more_megabytes_than',
            config('appkeep.backups.delete_oldest_backups_when_using_more_megabytes_than')
        );
    }
}
