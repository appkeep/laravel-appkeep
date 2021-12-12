<?php

namespace Tests\Unit;

use Tests\TestCase;
use Appkeep\Eye\Checklist;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class ChecklistTest extends TestCase
{
    /**
     * @test
     */
    public function it_fetches_checklist_from_server()
    {
        $fixture = __DIR__ . '/../_fixtures/checklist.json';

        Http::fake([
            'appkeep.dev/*' => Http::response(json_decode(file_get_contents($fixture), true), 200),
        ]);

        $checklist = Checklist::fetch();

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


        Http::assertSent(function (Request $request) {
            return $request->hasHeader('Authorization', 'EyeSecret test_secret') &&
                $request->url() == 'https://appkeep.dev/api/eye/checklist';
        });
    }
}
