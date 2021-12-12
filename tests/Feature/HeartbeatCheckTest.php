<?php

namespace Tests\Feature;

use Tests\TestCase;
use Appkeep\Eye\ChecklistItem;
use Appkeep\Eye\Checks\HeartbeatCheck;

class HeartbeatCheckTest extends TestCase
{
    /**
     * @test
     */
    public function it_always_passes()
    {
        $check = new HeartbeatCheck(new ChecklistItem());
        $result = $check->run();

        $this->assertTrue($result->passes);
    }
}
