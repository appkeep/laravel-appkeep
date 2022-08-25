<?php

namespace Tests\Feature\Checks;

use Tests\TestCase;
use Appkeep\Laravel\Enums\Status;
use Appkeep\Laravel\Checks\OptimizationCheck;
use Orchestra\Testbench\Concerns\HandlesRoutes;

class OptimizationCheckTest extends TestCase
{
    use HandlesRoutes;

    public function tearDown(): void
    {
        $this->artisan('optimize:clear');
        $this->artisan('event:clear');
        parent::tearDown();
    }

    /**
     * @test
     */
    public function succeeds_if_optimizations_are_done()
    {
        // This creates the route cache...
        $this->defineCacheRoutes(
            "<?php Route::resource('lessons', 'LessonsController');"
        );

        $this->artisan('optimize');
        $this->artisan('event:cache');

        $result = OptimizationCheck::make()->run();
        $this->assertEquals(Status::OK, $result->status);
    }

    /**
     * @test
     */
    public function warns_if_routes_are_not_cached()
    {
        $this->artisan('config:cache');
        $this->artisan('event:cache');

        $result = OptimizationCheck::make()->run();
        $this->assertEquals(Status::WARN, $result->status);
        $this->assertStringContainsString('Routes', $result->message);
    }

    /**
     * @test
     */
    public function warns_if_config_is_not_cached()
    {
        // This creates the route cache...
        $this->defineCacheRoutes(
            "<?php Route::resource('lessons', 'LessonsController');"
        );

        $this->artisan('event:cache');

        $result = OptimizationCheck::make()->run();
        $this->assertEquals(Status::WARN, $result->status);
        $this->assertStringContainsString('Config', $result->message);
    }

    /**
     * @test
     */
    public function warns_if_event_listeners_are_not_caached()
    {
        // This creates the route cache...
        $this->defineCacheRoutes(
            "<?php Route::resource('lessons', 'LessonsController');"
        );

        $this->artisan('config:cache');

        $result = OptimizationCheck::make()->run();
        $this->assertEquals(Status::WARN, $result->status);
        $this->assertStringContainsString('Event', $result->message);
    }
}
