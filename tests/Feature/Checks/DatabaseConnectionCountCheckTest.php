<?php

namespace Tests\Feature\Checks;

use Mockery;
use Tests\TestCase;
use Appkeep\Laravel\Enums\Status;
use Illuminate\Support\Facades\DB;
use Appkeep\Laravel\Checks\DatabaseConnectionCountCheck;

class DatabaseConnectionCountCheckTest extends TestCase
{
    /**
     * @test
     */
    public function fails_if_connection_count_is_exceeded()
    {
        $this->mockDatabaseConnection(155, 155);

        $result = DatabaseConnectionCountCheck::make()->connection('mysql')->warnIfConnectionCountIsAbove(35)->run();
        $this->assertEquals(Status::FAIL, $result->status);
    }

    /**
     * @test
     */
    public function warns_if_connection_count_is_exceeded()
    {
        $this->mockDatabaseConnection(50, 155);

        $result = DatabaseConnectionCountCheck::make()->connection('mysql')->warnIfConnectionCountIsAbove(35)->run();
        $this->assertEquals(Status::WARN, $result->status);
    }

    /**
     * @test
     */
    public function succeed_if_connection_count_is_below_warn_limit()
    {
        $this->mockDatabaseConnection(8, 155);

        $result = DatabaseConnectionCountCheck::make()->connection('mysql')->warnIfConnectionCountIsAbove(35)->run();
        $this->assertEquals(Status::OK, $result->status);
    }

    private function mockDatabaseConnection($currentConnections, $maxConnections)
    {
        // Set DB Mock
        DB::shouldReceive("connection")
            ->once()
            ->with('mysql')
            ->andReturn(
                Mockery::mock('Illuminate\Database\MysqlConnection', function ($mock) use ($currentConnections, $maxConnections) {
                    $mock->shouldReceive('getDriverName')->andReturn('mysql');

                    $mock->shouldReceive("selectOne")
                        ->once()
                        ->with('SHOW STATUS LIKE "threads_connected"')
                        ->andReturn((object) [
                            'Value' => $currentConnections,
                        ]);

                    $mock->shouldReceive("selectOne")
                        ->once()
                        ->with('SHOW VARIABLES LIKE "max_connections"')
                        ->andReturn((object) [
                            'Value' => $maxConnections,
                        ]);
                })
            );
    }
}
