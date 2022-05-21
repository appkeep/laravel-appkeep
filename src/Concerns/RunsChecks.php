<?php

namespace Appkeep\Laravel\Concerns;

use Appkeep\Laravel\Check;
use Appkeep\Laravel\Appkeep;
use Appkeep\Laravel\Checks\DatabaseCheck;
use Appkeep\Laravel\Checks\DiskUsageCheck;

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
