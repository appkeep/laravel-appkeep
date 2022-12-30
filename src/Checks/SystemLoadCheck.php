<?php

namespace Appkeep\Laravel\Checks;

use RuntimeException;
use Appkeep\Laravel\Check;
use Appkeep\Laravel\Result;
use Appkeep\Laravel\Enums\Scope;

class SystemLoadCheck extends Check
{
    public $scope = Scope::SERVER;

    protected $warnAt = 70;
    protected $failAt = 90;

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
        $maxLoad = ($this->totalCpus());
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

    private function totalCpus()
    {
        if (PHP_OS === 'Linux') {
            $output = shell_exec('grep -c ^processor /proc/cpuinfo');
        } elseif (PHP_OS === 'Darwin' || PHP_OS === 'FreeBSD') {
            $output = shell_exec('sysctl -n hw.ncpu');
        } else {
            throw new RuntimeException('Operating system not supported!');
        }

        return (int) trim($output);
    }
}
