<?php

namespace Appkeep\Laravel;

use Appkeep\Laravel\Facades\Appkeep;
use Illuminate\Support\Facades\Http;
use Appkeep\Laravel\Events\AbstractEvent;
use Appkeep\Laravel\Events\ScheduledTaskEvent;

class HttpClient
{
    /**
     * @var string
     */
    private $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Send data to Appkeep
     */
    public function sendEvent(AbstractEvent $event)
    {
        return Http::withHeaders($this->defaultHeaders())->post(
            config('appkeep.endpoint'),
            $event->toArray()
        );
    }

    /**
     * Send scheduled task result
     */
    public function sendScheduledTaskOutput(ScheduledTaskOutput $output)
    {
        return $this->sendEvent(new ScheduledTaskEvent($output))->throw();
    }

    /**
     * Send batch events to Appkeep
     */
    public function sendBatchEvents(array $events)
    {
        return Http::withHeaders($this->defaultHeaders())->post(
            config('appkeep.endpoint'),
            [
                'batch' => $events,
            ]
        );
    }

    protected function defaultHeaders()
    {
        return [
            'accept' => 'application/json',
            'authorization' => 'Bearer ' . $this->key,
            'x-appkeep-client' => Appkeep::version(),
        ];
    }
}
