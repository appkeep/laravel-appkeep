<?php

namespace Appkeep\Laravel\Checks;

use Appkeep\Laravel\Check;
use Appkeep\Laravel\Result;

class EnvironmentCheck extends Check
{
    protected $environment = 'production';

    /**
     * Ensure environment value is set to...
     */
    public function environment($environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * @var Result
     */
    public function run()
    {
        if (config('app.debug')) {
            Result::fail('APP_DEBUG is true. Always turn off debug mode in production!');
        }

        $env = app()->environment();

        if ($env !== $this->environment) {
            Result::fail("Environment is not set to `{$this->environment}`");
        }

        return Result::ok();
    }
}
