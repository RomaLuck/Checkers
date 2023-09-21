<?php

namespace App\Tests\gameCoreTests;

use App\Db\SqlQueryBuilder;
use App\GameCore\CheckerDesk;
use App\Tests\DbTests\DbTestAbstract;

class CheckerDeskTest extends DbTestAbstract
{
    protected SqlQueryBuilder $sqlQueryBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS ' . $this->table . ' (
            id VARCHAR(10),
            team VARCHAR(100),
            figure VARCHAR(100)
        )');
        $this->sqlQueryBuilder = new SqlQueryBuilder($this->pdo);
        $checkerDesk = new CheckerDesk($this->sqlQueryBuilder);
        $checkerDesk->fillTheTable($this->table);
    }

    public function testBoard(): void
    {
        $result = $this->sqlQueryBuilder->select($this->table, ['team'])->where('id', "'h6'")
            ->getQuery()
            ->findOne();
        self::assertEquals('black', $result['team']);
    }
}