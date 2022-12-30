<?php

namespace Appkeep\Laravel\Concerns;

use Appkeep\Laravel\Facades\Appkeep;
use Appkeep\Laravel\Checks\CacheCheck;
use Appkeep\Laravel\Checks\StorageCheck;
use Appkeep\Laravel\Checks\DatabaseCheck;
use Appkeep\Laravel\Checks\DiskUsageCheck;
use Appkeep\Laravel\Checks\SystemLoadCheck;
use Appkeep\Laravel\Checks\OptimizationCheck;
use Appkeep\Laravel\Checks\ProductionModeCheck;

trait RegistersDefaultChecks
{
    protected function registerDefaultChecks()
    {
        Appkeep::checks([

            ProductionModeCheck::make(),

            StorageCheck::make(),

            DatabaseCheck::make(),

            CacheCheck::make(),

            DiskUsageCheck::make(),

            OptimizationCheck::make(),

            SystemLoadCheck::make(),
        ]);
    }
}
