<?php

namespace Appkeep\Eye\Tasks;

use Illuminate\Console\Scheduling\Event;

abstract class Task extends Event
{
    protected $settings = [];

    public function __construct(array $settings)
    {
        $this->settings = array_merge(
            $this->getDefaultSettings(),
            $settings
        );
    }

    public static function make(array $settings = [])
    {
        $task = new static($settings);

        return $task;
    }

    protected function getDefaultSettings()
    {
        return [];
    }

    protected function updateSetting($key, $value)
    {
        $this->settings[$key] = $value;
    }
}
