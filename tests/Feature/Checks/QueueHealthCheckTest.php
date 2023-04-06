<?php

namespace Tests\Feature\Checks;

use Tests\TestCase;
use Appkeep\Laravel\Enums\Status;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Appkeep\Laravel\Checks\QueueHealthCheck;
use Orchestra\Testbench\Concerns\HandlesRoutes;

class QueueHealthCheckTest extends TestCase
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
    public function succeeds_on_first_run()
    {
        $result = QueueHealthCheck::make()->run();
        $this->assertEquals(Status::OK, $result->status);

        $this->assertTrue(
            Cache::has('appkeep.queue-health-check.default')
        );
    }

    /**
     * @test
     */
    public function succeeds_when_queue_is_fast()
    {
        $result = QueueHealthCheck::make()->run();
        $this->assertEquals(Status::OK, $result->status);

        Cache::put('appkeep.queue-health-check.default', [
            'dispatched_at' => now()->subSecond(40),
            'processed_at' => now(),
        ], now()->addMinutes(10));

        $result = QueueHealthCheck::make()->run();
        $this->assertEquals(Status::OK, $result->status);
    }

    /**
     * @test
     */
    public function warns_when_queue_is_getting_slower()
    {
        $result = QueueHealthCheck::make()->run();
        $this->assertEquals(Status::OK, $result->status);

        Cache::put('appkeep.queue-health-check.default', [
            'dispatched_at' => now()->subSecond(45),
            'processed_at' => now(),
        ], now()->addMinutes(10));

        $result = QueueHealthCheck::make()->run();
        $this->assertEquals(Status::WARN, $result->status);
    }

    /**
     * @test
     */
    public function fails_when_queue_is_too_slow()
    {
        $result = QueueHealthCheck::make()->run();
        $this->assertEquals(Status::OK, $result->status);

        Cache::put('appkeep.queue-health-check.default', [
            'dispatched_at' => now()->subSecond(120),
            'processed_at' => now(),
        ], now()->addMinutes(10));

        $result = QueueHealthCheck::make()->run();
        $this->assertEquals(Status::FAIL, $result->status);
    }

    /**
     * @test
     */
    public function respects_dispatch_frequency()
    {
        Queue::fake();

        Cache::put('appkeep.queue-health-check.default', [
            'dispatched_at' => now()->subMinute(2),
            'processed_at' => now(),
        ], now()->addMinutes(10));

        QueueHealthCheck::make()->run();
        Queue::assertNothingPushed();

        QueueHealthCheck::make()->dispatchFrequency(1)->run();
        $this->assertEquals(1, Queue::size('default'));
    }

    /**
     * @test
     */
    public function respecst_queue_name()
    {
        Queue::fake();

        QueueHealthCheck::make('notifications')->run();
        $this->assertEquals(0, Queue::size('default'));
        $this->assertEquals(1, Queue::size('notifications'));
    }
}
