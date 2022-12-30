<?php

namespace Appkeep\Laravel\Support;

use Illuminate\Console\Scheduling\Event;

/**
 * Allows us to get a ID for a scheduled event.
 * This way we can match up pings from events to records on Appkeep server.
 */
class ScheduledEventId
{
    public static function get(Event $event)
    {
        return hash('md5', $event->command . '-' . $event->expression);
    }
}
