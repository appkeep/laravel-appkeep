<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Tests\TestCheck;
use Appkeep\Laravel\Result;
use Appkeep\Laravel\Appkeep;
use Illuminate\Support\Facades\Http;

class RunChecksCommandTest extends TestCase
{
    /**
     * @test
     */
    public function the_check_command_is_registered()
    {
        $this->artisan('appkeep:run')->expectsOutput('No checks are due to run.');
    }

    /**
     * @test
     */
    public function it_returns_a_table_output()
    {
        Http::fake();

        $appkeep = resolve(Appkeep::class);

        $appkeep->checks([
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

        $appkeep = resolve(Appkeep::class);

        Carbon::setTestNow(Carbon::now()->setMinute(3)->setSecond(0));

        $appkeep->checks([
            TestCheck::make('test-check-1')->everyMinute(),
            TestCheck::make('test-check-15')->everyFifteenMinutes(),
        ]);

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
}
