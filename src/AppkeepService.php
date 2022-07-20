<?php

namespace Appkeep\Laravel;

use InvalidArgumentException;

class AppkeepService
{
    public $checks = [];

    public function version()
    {
        return '0.3.0';
    }

    public function forgetDefaultChecks()
    {
        $this->checks = [];

        return $this;
    }

    public function checks(array $checks = [], $replace = false)
    {
        if (!app()->runningInConsole()) {
            return;
        }

        foreach ($checks as $check) {
            $this->rejectIfDoesNotExtendBaseClass($check);

            if (!$replace) {
                $this->rejectIfDuplicate($check);
            }

            $this->checks[$check->name] = $check;
        }

        return collect($this->checks);
    }


    public function replaceChecks(array $checks = [])
    {
        return $this->checks($checks, true);
    }

    protected function rejectIfDoesNotExtendBaseClass($check)
    {
        if (!($check instanceof Check)) {
            throw new InvalidArgumentException(
                sprintf('%s is not an instance of %s', get_class($check), Check::class)
            );
        }
    }

    protected function rejectIfDuplicate($check)
    {
        if (isset($this->checks[$check->name])) {
            throw new InvalidArgumentException(
                sprintf(
                    'A check with the name %s already registered. Set a custom name if you want to register the same check multiple times.',
                    $check->name
                )
            );
        }
    }
}
