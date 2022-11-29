<?php

namespace Appkeep\Laravel\Http\Controllers;

use Appkeep\Laravel\Diagnostics\Git;
use Appkeep\Laravel\Facades\Appkeep;
use Appkeep\Laravel\Diagnostics\Laravel;

class InsightsController
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
            logger('GET /appkeep/insights request received but Appkeep is not configured yet. APPKEEP_KEY env variable is missing or is empty.');
            abort(500);
        }

        /*
        if ($hashedKey !== request()->header('x-appkeep-key')) {
            abort(401, "Something's not right...");
        }*/

        $opcache = (bool) opcache_get_status();


        return [
            'php' => [
                'version' => [
                    'text' => PHP_VERSION,
                    'major' => PHP_MAJOR_VERSION,
                    'minor' => PHP_MINOR_VERSION,
                    'release' => PHP_RELEASE_VERSION,
                ],
                'opcache_enabled' => $opcache,
                'xdebug' => extension_loaded('xdebug'),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
            ],
            'packages' => [
                'laravel/framework' => Laravel::version(),
                'appkeep/laravel-appkeep' => Appkeep::version(),
            ],
            'git' => ($hash = Git::shortCommitHash()) ? [
                'commit' => $hash,
                'url' => Git::repositoryUrl(),
            ] : null,
        ];
    }
}
