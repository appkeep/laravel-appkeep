<?php

namespace Appkeep\Laravel\Health\Checks;

use Appkeep\Laravel\Health\Check;
use Appkeep\Laravel\Health\Result;

class EnvironmentCheck extends Check
{
    protected string $expectedEnvironment = 'production';

    public function expectEnvironment($expectedEnvironment)
    {
        $this->expectedEnvironment = $expectedEnvironment;

        return $this;
    }

    /**
     * @var \Appkeep\Laravel\Health\Result
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
