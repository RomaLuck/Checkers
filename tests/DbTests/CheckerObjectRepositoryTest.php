<?php

namespace App\Tests\DbTests;

use App\Db\CheckerObjectRepository;
use App\Db\SqlQueryBuilder;
use function PHPUnit\Framework\assertEquals;

class CheckerObjectRepositoryTest extends DbTestAbstract
{
    protected SqlQueryBuilder $sqlQueryBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sqlQueryBuilder = new SqlQueryBuilder($this->pdo);
        $this->checkerObject = new CheckerObjectRepository($this->pdo);
        $this->checkerObject->setTableName($this->table);
        $this->checkerObject->fillTheTable();
    }

    public function testBoard(): void
    {
        $black = $this->sqlQueryBuilder->select($this->table, ['team'])
            ->where('cell', ':c')
            ->setParameters([':c' => 'h6'])
            ->getQuery()
            ->findOne();

        $white = $this->sqlQueryBuilder->select($this->table, ['team'])
            ->where('cell', ':c')
            ->setParameters([':c' => 'd2'])
            ->getQuery()
            ->findOne();

        self::assertEquals('black', $black);
        self::assertEquals('white', $white);
    }

    public function testSplitCell(): void
    {
        $result = $this->checkerObject->getSplitCell('a1', 1);
        self::assertEquals('1', $result);
    }

    public function testShowAllItems(): void
    {
        $result = $this->checkerObject->showAllItems();
        assertEquals(['id' => 1, 'cell' => 'a1', 'team' => 'white', 'figure' => 'checker'], $result[0]);
    }

    public function testGetAreaForWalk(): void
    {
        try {
            $result = $this->checkerObject->getAreaForWalk('c3', 1);
            self::assertEquals(['d4', 'b4', 'd2', 'b2'], $result);
        } catch (\RuntimeException $exception) {
            self::fail($exception->getMessage());
        }
    }
}