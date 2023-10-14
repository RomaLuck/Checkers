<?php

namespace App\Tests\DbTests;

use App\Db\SqlQueryBuilder;

class SqlQueryBuilderWithDbTest extends DbTestAbstract
{
    protected ?SqlQueryBuilder $sqlQueryBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sqlQueryBuilder = new SqlQueryBuilder(self::$pdo);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->sqlQueryBuilder->deleteAll(self::$table);
        $this->sqlQueryBuilder = null;
    }

    public function testInsertAndSelectMany(): void
    {
        $this->sqlQueryBuilder->insert(self::$table, ['team' => ':white', 'figure' => ':checker'])
            ->setParameters([':white' => 'white', ':checker' => 'checker'])
            ->getQuery();

        $this->sqlQueryBuilder->insert(self::$table, ['team' => ':black', 'figure' => ':checker'])
            ->setParameters([':black' => 'black', ':checker' => 'checker'])
            ->getQuery();

        $result = $this->sqlQueryBuilder->select(self::$table, ['team'])->getQuery()->findAll();

        $this->assertIsArray($result);
        $this->assertEquals(['white', 'black'], $result);
    }

    public function testInsertAndSelectOne(): void
    {
        $this->sqlQueryBuilder
            ->insert(self::$table, ['cell' => ':c', 'team' => ':w'])
            ->setParameters([':c' => 'a1', ':w' => 'white'])
            ->getQuery();
        $result = $this->sqlQueryBuilder->select(self::$table)->getQuery()->findOne();

        $this->assertIsString($result->getCell());
        $this->assertNotEquals([], $result->getTeam());
        $this->assertEquals('white', $result->getTeam());
    }

    public function testUpdateAndWhere(): void
    {
        $this->sqlQueryBuilder->insert(self::$table, ['team' => ':black', 'figure' => ':checker'])
            ->setParameters([':black' => 'black', ':checker' => 'checker'])
            ->getQuery();

        $this->sqlQueryBuilder->update(self::$table, ['team' => ':white'])->where('id', '1')
            ->setParameters([':white' => 'white'])
            ->getQuery();

        $result = $this->sqlQueryBuilder->select(self::$table, ['team'])->where('id', '1')
            ->getQuery()
            ->findOne();

        $this->assertEquals('white', $result->getTeam());
    }

    public function testOffset(): void
    {
        $this->sqlQueryBuilder->insert(self::$table, ['team' => ':black', 'figure' => ':checker'])
            ->setParameters([':black' => 'black', ':checker' => 'checker'])
            ->getQuery();

        $this->sqlQueryBuilder->insert(self::$table, ['team' => ':black', 'figure' => ':checker'])
            ->setParameters([':black' => 'black', ':checker' => 'checker'])
            ->getQuery();

        $this->sqlQueryBuilder->insert(self::$table, ['team' => ':black', 'figure' => ':checker'])
            ->setParameters([':black' => 'white', ':checker' => 'checker'])
            ->getQuery();

        $this->sqlQueryBuilder->insert(self::$table, ['team' => ':black', 'figure' => ':checker'])
            ->setParameters([':black' => 'white', ':checker' => 'checker'])
            ->getQuery();

        $result = $this->sqlQueryBuilder->select(self::$table)->limit(2, 2)->getQuery()->findAll();

        $this->assertIsArray($result);
        $this->assertCount(2, array_column($result, 'team'));
        $this->assertEquals(3, array_column($result, 'id')[0]);
    }
}