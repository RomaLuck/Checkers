<?php

namespace App\Db;

use PDO;

final class CheckerObjectRepository
{
    private const DESK_SIZE_START = 1;
    private const DESK_SIZE_END = 8;
    private const START_PIECES_WHITE = ['a1', 'a3', 'b2', 'c1', 'c3', 'd2', 'e1', 'e3', 'f2', 'g1', 'g3', 'h2'];
    private const START_PIECES_BLACK = ['a7', 'b8', 'b6', 'c7', 'd8', 'd6', 'e7', 'f8', 'f6', 'g7', 'h8', 'h6'];
    private const HORIZONTAL_SIDE_OF_DESK = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
    private const VERTICAL_SIDE_OF_DESK = [1, 2, 3, 4, 5, 6, 7, 8];
    private string $tableName;
    private SqlQueryBuilder $sqlQueryBuilder;

    public function __construct(PDO $pdo)
    {
        $this->sqlQueryBuilder = new SqlQueryBuilder($pdo);
    }

    public function fillTheTable(): void
    {
        $this->fillTableByPieces();
        $this->fillByWhite();
        $this->fillByBlack();
    }

    public function clearTable(): void
    {
        $this->sqlQueryBuilder->deleteAll($this->getTableName());
    }

    private function fillTableByPieces(): void
    {
        for ($i = self::DESK_SIZE_START; $i <= self::DESK_SIZE_END; $i++) {
            $this->sqlQueryBuilder->insert($this->getTableName(), ['cell' => ':a', 'team' => "''", 'figure' => "''"])->setParameters([':a' => 'a' . $i])->getQuery();
            $this->sqlQueryBuilder->insert($this->getTableName(), ['cell' => ':b', 'team' => "''", 'figure' => "''"])->setParameters([':b' => 'b' . $i])->getQuery();
            $this->sqlQueryBuilder->insert($this->getTableName(), ['cell' => ':c', 'team' => "''", 'figure' => "''"])->setParameters([':c' => 'c' . $i])->getQuery();
            $this->sqlQueryBuilder->insert($this->getTableName(), ['cell' => ':d', 'team' => "''", 'figure' => "''"])->setParameters([':d' => 'd' . $i])->getQuery();
            $this->sqlQueryBuilder->insert($this->getTableName(), ['cell' => ':e', 'team' => "''", 'figure' => "''"])->setParameters([':e' => 'e' . $i])->getQuery();
            $this->sqlQueryBuilder->insert($this->getTableName(), ['cell' => ':f', 'team' => "''", 'figure' => "''"])->setParameters([':f' => 'f' . $i])->getQuery();
            $this->sqlQueryBuilder->insert($this->getTableName(), ['cell' => ':g', 'team' => "''", 'figure' => "''"])->setParameters([':g' => 'g' . $i])->getQuery();
            $this->sqlQueryBuilder->insert($this->getTableName(), ['cell' => ':h', 'team' => "''", 'figure' => "''"])->setParameters([':h' => 'h' . $i])->getQuery();
        }
    }

    private function fillByWhite(): void
    {
        foreach (self::START_PIECES_WHITE as $i) {
            $this->sqlQueryBuilder->update($this->getTableName(), ['team' => ':w', 'figure' => ':c'])
                ->where('cell', "'$i'")
                ->setParameters([':w' => 'white', ':c' => 'checker'])
                ->getQuery();
        }
    }

    private function fillByBlack(): void
    {
        foreach (self::START_PIECES_BLACK as $i) {
            $this->sqlQueryBuilder->update($this->getTableName(), ['team' => ':w', 'figure' => ':c'])
                ->where('cell', "'$i'")
                ->setParameters([':w' => 'black', ':c' => 'checker'])
                ->getQuery();
        }
    }

    public function attackOppositePlayer($stepFrom, $stepTo, $playerColor, $playerFigure): void
    {
        $futurePositionAfterBeat = $this->getFuturePositionAfterBeat($stepFrom, $stepTo);
        if ($futurePositionAfterBeat === '') {
            throw new \RuntimeException('Future position is out of the board');
        }
        $this->sqlQueryBuilder->update($this->getTableName(), ['team' => ':t', 'figure' => ':f'])
            ->setParameters([':t' => $playerColor, ':f' => $playerFigure])
            ->where('cell', ':i')
            ->setParameters([':i' => $futurePositionAfterBeat])
            ->getQuery();

        $this->sqlQueryBuilder->update($this->getTableName(), ['team' => ':t', 'figure' => ':f'])
            ->setParameters([':t' => '', ':f' => ''])
            ->where('cell', ':i')
            ->setParameters([':i' => $stepFrom])
            ->getQuery();

        $this->sqlQueryBuilder->update($this->getTableName(), ['team' => ':t', 'figure' => ':f'])
            ->setParameters([':t' => '', ':f' => ''])
            ->where('cell', ':i')
            ->setParameters([':i' => $stepTo])
            ->getQuery();
    }

    public function walk($stepFrom, $stepTo, $playerColor, $playerFigure): void
    {
        $this->sqlQueryBuilder->update($this->getTableName(), ['team' => ':t', 'figure' => ':f'])
            ->setParameters([':t' => $playerColor, ':f' => $playerFigure])
            ->where('cell', ':c')
            ->setParameters([':c' => $stepTo])
            ->getQuery();

        $this->sqlQueryBuilder->update($this->getTableName(), ['team' => ':t', 'figure' => ':f'])
            ->setParameters([':t' => '', ':f' => ''])
            ->where('cell', ':c')
            ->setParameters([':c' => $stepFrom])
            ->getQuery();
    }

    public function isCheckerInTeam($stepFrom, $playerColor): bool
    {
        $team = $this->sqlQueryBuilder->select($this->getTableName(), ['cell'])
            ->where('team', ':t')
            ->setParameters([':t' => $playerColor])
            ->getQuery()
            ->find();

        return in_array($stepFrom, $team);
    }

    public function isCellAfterAttackAvailable(string $stepFrom, string $stepTo): bool
    {
        $futurePositionAfterBeat = $this->getFuturePositionAfterBeat($stepFrom, $stepTo);
        return $this->findOneBy(['cell' => $futurePositionAfterBeat])->getTeam() === '';

    }

    public function isCheckerInDesk($stepFrom, $stepTo): bool
    {
        return in_array($stepFrom, $this->showAllItems(['cell']))
            && in_array($stepTo, $this->showAllItems(['cell']));
    }

    public function isStepForMove($stepTo): bool
    {
        return $this->findOneBy(['cell' => $stepTo])->getTeam() === '';
    }

    public function isStepForAttack(string $stepTo, string $oppositePlayer): bool
    {
        return $this->findOneBy(['cell' => $stepTo])->getTeam() === $oppositePlayer;
    }

    public function isStepAfterAttackOnDesk(string $stepFrom, string $stepTo): bool
    {
        return in_array($this->getFuturePositionAfterBeat($stepFrom, $stepTo), $this->showAllItems(['cell']));
    }

    public function getFuturePositionAfterBeat(string $stepFrom, string $stepTo): string
    {
        $keyHSide = ($this->getPositionOnDesk($stepTo, self::HORIZONTAL_SIDE_OF_DESK)
                - $this->getPositionOnDesk($stepFrom, self::HORIZONTAL_SIDE_OF_DESK)) * 2
            + $this->getPositionOnDesk($stepFrom, self::HORIZONTAL_SIDE_OF_DESK);

        $keyVSide = ($this->getPositionOnDesk($stepTo, self::VERTICAL_SIDE_OF_DESK)
                - $this->getPositionOnDesk($stepFrom, self::VERTICAL_SIDE_OF_DESK)) * 2
            + $this->getPositionOnDesk($stepFrom, self::VERTICAL_SIDE_OF_DESK);

        if ($keyVSide > 7 || $keyHSide > 7 || $keyVSide < 0 || $keyHSide < 0) {
            return '';
        }

        $horizontalSide = self::HORIZONTAL_SIDE_OF_DESK[$keyHSide];

        $verticalSide = self::VERTICAL_SIDE_OF_DESK[$keyVSide];

        return $horizontalSide . $verticalSide;
    }

    public function getPositionOnDesk(string $cell, array $sideOfDesk): int
    {
        if (!in_array($cell, $this->showAllItems(['cell']))) {
            throw new \RuntimeException('Your checker is out of the board');
        }
        $idPiece = match ($sideOfDesk) {
            self::VERTICAL_SIDE_OF_DESK => 1,
            self::HORIZONTAL_SIDE_OF_DESK => 0,
            default => throw new \RuntimeException('Wrong number piece\'s position'),
        };
        return array_search($this->getSplitCell($cell, $idPiece), $sideOfDesk);
    }

    public function getAreaForWalk(string $stepFrom, int $moveOpportunity): array
    {
        $right = self::HORIZONTAL_SIDE_OF_DESK[$this->getPositionOnDesk(
            $stepFrom,
            self::HORIZONTAL_SIDE_OF_DESK
        ) + $moveOpportunity];

        $left = self::HORIZONTAL_SIDE_OF_DESK[$this->getPositionOnDesk(
            $stepFrom,
            self::HORIZONTAL_SIDE_OF_DESK
        ) - $moveOpportunity];

        $forward = self::VERTICAL_SIDE_OF_DESK[$this->getPositionOnDesk(
            $stepFrom,
            self::VERTICAL_SIDE_OF_DESK
        ) + $moveOpportunity];

        $back = self::VERTICAL_SIDE_OF_DESK[$this->getPositionOnDesk(
            $stepFrom,
            self::VERTICAL_SIDE_OF_DESK
        ) - $moveOpportunity];

        return array_intersect([$right . $forward, $left . $forward, $right . $back, $left . $back],
            $this->showAllItems(['cell']));
    }

    public function showAllItems(array $items = []): array
    {
        return $this->sqlQueryBuilder->select($this->getTableName(), $items)->getQuery()->find();
    }

    public function getSplitCell(string $data, int $key): string
    {
        return (str_split($data))[$key];
    }

    public function setTableName($tableName): void
    {
        $this->tableName = $tableName;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function findOneBy(array $parameters): CheckerObject
    {
        $tableName = $this->sqlQueryBuilder->select($this->getTableName());
        foreach ($parameters as $key => $value) {
            $str = ':' . $key[0];
            $tableName->where($key, $str)->setParameters([$str => $value]);
        }
        return $tableName->getQuery()->findOne();
    }

    public function findBy(array $parameters): CheckerObject
    {
        $tableName = $this->sqlQueryBuilder->select($this->getTableName());
        foreach ($parameters as $key => $value) {
            $str = ':' . $key[0];
            $tableName->where($key, $str)->setParameters([$str => $value]);
        }
        return $tableName->getQuery()->findAll();
    }
}
