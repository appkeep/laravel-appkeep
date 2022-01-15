<?php

namespace Appkeep\Eye;

use Cron\CronExpression;
use Illuminate\Support\Facades\Date;
use Illuminate\Console\Scheduling\ManagesFrequencies;

abstract class Check
{
    public $name;

    protected string $expression = '* * * * *';

    use ManagesFrequencies {
        // These two methods are not supported.
        between as private;
        unlessBetween as private;
    }

    /**
     * If you don't provide a name,
     * it will default to check's class name.
     **/
    public function __construct($name = null)
    {
        $this->name = $name ?: class_basename($this);
    }

    public static function make($name = null)
    {
        return new static($name);
    }

    /**
     * @return Result
     */
    abstract public function run();

    /**
     * @return bool
     */
    public function isDue()
    {
        $date = Date::now();

        /*  TODO: think about the handling of timezone.
        if ($this->timezone) {
            $date = $date->setTimezone($this->timezone);
        }
        */

        return (new CronExpression($this->expression))->isDue($date->toDateTimeString());
    }
}
