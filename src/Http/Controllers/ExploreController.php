<?php

namespace Appkeep\Laravel\Http\Controllers;

use Appkeep\Laravel\Facades\Appkeep;
use Appkeep\Laravel\Events\ExploreEvent;

class ExploreController
{
    public function __invoke()
    {
        $ourServers = config('appkeep.our_servers');

        $hashedIp = hash('sha256', request()->ip());
        $hashedKey = hash('sha256', config('appkeep.key'));

        // This endpoint doesn't even exist for strangers. ¯\_(ツ)_/¯
        if (! in_array($hashedIp, $ourServers)) {
            abort(404);
        }

        if (! config('appkeep.key')) {
            logger('GET /appkeep/explore request received but Appkeep is not configured yet. APPKEEP_KEY env variable is missing or is empty.');
            abort(500);
        }

        if ($hashedKey !== request()->header('x-appkeep-key')) {
            abort(401, "Something's not right...");
        }

        Appkeep::client()->sendEvent(new ExploreEvent);
    }
}
