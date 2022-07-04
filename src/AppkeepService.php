<?php

namespace Appkeep\Laravel;

use InvalidArgumentException;

class AppkeepService
{
    public $checks = [];

    public function forgetDefaultChecks()
    {
        $this->checks = [];

        return $this;
    }

    public function checks(array $checks = [])
    {
        if (! app()->runningInConsole()) {
            return;
        }

        foreach ($checks as $check) {
            $this->rejectIfDoesNotExtendBaseClass($check);

            $this->checks[$check->name] = $check;
        }

        return collect($this->checks);
    }

    protected function rejectIfDoesNotExtendBaseClass($check)
    {
        if (! ($check instanceof Check)) {
            throw new InvalidArgumentException(
                sprintf('%s is not an instance of %s', get_class($check), Check::class)
            );
        }
    }
}
