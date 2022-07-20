<?php

namespace Appkeep\Laravel;

use Illuminate\Support\Composer as BaseComposer;

class Composer extends BaseComposer
{
    public function require(array $command, $callback = null)
    {
        return $this->getProcess(
            array_merge($this->findComposer(), $command)
        )->mustRun($callback);
    }
}
