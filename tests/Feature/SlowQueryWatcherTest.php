<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Appkeep\Laravel\AppkeepService;
use Appkeep\Laravel\EventCollector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class SlowQueryTest extends TestCase
{
    protected function setUp(): void
    {
        // This way, any query will be considered slow.
        AppkeepService::$slowQueryThreshold = 0;

        parent::setUp();
    }

    public function tearDown(): void
    {
        Cache::flush();

        parent::tearDown();
    }

    /**
     * @test
     */
    public function it_listens_to_slow_queries_from_cli()
    {
        // Using sqlite, run a slow query without needing any actual tables. Use sleep
        // to simulate a slow query.
        DB::statement('SELECT RANDOM() AS random_number;');

        // Normally, this would run when the test finishes. Go ahead and run it early.
        $this->app->make(EventCollector::class)->persist();

        $events = $this->app->make(EventCollector::class)->pull();

        $this->assertCount(1, $events);

        $event = array_values($events)[0];

        $this->assertEquals('slow-query', Arr::get($event, 'name'));
        $this->assertEquals('sqlite', Arr::get($event, 'context.database.driver'));

        $this->assertEquals('SELECT RANDOM() AS random_number;', Arr::get($event, 'query.sql'));

        // TODO: Assert we can see the
    }

    /**
     * @test
     */
    public function it_listens_to_slow_queries_from_http()
    {
        Route::get('/slow-query', function () {
            DB::statement('SELECT RANDOM() AS random_number;');

            return 'ok';
        });

        $this->get('/slow-query')
            ->assertStatus(200)
            ->assertSee('ok');

        $events = $this->app->make(EventCollector::class)->pull();
        $this->assertCount(1, $events);

        $event = array_values($events)[0];

        $this->assertEquals('slow-query', Arr::get($event, 'name'));
        $this->assertEquals('SELECT RANDOM() AS random_number;', Arr::get($event, 'query.sql'));

        $this->assertEquals('/slow-query', Arr::get($event, 'context.request.path'));
    }

    /**
     * @test
     */
    public function it_deduplicates_the_same_query()
    {
        Route::get('/slow-query/{number}', function ($number) {
            DB::statement("SELECT $number AS random_number_$number;");

            return 'ok';
        });

        $this->get('/slow-query/1')
            ->assertStatus(200)
            ->assertSee('ok');

        $this->get('/slow-query/1')
            ->assertStatus(200)
            ->assertSee('ok');

        $this->get('/slow-query/2')
            ->assertStatus(200)
            ->assertSee('ok');


        $events = $this->app->make(EventCollector::class)->pull();
        $this->assertCount(2, $events);
    }
}
