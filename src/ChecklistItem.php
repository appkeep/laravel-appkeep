<?php

namespace Appkeep\Eye;

use Cron\CronExpression;
use Illuminate\Support\Facades\Date;

class ChecklistItem
{
    /**
     * Name of the check...
     */
    public string $check;

    /**
     * Frequency of the check.
     * Follows the same format as cron.
     * @see https://crontab.guru/
     */
    public string $frequency;

    /**
     * Arguments passed to the check function.
     */
    public array $arguments = [];

    /**
     * @var Threshold
     */
    public $threshold = null;

    /**
     * TODO: Support timezones?
     * @see https://github.com/laravel/framework/blob/32da5eba2a11c14ab1a268098196fe10ec71b3f4/src/Illuminate/Console/Scheduling/Event.php#L339
     */
    public function isDue()
    {
        return (new CronExpression($this->frequency))->isDue(
            Date::now()->toDateTimeString()
        );
    }
}
