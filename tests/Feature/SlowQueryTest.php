<?php

namespace Tests\Feature;

use Appkeep\Laravel\Facades\Appkeep;
use Appkeep\Laravel\Listeners\SlowQueryHandler;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class SlowQueryTest extends TestCase
{
    private $sql = 'SELECT * FROM TABLE';
    private $bindings = [];
    private $queryTime = 770.00;
    private Connection $connectionMock;

    private $context = [
        'command' => 'test'
    ];

    private $fileName = 'tmp_slow_query_test.json';

    private function mockConnection(): void
    {
        $this->connectionMock = Mockery::mock(Connection::class);
        $this->connectionMock->shouldReceive('getName')->andReturn('DatabaseName');
    }

    private function clearFile(): void
    {
        file_put_contents($this->fileName, '');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockConnection();
        $this->clearFile();
    }

    /**
     * @test
     */
    public function should_create_slow_query_file()
    {
        SlowQueryHandler::handle($this->fileName, new QueryExecuted($this->sql, $this->bindings, $this->queryTime, $this->connectionMock), $this->context);

        $this->assertFileExists($this->fileName);
    }

    /**
     * @test
     */
    public function should_update_the_file_contents()
    {
        SlowQueryHandler::handle($this->fileName, new QueryExecuted($this->sql, $this->bindings, $this->queryTime, $this->connectionMock), $this->context);
        $fileContents = file_get_contents($this->fileName);

        SlowQueryHandler::handle($this->fileName, new QueryExecuted($this->sql, $this->bindings, $this->queryTime, $this->connectionMock), $this->context);
        $fileContentsAfter = file_get_contents($this->fileName);

        $this->assertNotEquals($fileContents, $fileContentsAfter);
    }

    /**
     * @test
     */
    public function should_check_file_json_parseable()
    {
        SlowQueryHandler::handle($this->fileName, new QueryExecuted($this->sql, $this->bindings, $this->queryTime, $this->connectionMock), $this->context);
        $fileContents = file_get_contents($this->fileName);

        $this->assertJson($fileContents);
    }


    /**
     * @test
     */
    public function should_check_file_empty_after_batching()
    {
        SlowQueryHandler::handle($this->fileName, new QueryExecuted($this->sql, $this->bindings, $this->queryTime, $this->connectionMock), $this->context);
        $fileContents = file_get_contents($this->fileName);

        $this->artisan("appkeep:batch-slow-queries {$this->fileName} --clear")->assertExitCode(0);

        $fileContentsAfterFlush = file_get_contents($this->fileName);
        $this->assertNotEquals($fileContents, $fileContentsAfterFlush);
        $this->assertEquals(strlen($fileContentsAfterFlush), 0);
    }

    /**
     * @test
     */
    public function should_output_slow_queries_to_console()
    {
        SlowQueryHandler::handle($this->fileName, new QueryExecuted($this->sql, $this->bindings, $this->queryTime, $this->connectionMock), $this->context);
        $this->artisan("appkeep:batch-slow-queries {$this->fileName}")->expectsTable(
            ['SQL', 'Connection', 'Time', 'Context'],
            [
                [$this->sql, $this->connectionMock->getName(), $this->queryTime, json_encode($this->context)],
            ]
        );
    }
}
