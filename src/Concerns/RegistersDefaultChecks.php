<?php

namespace Appkeep\Laravel\Concerns;

use Appkeep\Laravel\Facades\Appkeep;
use Appkeep\Laravel\Checks\CacheCheck;
use Appkeep\Laravel\Checks\StorageCheck;
use Appkeep\Laravel\Checks\DatabaseCheck;
use Appkeep\Laravel\Checks\DiskUsageCheck;
use Appkeep\Laravel\Checks\EnvironmentCheck;
use Appkeep\Laravel\Checks\OptimizationCheck;

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

            OptimizationCheck::make(),
        ]);
    }
}
