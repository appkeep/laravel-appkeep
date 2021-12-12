<?php

namespace Tests\Unit;

use Tests\TestCase;
use Appkeep\Eye\Threshold;

class ThresholdTest extends TestCase
{
    public function test_equals_comparator()
    {
        $threshold = new Threshold('abc', '=');

        $this->assertFalse($threshold->passes('xyz'));
        $this->assertTrue($threshold->passes('abc'));
    }

    public function test_not_equals_comparator()
    {
        $threshold = new Threshold('abc', '!=');

        $this->assertFalse($threshold->passes('abc'));
        $this->assertTrue($threshold->passes('xyz'));
    }

    public function test_greater_than_comparator()
    {
        $threshold = new Threshold(1, '>');

        $this->assertFalse($threshold->passes(1));
        $this->assertTrue($threshold->passes(2));
    }

    public function test_greater_than_or_equal_comparator()
    {
        $threshold = new Threshold(1, '>=');

        $this->assertFalse($threshold->passes(0));
        $this->assertTrue($threshold->passes(1));
        $this->assertTrue($threshold->passes(2));
    }

    public function test_less_than_comparator()
    {
        $threshold = new Threshold(1, '<');

        $this->assertFalse($threshold->passes(1));
        $this->assertTrue($threshold->passes(0));
    }

    public function test_invalid_comparator()
    {
        $threshold = new Threshold(1, 'x');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Invalid comparator x/');

        $threshold->passes(1);
    }
}
