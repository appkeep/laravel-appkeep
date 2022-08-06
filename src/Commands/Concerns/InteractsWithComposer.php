<?php

namespace Appkeep\Laravel\Commands\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Composer;

trait InteractsWithComposer
{
    protected function requireComposerPackage($package)
    {
        $composer = new class(app()['files'], base_path()) extends Composer {
            public function require($parameters, $callback = null)
            {
                return $this->getProcess(
                    array_merge(
                        $this->findComposer(),
                        ['require'],
                        Arr::wrap($parameters)
                    )
                )->mustRun($callback);
            }
        };

        return $composer->require(
            $package,
            fn ($type, $buffer) => $this->output->write($buffer)
        );
    }
}
