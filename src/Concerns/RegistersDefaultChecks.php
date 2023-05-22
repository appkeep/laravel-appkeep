<?php

namespace Appkeep\Laravel\Concerns;

use Appkeep\Laravel\Facades\Appkeep;
use Appkeep\Laravel\Checks\CacheCheck;
use Appkeep\Laravel\Checks\HorizonCheck;
use Appkeep\Laravel\Checks\StorageCheck;
use Appkeep\Laravel\Checks\DatabaseCheck;
use Appkeep\Laravel\Checks\DiskUsageCheck;
use Appkeep\Laravel\Checks\SystemLoadCheck;
use Appkeep\Laravel\Checks\OptimizationCheck;
use Appkeep\Laravel\Checks\ProductionModeCheck;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;

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

        // Register Horizon check if it's installed
        try {
            app(MasterSupervisorRepository::class);
            Appkeep::check(HorizonCheck::make());
        } catch (\Exception $e) {
            // Horizon not installed
        }
    }
}
