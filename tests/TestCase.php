<?php

namespace Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            'Appkeep\Laravel\AppkeepServiceProvider',
        ];
    }
}
