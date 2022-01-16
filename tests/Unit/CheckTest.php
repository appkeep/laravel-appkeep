<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use Tests\TestCheck;

class CheckTest extends TestCase
{
    /**
     * @test
     */
    public function check_name_defaults_to_class_name()
    {
        $testCheck = TestCheck::make();
        $this->assertEquals('TestCheck', $testCheck->name);
    }

    /**
     * @test
     */
    public function check_name_can_be_overridden()
    {
        $testCheck = TestCheck::make('override');
        $this->assertEquals('override', $testCheck->name);
    }

    /**
     * @test
     */
    public function it_manages_frequencies()
    {
        $testCheck = TestCheck::make()->daily();
        $this->assertEquals('0 0 * * *', $testCheck->expression);
    }

    /**
     * @test
     */
    public function it_can_determine_if_check_is_due()
    {
        $testCheck = TestCheck::make()->dailyAt('12:00');

        Carbon::setTestNow(Carbon::now()->setTime(11, 00));
        $this->assertFalse($testCheck->isDue());

        Carbon::setTestNow(Carbon::now()->setTime(12, 00));
        $this->assertTrue($testCheck->isDue());
    }
}
