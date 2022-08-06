<?php

namespace Appkeep\Laravel\Health\Checks;

use Exception;
use Appkeep\Laravel\Enums\Status;
use Appkeep\Laravel\Health\Check;
use Appkeep\Laravel\Health\Result;
use Illuminate\Support\Facades\Storage;

class StorageCheck extends Check
{
    private $disk;

    public static function make($name = null)
    {
        return (new StorageCheck($name))->everyFiveMinutes();
    }

    public function disk($disk)
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * @var Result
     */
    public function run()
    {
        $disk = $this->disk ?: config('filesystems.default');

        $result = (new Result(Status::OK))->meta([
            'disk' => $disk,
        ]);

        try {
            $this->testDisk($disk);

            return $result;
        } catch (Exception $e) {
            return $result->failWith($e->getMessage());
        }
    }

    protected function testDisk($disk)
    {
        $value = time();
        $filename = 'appkeep-check.txt';

        $storage = Storage::disk($disk);
        $storage->put($filename, $value);

        $retrievedValue = $storage->get($filename);

        if ($value != $retrievedValue) {
            throw new Exception('Retrieved value does not match stored value.');
        }

        $storage->delete($filename);
    }
}
