<?php

namespace Tests\Feature;

use Tests\TestCase;
use Appkeep\Laravel\Backups\BackupService;

class BackupServiceTest extends TestCase
{
    /**
     * @test
     */
    public function it_configures_the_backup_package()
    {
        $this->app['config']->set('appkeep.backups', [
            'destination' => [
                'custom_disk',
            ],
            'files' => [
                'enabled' => true,
                'exclude' => [
                    'exclude_file',
                ],
            ],
            'database' => [
                'enabled' => true,
                'connection' => 'mysql',
                'exclude' => [
                    'exclude_table',
                ],
            ],
            'delete_oldest_backups_when_using_more_megabytes_than' => 300,
        ]);

        app(BackupService::class)->applyConfig();

        $this->assertNotEmpty(
            $this->app['config']->get('backup.backup.source.files.include')
        );

        $this->assertEquals(
            ['exclude_file'],
            $this->app['config']->get('backup.backup.source.files.exclude')
        );

        $this->assertEquals(
            ['mysql'],
            $this->app['config']->get('backup.backup.source.databases')
        );

        $this->assertEquals(
            [
                'useSingleTransaction' => true,
                'excludeTables' => ['exclude_table'],
            ],
            $this->app['config']->get('database.connections.mysql.dump')
        );

        $this->assertEquals(
            \Spatie\DbDumper\Compressors\GzipCompressor::class,
            $this->app['config']->get('backup.database_dump_compressor'),
        );

        $this->assertEquals(
            ['custom_disk'],
            $this->app['config']->get('backup.destination.disks'),
        );

        $this->assertEquals(
            300,
            $this->app['config']->get('backup.cleanup.default_strategy.delete_oldest_backups_when_using_more_megabytes_than'),
        );

        $this->assertEmpty($this->app['config']->get('backup.notifications.notifications'));
    }
}
