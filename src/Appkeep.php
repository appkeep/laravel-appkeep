<?php

namespace Appkeep\Laravel;

use InvalidArgumentException;

class Appkeep
{
    public $checks = [];

    public function checks(array $checks)
    {
        foreach ($checks as $check) {
            $this->rejectIfDoesNotExtendBaseClass($check);
            $this->rejectIfDuplicate($check);

            $this->checks[$check->name] = $check;
        }

        return $this;
    }

    protected function rejectIfDoesNotExtendBaseClass($check)
    {
        if (! ($check instanceof Check)) {
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
