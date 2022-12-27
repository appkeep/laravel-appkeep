<?php

namespace Tests\Unit;

use Tests\TestCase;
use Appkeep\Laravel\Events\AbstractEvent;

class EventTest extends TestCase
{
    /**
     * @test
     */
    public function it_sets_default_contexts()
    {
        $event = new class extends AbstractEvent {
            protected $name = 'test';
        };

        $data = $event->toArray();

        $this->assertEquals('test', $data['name']);

        $this->assertArrayHasKey('os', $data['context']);
        $this->assertArrayHasKey('server', $data['context']);
        $this->assertArrayHasKey('runtime', $data['context']);
        $this->assertArrayHasKey('appkeep', $data['context']);
    }
}
