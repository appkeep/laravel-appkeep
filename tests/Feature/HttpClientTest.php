<?php

namespace Tests\Feature;

use Tests\TestCase;
use Appkeep\Laravel\HttpClient;
use Appkeep\Laravel\Facades\Appkeep;
use Illuminate\Support\Facades\Http;
use Appkeep\Laravel\ScheduledTaskOutput;
use Appkeep\Laravel\Events\AbstractEvent;

class HttpClientTest extends TestCase
{
    /**
     * @test
     */
    public function it_sends_event()
    {
        Http::fake();

        $client = new HttpClient('appkeep_key');
        $client->sendEvent(new class extends AbstractEvent {

            protected $name = 'test_event';

            public function toArray()
            {
                return array_merge(
                    parent::toArray(),
                    [
                        'foo' => 'bar',
                        'baz' => 'qux',
                    ]
                );
            }
        });

        $this->assertPayloadSentToAppkeep([
            'name' => 'test_event',
            'foo' => 'bar',
            'baz' => 'qux',
        ]);
    }

    /**
     * @test
     */
    public function it_sends_scheduled_task_output()
    {
        Http::fake();

        $output = new ScheduledTaskOutput('heartbeat:test');

        $output->failed()
            ->setDuration(1.2)
            ->setOutput('xyz')
            ->setStartedAt(now()->subMinutes(10))
            ->setFinishedAt(now());

        $client = new HttpClient('appkeep_key');
        $client->sendScheduledTaskOutput($output);

        $this->assertPayloadSentToAppkeep([
            'name' => 'scheduled-task',
            'output' => [
                'id' => 'heartbeat:test',
                'success' => false,
                'duration' => 1.2,
                'output' => 'xyz',
                'started_at' => $output->startedAt,
                'finished_at' => $output->finishedAt,
            ],
        ]);
    }

    private function assertPayloadSentToAppkeep($payload)
    {
        Http::assertSent(function ($request) use ($payload) {
            $headers = $request->headers();

            $this->assertEquals(
                config('appkeep.endpoint'),
                $request->url()
            );

            $this->assertEquals(
                'POST',
                $request->method()
            );

            $this->assertEquals(
                'application/json',
                $headers['accept'][0]
            );

            $this->assertEquals(
                'Bearer appkeep_key',
                $headers['authorization'][0]
            );

            $this->assertEquals(
                Appkeep::version(),
                $headers['x-appkeep-client'][0]
            );

            // dd($request->body());

            foreach ($payload as $key => $value) {
                $this->assertEquals(
                    $value,
                    $request[$key]
                );
            }

            return true;
        });
    }
}
