<?php

namespace Tests\Feature\Checks;

use Tests\TestCase;
use Appkeep\Laravel\Enums\Status;
use Appkeep\Laravel\Checks\DBConnectionCountCheck;

class DBConnectionCountCheckTest extends TestCase
{
    /**
     * @test
     */
    public function fails_if_connection_count_is_exceeded()
    {
        $result = DBConnectionCountCheck::make()->setConnectionName('mysql')->run();

        // print_r($result);

        // $result = DatabaseCheck::make()->run();
        // $this->assertEquals(Status::FAIL, $result->status);
        // $this->assertStringStartsWith('Could not connect to DB:', $result->message);
    }
    /**
     * @test
     */
    public function warns_if_connection_count_is_exceeded()
    {
    }
}
