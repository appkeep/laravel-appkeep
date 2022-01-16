<?php

namespace Appkeep\Eye\Checks;

use Appkeep\Eye\Check;
use Appkeep\Eye\Result;

class DiskUsageCheck extends Check
{
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
        $freeSpace = disk_free_space(base_path());
        $usedSpace = $freeSpace / disk_total_space(base_path());
        $usedSpace = round($usedSpace * 100);

        if ($usedSpace >= $this->failAt) {
            return Result::fail('Your disk is too full! Only ' . $this->humanFileSize($freeSpace) . ' left.')
                ->summary("{$usedSpace}%");
        }

        if ($usedSpace >= $this->warnAt) {
            return Result::warn('Your disk is getting full! Only ' . $this->humanFileSize($freeSpace) . ' left.')
                ->summary("{$usedSpace}%");
        }

        return Result::ok()->summary("{$usedSpace}%");
    }

    private function humanFileSize($size, $unit = "")
    {
        if ((! $unit && $size >= 1 << 30) || $unit == "GB") {
            return number_format($size / (1 << 30), 2) . "GB";
        }
        if ((! $unit && $size >= 1 << 20) || $unit == "MB") {
            return number_format($size / (1 << 20), 2) . "MB";
        }
        if ((! $unit && $size >= 1 << 10) || $unit == "KB") {
            return number_format($size / (1 << 10), 2) . "KB";
        }

        return number_format($size) . " bytes";
    }
}
