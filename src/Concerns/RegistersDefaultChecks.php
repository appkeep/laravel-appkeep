<?php

namespace Appkeep\Laravel\Concerns;

use Appkeep\Laravel\Facades\Appkeep;
use Appkeep\Laravel\Checks\DatabaseCheck;
use Appkeep\Laravel\Checks\DiskUsageCheck;

trait RegistersDefaultChecks
{
    protected function registerDefaultChecks()
    {
        Appkeep::checks([
            DatabaseCheck::make(),

            DiskUsageCheck::make()
                ->warnIfUsedPercentageIsAbove(20)
                ->failIfUsedPercentageIsAbove(80),
        ]);
    }
}
