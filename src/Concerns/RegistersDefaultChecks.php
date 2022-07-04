<?php

namespace Appkeep\Laravel\Concerns;

use Appkeep\Laravel\Facades\Appkeep;
use Appkeep\Laravel\Checks\DatabaseCheck;
use Appkeep\Laravel\Checks\DiskUsageCheck;
use Appkeep\Laravel\Checks\EnvironmentCheck;

trait RegistersDefaultChecks
{
    protected function registerDefaultChecks()
    {
        Appkeep::checks([

            EnvironmentCheck::make(),

            DatabaseCheck::make(),

            DiskUsageCheck::make(),
        ]);
    }
}
