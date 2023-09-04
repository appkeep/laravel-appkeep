<?php

namespace Tests\Feature;

use Appkeep\Laravel\AppkeepService;
use Appkeep\Laravel\EventCollector;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tests\TestCheck;
use Appkeep\Laravel\Result;
use Appkeep\Laravel\Facades\Appkeep;
use Illuminate\Support\Facades\Http;

class RunCommandTest extends TestCase
{
    /**
     * @test
     */
    public function the_check_command_is_registered()
    {
        Appkeep::forgetDefaultChecks();

        $this->artisan('appkeep:run')->expectsOutput('No checks are due to run.');
    }

    /**
     * @test
     */
    public function it_returns_a_table_output()
    {
        Http::fake();

        Appkeep::forgetDefaultChecks()->checks([
            TestCheck::make('bar')->result(
                Result::ok()->summary('50%')
            ),
            TestCheck::make('baz')->result(
                Result::warn('This is a warning')
            ),
        ]);

        $this->artisan('appkeep:run')->expectsTable(
            ['Check', 'Outcome', 'Message'],
            [
                ['bar', '✅ 50%', ''],
                ['baz', '⚠️', 'This is a warning'],
            ]
        );
    }

    /**
     * @test
     */
    public function it_runs_only_due_checks()
    {
        Http::fake();

        Carbon::setTestNow(Carbon::now()->setMinute(3)->setSecond(0));

        // Prevent hitting Appkeep server.
        $this->artisan('appkeep:run')->assertExitCode(0);

        // Assert that only one check ran.
        Http::assertSent(function ($request) {
            $data = $request->data();

            return count($data['checks']) === 1
                && $data['checks'][0]['check'] === 'test-check-1';
        });

        Carbon::setTestNow(Carbon::now()->setMinute(15)->setSecond(0));

        $this->artisan('appkeep:run')->assertExitCode(0);

        // Assert that both checks ran.
        Http::assertSent(function ($request) {
            $data = $request->data();

            return count($data['checks']) === 2;
        });
    }

    /**
     * @test
     */
    public function it_sends_batched_events()
    {
        Http::fake();
        Cache::flush();
        AppkeepService::$slowQueryThreshold = 0;

        // Don't execute checks
        Appkeep::forgetDefaultChecks();

        // Using sqlite, run a slow query without needing any actual tables. Use sleep
        // to simulate a slow query.
        DB::statement('SELECT RANDOM() AS random_number2;');

        // Normally, this would run when the test finishes. Go ahead and run it early.
        $this->app->make(EventCollector::class)->persist();

        $this->artisan('appkeep:run')->assertExitCode(0);

        Http::assertSent(function ($request) {
            $data = $request->data();

            $eventCount = count($data['batch']);
            return $eventCount == 1;
        });
    }
}
