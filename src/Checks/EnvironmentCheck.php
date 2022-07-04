<?php

namespace Appkeep\Laravel\Checks;

use Appkeep\Laravel\Check;
use Appkeep\Laravel\Result;

class EnvironmentCheck extends Check
{
    protected string $expectedEnvironment = 'production';

    public function expectEnvironment($expectedEnvironment)
    {
        $this->expectedEnvironment = $expectedEnvironment;

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

        if ($env != $this->expectedEnvironment) {
            Result::fail("Environment is not set to `{$this->expectedEnvironment}`");
        }

        return Result::ok();
    }
}
