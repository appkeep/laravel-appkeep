<?php

namespace Tests\Feature\Checks;

use Tests\TestCase;
use Appkeep\Laravel\Enums\Status;
use Appkeep\Laravel\Checks\DiskUsageCheck;

class DiskUsageCheckTest extends TestCase
{
    /**
     * @test
     */
    public function it_allows_changing_warning_threshold()
    {
        $result = DiskUsageCheck::make()->run();
        $diskUsage = $result->meta['value'];


        $result = DiskUsageCheck::make()
            ->warnIfUsedPercentageIsAbove(($diskUsage * 100) - 1)
            ->failIfUsedPercentageIsAbove(100)
            ->run();

        $this->assertEquals(Status::WARN, $result->status);
        $this->assertStringStartsWith('Your disk is getting full! Only ', $result->message);
    }

    /**
     * @test
     */
    public function it_allows_changing_the_fail_threshold()
    {
        $result = DiskUsageCheck::make()->run();
        $diskUsage = $result->meta['value'];


        $result = DiskUsageCheck::make()
            ->warnIfUsedPercentageIsAbove(1)
            ->failIfUsedPercentageIsAbove(($diskUsage * 100) - 1)
            ->run();

        $this->assertEquals(Status::FAIL, $result->status);
        $this->assertStringStartsWith('Your disk is too full! Only ', $result->message);
    }
}
