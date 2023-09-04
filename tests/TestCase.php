<?php

namespace Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            'Appkeep\Laravel\AppkeepProvider',
            'Spatie\Backup\BackupServiceProvider',
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Appkeep' => 'Appkeep\Laravel\Facades\Appkeep',
        ];
    }
}
