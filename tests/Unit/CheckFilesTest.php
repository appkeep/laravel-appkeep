<?php

namespace Tests\Unit;

use Tests\TestCase;
use Appkeep\Laravel\Check;
use Appkeep\Laravel\Checks\CacheCheck;
use Appkeep\Laravel\Checks\HorizonCheck;
use Appkeep\Laravel\Checks\StorageCheck;
use Appkeep\Laravel\Checks\DatabaseCheck;
use Appkeep\Laravel\Checks\DiskUsageCheck;
use Appkeep\Laravel\Checks\ProductionModeCheck;
use Appkeep\Laravel\Checks\SystemLoadCheck;
use Appkeep\Laravel\Checks\UbuntuSecurityUpdatesCheck;

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
            [HorizonCheck::class],
            [StorageCheck::class],
            [SystemLoadCheck::class],
            [ProductionModeCheck::class],
            [UbuntuSecurityUpdatesCheck::class],
        ];
    }
}
