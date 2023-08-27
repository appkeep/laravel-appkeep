<?php

namespace Tests\Feature\Checks;

use Illuminate\Support\Facades\DB;
use Mockery;
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
        // Set DB Mock
        DB::shouldReceive("connection")
            ->once()
            ->with('mysql')
            ->andReturn(
                Mockery::mock('Illuminate\Database\MysqlConnection', function ($mock) {
                    $mock->shouldReceive("selectOne")
                        ->once()
                        ->with('show status where variable_name = "threads_connected"')
                        ->andReturn([
                            'Value' => 155
                        ]);

                    $mock->shouldReceive("selectOne")
                        ->once()
                        ->with("show variables like 'max_connections'")
                        ->andReturn([
                            'Value' => 155
                        ]);
                })
            );


        $result = DBConnectionCountCheck::make()->setConnectionName('mysql')->warnIfConnectionCountIsAbove(35)->run();
        $this->assertEquals(Status::FAIL, $result->status);
    }
    /**
     * @test
     */
    public function warns_if_connection_count_is_exceeded()
    {
        // Set DB Mock
        DB::shouldReceive("connection")
            ->once()
            ->with('mysql')
            ->andReturn(
                Mockery::mock('Illuminate\Database\MysqlConnection', function ($mock) {
                    $mock->shouldReceive("selectOne")
                        ->once()
                        ->with('show status where variable_name = "threads_connected"')
                        ->andReturn([
                            'Value' => 50
                        ]);

                    $mock->shouldReceive("selectOne")
                        ->once()
                        ->with("show variables like 'max_connections'")
                        ->andReturn([
                            'Value' => 155
                        ]);
                })
            );


        $result = DBConnectionCountCheck::make()->setConnectionName('mysql')->warnIfConnectionCountIsAbove(35)->run();
        $this->assertEquals(Status::WARN, $result->status);
    }

    /**
     * @test
     */
    public function succeed_if_connection_count_is_below_warn_limit()
    {
        // Set DB Mock
        DB::shouldReceive("connection")
            ->once()
            ->with('mysql')
            ->andReturn(
                Mockery::mock('Illuminate\Database\MysqlConnection', function ($mock) {
                    $mock->shouldReceive("selectOne")
                        ->once()
                        ->with('show status where variable_name = "threads_connected"')
                        ->andReturn([
                            'Value' => 8
                        ]);

                    $mock->shouldReceive("selectOne")
                        ->once()
                        ->with("show variables like 'max_connections'")
                        ->andReturn([
                            'Value' => 155
                        ]);
                })
            );


        $result = DBConnectionCountCheck::make()->setConnectionName('mysql')->warnIfConnectionCountIsAbove(35)->run();
        $this->assertEquals(Status::OK, $result->status);
    }
}
