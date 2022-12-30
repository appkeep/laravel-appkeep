<?php

namespace Tests\Feature\Checks;

use Tests\TestCase;
use Appkeep\Laravel\Enums\Status;
use Appkeep\Laravel\Checks\CacheCheck;

class CacheCheckTest extends TestCase
{
    /**
     * @test
     */
    public function it_succeeds_if_cached_value_is_retrieved()
    {
        // This driver is not set up in test env, so it should fail.
        $this->app['config']->set(
            'cache.default',
            'file'
        );

        $result = CacheCheck::make()->run();
        $this->assertEquals(Status::OK, $result->status);
    }

    /**
     * @test
     */
    public function it_fails_if_it_cannot_retrieve_cached_value()
    {
        // This driver is not set up in test env, so it should fail.
        $this->app['config']->set(
            'cache.default',
            'database'
        );

        $result = CacheCheck::make()->run();
        $this->assertEquals(Status::FAIL, $result->status);
    }

    /**
     * @test
     */
    public function it_shares_tested_driver_in_meta()
    {
        $check = new CacheCheck();
        $check->driver('array');

        $result = $check->run();
        $this->assertEquals('array', $result->meta['driver']);
    }
}
