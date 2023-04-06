<?php

namespace Appkeep\Laravel\Checks;

use Appkeep\Laravel\Check;
use Appkeep\Laravel\Result;
use Carbon\CarbonInterface;
use Appkeep\Laravel\Enums\Scope;
use Illuminate\Support\Facades\Cache;

class QueueHealthCheck extends Check
{
    public $scope = Scope::GLOBAL;

    /**
     * Where to dispatch the job.
     */
    private $queue;

    /**
     * How many minutes to wait in between dispatches.
     */
    private $dispatchMinutes = 10;

    // In seconds...
    protected $warnAt = 45;
    protected $failAt = 120;

    /**
     * Raise a warning if the job takes longer than $seconds.
     */
    public function warnIfJobTakesLongerThan($seconds)
    {
        $this->warnAt = (int) $seconds;

        return $this;
    }

    /**
     * Raise a failure if the job takes longer than $seconds.
     */
    public function failIfJobTakesLongerThan($seconds)
    {
        $this->failAt = (int) $seconds;

        return $this;
    }

    public static function make($queue = 'default')
    {
        return (new static('queue-check.' . $queue))->queue($queue);
    }

    public function queue($queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * How often should we dispatch the test job?
     * Defaults to every 10 minutes.
     */
    public function dispatchFrequency($minutes)
    {
        $this->dispatchMinutes = $minutes;

        return $this;
    }

    public function run()
    {
        $result = Result::ok();

        // By default, we'll dispatch a new job every time this check runs.
        $shouldDispatchNewJob = true;
        $lastDispatchedAt = null;
        $lastValue = Cache::get($this->cacheKey());

        if ($lastValue) {
            $lastDispatchedAt = $lastValue['dispatched_at'];

            $startTime = $lastDispatchedAt->copy();
            $endTime = $lastValue['processed_at'] ?? now();

            // If we have an active check, don't dispatch a new one.
            $shouldDispatchNewJob = ! is_null($lastValue['processed_at']);

            $lastJobDuration = $endTime->diffInSeconds($startTime);

            $duration = $startTime->diffForHumans($endTime, [
                'parts' => 2, // Number of duration components to include
                'short' => true, // Use short forms like 's' for seconds, 'm' for minutes, etc.
                'syntax' => CarbonInterface::DIFF_ABSOLUTE,
            ]);

            if ($lastJobDuration >= $this->failAt) {
                $result = Result::fail("{$this->queue} wait time is too long. (> {$duration})");
            } elseif ($lastJobDuration >= $this->warnAt) {
                $result = Result::warn("{$this->queue} wait time is getting longer. (> {$duration})");
            }
        }

        $dispatchAgain = $shouldDispatchNewJob && (
            is_null($lastDispatchedAt) ||
            $lastDispatchedAt->diffInMinutes(now()) >= $this->dispatchMinutes
        );

        if ($dispatchAgain) {
            $this->dispatchNewJob();
        }

        return $result;
    }

    private function cacheKey()
    {
        return 'appkeep.queue-health-check.' . $this->queue;
    }

    protected function dispatchNewJob()
    {
        $dispatchedAt = now();
        $cacheKey = $this->cacheKey();

        Cache::put(
            $cacheKey,
            [
                'dispatched_at' => $dispatchedAt,
                'processed_at' => null,
            ],
            now()->addMinutes(15)
        );

        dispatch(function () use ($cacheKey) {
            $value = Cache::get($cacheKey);

            if (! $value) {
                return;
            }

            Cache::put($cacheKey, [
                'dispatched_at' => $value['dispatched_at'],
                'processed_at' => now(),
            ], now()->addMinutes(15));
        })->onQueue($this->queue);
    }
}
