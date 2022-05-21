<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestCheck;
use Appkeep\Laravel\Appkeep;

class ListChecksCommandTest extends TestCase
{
    /**
     * @test
     */
    public function the_list_command_is_registered()
    {
        $this->artisan('appkeep:checks')->expectsOutput('No checks are set up.');
    }

    /**
     * @test
     */
    public function it_returns_a_table_output()
    {
        $appkeep = resolve(Appkeep::class);

        $appkeep->checks([
            TestCheck::make('bar')->everyFifteenMinutes(),
            TestCheck::make('baz')->dailyAt('12:00'),
        ]);

        $this->artisan('appkeep:checks')->assertExitCode(0)->expectsTable(
            ['Check', 'Expression'],
            [
                ['bar', '*/15 * * * *'],
                ['baz', '0 12 * * *'],
            ]
        );
    }
}
