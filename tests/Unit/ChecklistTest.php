<?php

namespace Tests\Unit;

use Tests\TestCase;
use Appkeep\Eye\Checklist;

class ChecklistTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_deserialize_from_json()
    {
        $fixture = __DIR__ . '/../_fixtures/checklist.json';
        $checklist = Checklist::fromJson(file_get_contents($fixture));

        // It contains the default check.
        $this->assertCount(2, $checklist->values());

        $item = $checklist->first();
        $this->assertEquals('disk_usage', $item->check);
        $this->assertEquals('*/5 * * * *', $item->frequency);
        $this->assertEquals(['disk' => 'local'], $item->arguments);

        $this->assertEquals(0.8, $item->threshold->threshold);
        $this->assertEquals('>', $item->threshold->comparator);

        $item = $checklist->last();
        $this->assertEquals('heartbeat', $item->check);
        $this->assertEquals('* * * * *', $item->frequency);
        $this->assertEquals([], $item->arguments);
        $this->assertNull($item->threshold);
    }
}
