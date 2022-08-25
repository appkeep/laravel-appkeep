<?php

namespace Tests\Feature\Checks;

use Tests\TestCase;
use Appkeep\Laravel\Enums\Status;
use Appkeep\Laravel\Checks\DatabaseCheck;

class DatabaseCheckTest extends TestCase
{
    /**
     * @test
     */
    public function fails_if_database_connection_fails()
    {
        $this->app['config']->set(
            'database.default',
            'mysql'
        );

        $result = DatabaseCheck::make()->run();
        $this->assertEquals(Status::FAIL, $result->status);
        $this->assertStringStartsWith('Could not connect to DB:', $result->message);
        $this->assertStringContainsString('Access denied for user', $result->message);
    }

    /**
     * @test
     */
    public function returns_ok_if_database_connection_is_ok()
    {
        $this->app['config']->set(
            'database.connections.sqlite_test',
            [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]
        );

        $this->app['config']->set('database.default', 'sqlite_test');

        $result = DatabaseCheck::make()->run();
        $this->assertEquals(Status::OK, $result->status);
    }

    /**
     * @test
     */
    public function it_shares_tested_connection_in_meta()
    {
        $check = new DatabaseCheck();
        $check->connection('sqlite');

        $result = $check->run();
        $this->assertEquals('sqlite', $result->meta['connection']);
    }
}
