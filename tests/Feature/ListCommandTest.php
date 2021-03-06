<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestCheck;
use Appkeep\Laravel\Facades\Appkeep;

class ListCommandTest extends TestCase
{
    /**
     * @test
     */
    public function the_list_command_is_registered()
    {
        Appkeep::forgetDefaultChecks();

        $this->artisan('appkeep:list')->expectsOutput('No checks are set up.');
    }

    /**
     * @test
     */
    public function it_returns_a_table_output()
    {
        Appkeep::forgetDefaultChecks()->checks([
            TestCheck::make('bar')->everyFifteenMinutes(),
            TestCheck::make('baz')->dailyAt('12:00'),
        ]);

        $this->artisan('appkeep:list')->assertExitCode(0)->expectsTable(
            ['Check', 'Expression'],
            [
                ['bar', '*/15 * * * *'],
                ['baz', '0 12 * * *'],
            ]
        );
    }
}
