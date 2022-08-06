<?php

namespace Appkeep\Laravel\Concerns;

use Appkeep\Laravel\Facades\Appkeep;
use Appkeep\Laravel\Health\Checks\CacheCheck;
use Appkeep\Laravel\Health\Checks\StorageCheck;
use Appkeep\Laravel\Health\Checks\DatabaseCheck;
use Appkeep\Laravel\Health\Checks\DiskUsageCheck;
use Appkeep\Laravel\Health\Checks\EnvironmentCheck;

trait RegistersDefaultChecks
{
    protected function registerDefaultChecks()
    {
        Appkeep::checks([

            EnvironmentCheck::make(),

            StorageCheck::make(),

            DatabaseCheck::make(),

            CacheCheck::make(),

            DiskUsageCheck::make(),
        ]);
    }
}
