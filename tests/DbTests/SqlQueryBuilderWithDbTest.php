<?php

namespace App\Tests\DbTests;

use App\Db\SqlQueryBuilder;

class SqlQueryBuilderWithDbTest extends DbTestAbstract
{
    protected SqlQueryBuilder $sqlQueryBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sqlQueryBuilder = new SqlQueryBuilder($this->pdo);
    }

    public function testInsertAndSelectOne(): void
    {
        $this->sqlQueryBuilder->insert($this->table, ['team' => ':white'])->setParameters([':white' => 'white'])->getQuery();
        $result = $this->sqlQueryBuilder->select($this->table, ['team'])->getQuery()->findOne();

        $this->assertIsString($result);
        $this->assertNotEquals([], $result);
        $this->assertEquals('white', $result);
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

        $this->assertIsArray($result);
        $this->assertEquals(['white', 'black'], $result);
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

        $this->assertEquals('white', $result);
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