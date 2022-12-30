<?php

namespace Tests\Feature\Checks;

use Tests\TestCase;
use Appkeep\Laravel\Enums\Status;
use Appkeep\Laravel\Checks\ProductionModeCheck;

class ProductionModeCheckTest extends TestCase
{
    /**
     * @test
     */
    public function it_fails_if_debug_mode_is_turned_on()
    {
        $this->app['config']->set('app.debug', true);

        $result = ProductionModeCheck::make()->run();

        $this->assertEquals(Status::FAIL, $result->status);
        $this->assertStringContainsString('Always turn off debug mode in production', $result->message);
    }

    /**
     * @test
     */
    public function it_fails_if_environment_is_not_production()
    {
        $this->app['config']->set('app.debug', false);

        $result = ProductionModeCheck::make()->run();
        $this->assertEquals(Status::FAIL, $result->status);
    }

    /**
     * @test
     */
    public function it_allows_checking_a_different_environment()
    {
        $this->app['config']->set('app.debug', false);

        $result = ProductionModeCheck::make()
            ->environment('testing')
            ->run();

        $this->assertEquals(Status::OK, $result->status);
    }
}
