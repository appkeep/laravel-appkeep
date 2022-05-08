<?php

namespace Appkeep\Eye\Concerns;

use Appkeep\Eye\Appkeep;

trait RunsChecks
{
    protected function checks($callable)
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $appkeep = $this->app->make(Appkeep::class);

        $appkeep->checks(
            call_user_func($callable)
        );
    }
}
