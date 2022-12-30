<?php

namespace Appkeep\Laravel\Checks;

use Exception;
use Appkeep\Laravel\Check;
use Appkeep\Laravel\Result;
use Illuminate\Support\Facades\Cache;

class CacheCheck extends Check
{
    private $driver;

    /**
     * Check a different cache driver
     */
    public function driver($driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @var \Appkeep\Laravel\Health\Result
     */
    public function run()
    {
        $driver = $this->driver ?? config('cache.default', 'file');
        $meta = ['driver' => $driver];

        try {
            $this->testCache($driver);
        } catch (Exception $e) {
            return Result::fail($e->getMessage())->meta($meta);
        }

        return Result::ok()->meta($meta);
    }

    protected function testCache($driver)
    {
        $value = time();

        Cache::driver($driver)->put('appkeep:check', $value, 5);

        if ($value != Cache::driver($driver)->pull('appkeep:check')) {
            throw new Exception('Could not read/write cache');
        }
    }
}
