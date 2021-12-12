<?php

namespace Tests\Feature;

use Tests\TestCase;
use Appkeep\Eye\Check;
use Appkeep\Eye\Checks\HeartbeatCheck;

class HeartbeatCheckTest extends TestCase
{
    /**
     * @test
     */
    public function it_always_passes()
    {
        $check = new HeartbeatCheck(new Check());
        $result = $check->run();

        $this->assertTrue($result->passes);
    }
}
