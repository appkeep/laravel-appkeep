<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use Appkeep\Eye\Check;

class CheckTest extends TestCase
{
    /**
     * @test
     */
    public function it_evaluates_frequency()
    {
        // 12 December, 2021 @ 00:02:00
        Carbon::setTestNow(Carbon::create(2021, 12, 12, 0, 2));

        $item = new Check();

        $item->frequency = '* * * * *';
        $this->assertTrue($item->isDue());

        $item->frequency = '*/5 * * * *';
        $this->assertFalse($item->isDue());

        // 12 December, 2021 @ 00:05:00
        Carbon::setTestNow(Carbon::create(2021, 12, 12, 0, 5));
        $this->assertTrue($item->isDue());
    }

    /**
     * @test
     */
    public function it_throws_error_if_frequency_is_invalid()
    {
        $item = new Check();
        $item->frequency = 'invalid value';

        $this->expectExceptionMessage('CRON');
        $this->assertTrue($item->isDue());
    }
}
