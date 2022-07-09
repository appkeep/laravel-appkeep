<?php

namespace Appkeep\Laravel\Checks;

use Exception;
use Appkeep\Laravel\Check;
use Appkeep\Laravel\Result;
use Appkeep\Laravel\Enums\Status;
use Illuminate\Support\Facades\Cache;

class CacheCheck extends Check
{
    private $driver;

    public function driver($driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @var Result
     */
    public function run()
    {
        $driver = $this->driver ?? config('cache.default');

        $result = (new Result(Status::OK))->meta([
            'driver' => $driver,
        ]);

        try {
            $this->testCache($driver);

            return $result;
        } catch (Exception $e) {
            dd($e);

            return $result->failWith("Could not read/write cache");
        }
    }

    protected function testCache($driver)
    {
        $value = time();

        Cache::driver($driver)->put('appkeep:check', $value, 1);

        if ($value !== Cache::driver($driver)->pull('appkeep:check')) {
            throw new Exception('Cache test failed.');
        }
    }
}
