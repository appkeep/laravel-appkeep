<?php

namespace Appkeep\Laravel;

use Appkeep\Laravel\Facades\Appkeep;
use Illuminate\Support\Facades\Http;
use Appkeep\Laravel\Events\CronjobEvent;
use Appkeep\Laravel\Events\AbstractEvent;

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
        return Http::withoutVerifying()->withHeaders($this->defaultHeaders())->post(
            config('appkeep.endpoint'),
            $event->toArray()
        );
    }

    /**
     * Send cronjob result
     */
    public function sendCronjobOutput(CronjobOutput $output)
    {
        return $this->sendEvent(new CronjobEvent($output));
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
