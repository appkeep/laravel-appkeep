<?php

namespace Appkeep\Laravel\Checks;

use Appkeep\Laravel\Check;
use Appkeep\Laravel\Result;
use Illuminate\Support\Str;

class OptimizationCheck extends Check
{
    private static $checks = [
        'routesAreCached',
        'configIsCached',
        'eventsAreCached',
        'servicesAreCached',
        'packagesAreCached',
    ];

    public function run()
    {
        $output = collect([]);

        foreach (static::$checks as $check) {
            $output->put(
                $check,
                call_user_func([$this, $check])
            );
        }

        $failing = $output->where(null, false);

        // All optimizations are done!
        if ($failing->isEmpty()) {
            return Result::ok();
        }

        $title = sprintf(
            'Missing %d %s',
            $failing->count(),
            Str::plural('optimization', $failing->count())
        );

        return Result::warn($title)
            ->message(
                'Missing optimizations: ' .
                    $failing->keys()
                    ->map(
                        fn ($check) => Str::of($check)
                            ->snake()
                            ->replace('_', ' ')
                            ->ucfirst()
                            ->words(1, '')
                    )
                    ->join(', ')
            );
    }

    private function routesAreCached()
    {
        return app()->routesAreCached();
    }

    private function configIsCached()
    {
        return app()->configurationIsCached();
    }

    private function eventsAreCached()
    {
        return app()->eventsAreCached();
    }

    private function servicesAreCached()
    {
        return file_exists(app()->getCachedServicesPath());
    }

    private function packagesAreCached()
    {
        return file_exists(app()->getCachedPackagesPath());
    }
}
