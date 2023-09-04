<?php

namespace Appkeep\Laravel;

use Illuminate\Support\Facades\Cache;
use Appkeep\Laravel\Events\AbstractEvent;

/**
 * This class is responsible for collecting events and storing them.
 */
class EventCollector
{
    private $cacheKey = 'appkeep.events';

    private $maxItemsInMemory = 20;

    private $maxItemsInCache = 100;

    private $events = [];

    /**
     * Push an event to cache.
     */
    public function push(AbstractEvent $event)
    {
        $data = $event->toArray();
        $json = json_encode($data);
        $hash = md5($json);

        // For now, we just store it in the memory
        if (isset($this->events[$hash])) {
            return;
        }

        if (count($this->events) >= $this->maxItemsInMemory) {
            logger()->debug('Appkeep: Reached maxItemsInMemory. Dropping event.');

            return;
        }

        $this->events[$hash] = $json;
    }

    /**
     * Persist events in the memory to cache.
     */
    public function persist()
    {
        $storedEvents = Cache::get($this->cacheKey, []);
        $numStoredEvents = count($storedEvents);

        foreach ($this->events as $hash => $json) {
            // Item already exists in cache.
            if (isset($storedEvents[$hash])) {
                continue;
            }

            if ($numStoredEvents >= $this->maxItemsInCache) {
                logger()->warning(
                    'Appkeep: Event cache is full. Dropping event.'
                        . ' Run appkeep:run to send cached events.'
                );

                break;
            }

            $storedEvents[$hash] = $json;
            $numStoredEvents++;
        }

        // Put the updated events back to cache.
        Cache::forever($this->cacheKey, $storedEvents);
    }

    /**
     * Pull all events from cache and clear the cache.
     */
    public function pull()
    {
        $events = Cache::pull($this->cacheKey, []);

        return $events;
    }
}
