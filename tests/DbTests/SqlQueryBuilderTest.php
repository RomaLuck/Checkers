<?php

namespace App\Tests\DbTests;

use App\Db\SqlQueryBuilder;
use Mockery;
use PDO;
use PHPUnit\Framework\TestCase;

class SqlQueryBuilderTest extends TestCase
{
    protected PDO $pdo;
    private string $table = 'CheckerDesk';
    protected SqlQueryBuilder $sqlQueryBuilder;

    protected function setUp(): void
    {
        $this->pdo = Mockery::mock(PDO::class);
//        $this->pdo->shouldReceive('connect')->andReturn($this->pdo);
        $this->sqlQueryBuilder = new SqlQueryBuilder($this->pdo);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testSelectWithoutValues(): void
    {
        $result = $this->sqlQueryBuilder->select($this->table)->getSQL();
        $expected = 'SELECT * FROM CheckerDesk;';
        $this->assertEquals($expected, $result);
    }

    public function testSelectWithValues(): void
    {
        $result = $this->sqlQueryBuilder->select($this->table, ['a1', 'b2'])->getSQL();
        $expected = "SELECT a1, b2 FROM CheckerDesk;";
        $this->assertEquals($expected, $result);
    }

    public function testInsert(): void
    {
        $result = $this->sqlQueryBuilder->insert($this->table, ['team' => ':white'])->getSQL();
        $expected = "INSERT INTO CheckerDesk (team) VALUES (:white);";
        $this->assertEquals($expected, $result);
    }

    public function testUpdate(): void
    {
        $result = $this->sqlQueryBuilder->update($this->table, ['team' => ':white'])->where('id', '1')->getSQL();
        $expected = "UPDATE CheckerDesk SET team=:white WHERE id = 1;";
        $this->assertEquals($expected, $result);
    }
}