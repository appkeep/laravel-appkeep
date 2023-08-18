<?php

namespace Appkeep\Laravel\Contexts;

use RuntimeException;
use Illuminate\Contracts\Support\Arrayable;

class SpecsContext implements Arrayable
{
    public function toArray()
    {
        return [
            'cores' => self::cpuCount(),
            'ram' => $this->totalMemory(),
            'disk' => $this->totalDiskspace(),
        ];
    }

    public static function cpuCount()
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

    private function totalDiskspace()
    {
        if (PHP_OS === 'Linux') {
            $output = shell_exec('df -BG | grep "/$" | awk \'{print $2}\'');
        } elseif (PHP_OS === 'Darwin' || PHP_OS === 'FreeBSD') {
            $output = shell_exec('df -g | grep "/$" | awk \'{print $2}\'');
        } else {
            throw new RuntimeException('Operating system not supported!');
        }

        return (int) trim($output);
    }

    private function totalMemory()
    {
        if (PHP_OS === 'Linux') {
            $output = shell_exec('free -m | grep Mem | awk \'{print $2}\'');
            $output = trim($output) / 1024;  // convert MB to GB
        } elseif (PHP_OS === 'Darwin' || PHP_OS === 'FreeBSD') {
            $output = shell_exec('sysctl -n hw.memsize');
            $output = trim($output) / 1073741824;  // convert bytes to GB
        } else {
            throw new RuntimeException('Operating system not supported!');
        }

        return (int) round(trim($output));
    }
}
