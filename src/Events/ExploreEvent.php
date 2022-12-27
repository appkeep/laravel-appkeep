<?php

namespace Appkeep\Laravel\Events;

/**
 * Here we look into important PHP settings.
 * Appkeep will collect this event by making a request to /appkeep/explore endpoint.
 * This gives us the most reliable information, as sometimes CLI uses a different PHP config (or even a different version).
 */
class ExploreEvent extends AbstractEvent
{
    protected $name = 'explore';

    public function __construct()
    {
        parent::__construct('explore');

        $opcache = (bool) opcache_get_status();

        $this->setContext('php', [
            'opcache_enabled' => $opcache,
            'xdebug' => extension_loaded('xdebug'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
        ]);
    }
}
