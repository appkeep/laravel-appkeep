<?php

namespace Appkeep\Laravel\Support;

use Illuminate\Console\Scheduling\Event;

/**
 * Allows us to get a ID for a scheduled event.
 * This way we can match up pings from events to records on Appkeep server.
 */
class ScheduledTaskId
{
    public static function get(Event $event)
    {
        return hash('md5', 'laravel-' . $event->command . '-' . $event->expression);
    }
}
