<?php

namespace Appkeep\Laravel\Checks;

use Appkeep\Laravel\Check;
use Appkeep\Laravel\Result;
use Appkeep\Laravel\Enums\Scope;
use Appkeep\Laravel\Contexts\SpecsContext;

class SystemLoadCheck extends Check
{
    public $scope = Scope::SERVER;

    protected $warnAt = 80;
    protected $failAt = 95;

    public static function make($name = null)
    {
        return (new SystemLoadCheck($name))->everyFiveMinutes();
    }

    public function warnIfUsedPercentageIsAbove($value)
    {
        $this->warnAt = (int) $value;

        return $this;
    }

    public function failIfUsedPercentageIsAbove($percent)
    {
        $this->failAt = (int) $percent;

        return $this;
    }

    public function run()
    {
        $maxLoad = SpecsContext::cpuCount();
        $avgLoad = sys_getloadavg()[1]; // Avg system load in the last 5 minutes

        $loadPercentage = round(($avgLoad / $maxLoad) * 100, 2);

        $meta = [
            'type' => 'percent',
            'value' => $loadPercentage / 100,
        ];

        if ($loadPercentage > $this->failAt) {
            return Result::fail('Average server load is too high.')
                ->summary("{$loadPercentage}%")
                ->meta($meta);
        }

        if ($loadPercentage >= $this->warnAt) {
            return Result::warn('Increased average server load.')
                ->summary("{$loadPercentage}%")
                ->meta($meta);
        }

        return Result::ok()
            ->summary("{$loadPercentage}%")
            ->meta($meta);
    }
}
