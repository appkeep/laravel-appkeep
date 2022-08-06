<?php

namespace Tests\Unit;

use Tests\TestCase;
use Appkeep\Laravel\Health\Check;
use Appkeep\Laravel\Health\Checks\CacheCheck;
use Appkeep\Laravel\Health\Checks\HorizonCheck;
use Appkeep\Laravel\Health\Checks\StorageCheck;
use Appkeep\Laravel\Health\Checks\DatabaseCheck;
use Appkeep\Laravel\Health\Checks\DiskUsageCheck;
use Appkeep\Laravel\Health\Checks\EnvironmentCheck;
use Appkeep\Laravel\Health\Checks\UbuntuSecurityUpdatesCheck;

class CheckFilesTest extends TestCase
{
    /**
     * @test
     * @dataProvider checksProvider
     */
    public function check_class_exists($class)
    {
        $this->assertTrue(class_exists($class));

        $check = $class::make();

        $this->assertInstanceOf(
            Check::class,
            $check
        );
    }

    public function checksProvider(): array
    {
        return [
            [CacheCheck::class],
            [DatabaseCheck::class],
            [DiskUsageCheck::class],
            [EnvironmentCheck::class],
            [HorizonCheck::class],
            [StorageCheck::class],
            [UbuntuSecurityUpdatesCheck::class],
        ];
    }
}
