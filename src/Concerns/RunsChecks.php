<?php

namespace Appkeep\Eye\Concerns;

use Appkeep\Eye\Check;
use Appkeep\Eye\Appkeep;
use Appkeep\Eye\Checks\DatabaseCheck;
use Appkeep\Eye\Checks\DiskUsageCheck;

trait RunsChecks
{
    protected $checks = [];

    protected function registerCheck(Check $check)
    {
        $appkeep = $this->app->make(Appkeep::class);
        $appkeep->checks([$check]);
    }

    protected function registerDefaultChecks()
    {
        $appkeep = $this->app->make(Appkeep::class);

        $appkeep->checks([
            DatabaseCheck::make(),

            DiskUsageCheck::make()
                ->warnIfUsedPercentageIsAbove(60)
                ->failIfUsedPercentageIsAbove(80),
        ]);
    }
}
