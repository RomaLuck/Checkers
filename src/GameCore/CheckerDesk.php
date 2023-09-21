<?php

namespace App\GameCore;

use App\Db\SqlQueryBuilder;

final class CheckerDesk
{
    private const DESK_SIZE_START = 1;
    private const DESK_SIZE_END = 8;
    private const START_PIECES_WHITE = ['a1', 'a3', 'b2', 'c1', 'c3', 'd2', 'e1', 'e3', 'f2', 'g1', 'g3', 'h2'];
    private const START_PIECES_BLACK = ['a7', 'b8', 'b6', 'c7', 'd8', 'd6', 'e7', 'f8', 'f6', 'g7', 'h8', 'h6'];
    private SqlQueryBuilder $queryBuilder;

    public function __construct(SqlQueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function fillTheTable(string $table): void
    {
        $this->fillTableByPieces($table);
        $this->fillByWhite($table);
        $this->fillByBlack($table);
    }

    public function clearTable(string $table): void
    {
        $this->queryBuilder->deleteAll($table);
    }

    public function fillTableByPieces(string $table): void
    {
        for ($i = self::DESK_SIZE_START; $i <= self::DESK_SIZE_END; $i++) {
            $this->queryBuilder->insert($table, ['id' => ':a', 'team' => "''", 'figure' => "''"])->setParameters([':a' => 'a' . $i])->getQuery();
            $this->queryBuilder->insert($table, ['id' => ':b', 'team' => "''", 'figure' => "''"])->setParameters([':b' => 'b' . $i])->getQuery();
            $this->queryBuilder->insert($table, ['id' => ':c', 'team' => "''", 'figure' => "''"])->setParameters([':c' => 'c' . $i])->getQuery();
            $this->queryBuilder->insert($table, ['id' => ':d', 'team' => "''", 'figure' => "''"])->setParameters([':d' => 'd' . $i])->getQuery();
            $this->queryBuilder->insert($table, ['id' => ':e', 'team' => "''", 'figure' => "''"])->setParameters([':e' => 'e' . $i])->getQuery();
            $this->queryBuilder->insert($table, ['id' => ':f', 'team' => "''", 'figure' => "''"])->setParameters([':f' => 'f' . $i])->getQuery();
            $this->queryBuilder->insert($table, ['id' => ':g', 'team' => "''", 'figure' => "''"])->setParameters([':g' => 'g' . $i])->getQuery();
            $this->queryBuilder->insert($table, ['id' => ':h', 'team' => "''", 'figure' => "''"])->setParameters([':h' => 'h' . $i])->getQuery();
        }
    }

    public function fillByWhite(string $table): void
    {
        foreach (self::START_PIECES_WHITE as $i) {
            $this->queryBuilder->update($table, ['team' => ':w', 'figure' => ':c'])
                ->where('id', "'$i'")
                ->setParameters([':w' => 'white', ':c' => 'checker'])
                ->getQuery();
        }
    }

    public function fillByBlack(string $table): void
    {
        foreach (self::START_PIECES_BLACK as $i) {
            $this->queryBuilder->update($table, ['team' => ':w', 'figure' => ':c'])
                ->where('id', "'$i'")
                ->setParameters([':w' => 'black', ':c' => 'checker'])
                ->getQuery();
        }
    }
}
