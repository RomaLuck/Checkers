<?php

namespace CheckersOOP\tests\dbTests;

use CheckersOOP\src\db\SqlQueryBuilder;
use Dotenv\Dotenv;
use PDO;
use PHPUnit\Framework\TestCase;

class SqlQueryBuilderWithDbTest extends TestCase
{
    protected PDO $pdo;
    private string $table = 'CheckerDeskTest';
    protected SqlQueryBuilder $sqlQueryBuilder;

    protected function setUp(): void
    {
        Dotenv::createUnsafeImmutable(__DIR__ . '/../../')->load();
        $dsn = 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE');
        $username = getenv('DB_USERNAME');
        $password = getenv('DB_PASSWORD');

        $this->pdo = new PDO($dsn, $username, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->pdo->exec('CREATE TABLE IF NOT EXISTS ' . $this->table . ' (
            id INT AUTO_INCREMENT PRIMARY KEY,
            team VARCHAR(100),
            figure VARCHAR(100)
        )');

        $this->sqlQueryBuilder = new SqlQueryBuilder($this->pdo);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec('DROP TABLE IF EXISTS ' . $this->table);
    }

    public function testInsertAndSelectOne(): void
    {
        $this->sqlQueryBuilder->insert($this->table, ['team' => ':white'])->setParameters([':white' => 'white'])->getQuery();
        $result = $this->sqlQueryBuilder->select($this->table)->getQuery()->findOne();

        $this->assertIsArray($result);
        $this->assertNotEquals([], $result);
        $this->assertEquals('white', $result['team']);
    }

    public function testInsertAndSelectMany(): void
    {
        $this->sqlQueryBuilder->insert($this->table, ['team' => ':white', 'figure' => ':checker'])
            ->setParameters([':white' => 'white', ':checker' => 'checker'])
            ->getQuery();

        $this->sqlQueryBuilder->insert($this->table, ['team' => ':black', 'figure' => ':checker'])
            ->setParameters([':black' => 'black', ':checker' => 'checker'])
            ->getQuery();

        $result = $this->sqlQueryBuilder->select($this->table, ['team'])->getQuery()->findAll();

        $this->assertEquals(['white', 'black'], array_column($result, 'team'));
    }

    public function testUpdateAndWhere(): void
    {
        $this->sqlQueryBuilder->insert($this->table, ['team' => ':black', 'figure' => ':checker'])
            ->setParameters([':black' => 'black', ':checker' => 'checker'])
            ->getQuery();

        $this->sqlQueryBuilder->update($this->table, ['team' => ':white'])->where('id', '1')
            ->setParameters([':white' => 'white'])
            ->getQuery();

        $result = $this->sqlQueryBuilder->select($this->table, ['team'])->where('id', '1')
            ->getQuery()
            ->findOne();

        $this->assertArrayHasKey('team', $result);
        $this->assertEquals('white', $result['team']);
    }

    public function testOffset(): void
    {
        $this->sqlQueryBuilder->insert($this->table, ['team' => ':black', 'figure' => ':checker'])
            ->setParameters([':black' => 'black', ':checker' => 'checker'])
            ->getQuery();

        $this->sqlQueryBuilder->insert($this->table, ['team' => ':black', 'figure' => ':checker'])
            ->setParameters([':black' => 'black', ':checker' => 'checker'])
            ->getQuery();

        $this->sqlQueryBuilder->insert($this->table, ['team' => ':black', 'figure' => ':checker'])
            ->setParameters([':black' => 'white', ':checker' => 'checker'])
            ->getQuery();

        $this->sqlQueryBuilder->insert($this->table, ['team' => ':black', 'figure' => ':checker'])
            ->setParameters([':black' => 'white', ':checker' => 'checker'])
            ->getQuery();

        $result = $this->sqlQueryBuilder->select($this->table)->limit(2, 2)->getQuery()->findAll();

        $this->assertIsArray($result);
        $this->assertCount(2, array_column($result, 'team'));
        $this->assertEquals(3, array_column($result, 'id')[0]);
    }

}