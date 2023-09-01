<?php

namespace Tests\Feature;

use Appkeep\Laravel\Listeners\SlowQueryHandler;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

use function PHPUnit\Framework\fileExists;

class SlowQueryTest extends TestCase
{
    private $sql = 'SELECT * FROM TABLE';
    private $bindings = [];
    private $queryTime = 770.00;
    private Connection $connectionMock;

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
        $this->mockConnection();
        $this->clearFile();
    }

    /**
     * @test
     */
    public function should_create_slow_query_file()
    {
        SlowQueryHandler::handle($this->fileName, new QueryExecuted($this->sql, $this->bindings, $this->queryTime, $this->connectionMock));

        $this->assertFileExists($this->fileName);
    }

    /**
     * @test
     */
    public function should_update_the_file_contents()
    {
        SlowQueryHandler::handle($this->fileName, new QueryExecuted($this->sql, $this->bindings, $this->queryTime, $this->connectionMock));
        $fileContents = file_get_contents($this->fileName);

        SlowQueryHandler::handle($this->fileName, new QueryExecuted($this->sql, $this->bindings, $this->queryTime, $this->connectionMock));
        $fileContentsAfter = file_get_contents($this->fileName);

        $this->assertNotEquals($fileContents, $fileContentsAfter);
    }

    /**
     * @test
     */
    public function should_check_file_json_parseable()
    {
        SlowQueryHandler::handle($this->fileName, new QueryExecuted($this->sql, $this->bindings, $this->queryTime, $this->connectionMock));
        $fileContents = file_get_contents($this->fileName);

        $this->assertJson($fileContents);
    }
}
