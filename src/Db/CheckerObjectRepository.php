<?php

namespace App\Db;

final class CheckerObjectRepository extends SqlQueryBuilder
{
    private const DESK_SIZE_START = 1;
    private const DESK_SIZE_END = 8;
    private const START_PIECES_WHITE = ['a1', 'a3', 'b2', 'c1', 'c3', 'd2', 'e1', 'e3', 'f2', 'g1', 'g3', 'h2'];
    private const START_PIECES_BLACK = ['a7', 'b8', 'b6', 'c7', 'd8', 'd6', 'e7', 'f8', 'f6', 'g7', 'h8', 'h6'];
    public const HORIZONTAL_SIDE_OF_DESK = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
    public const VERTICAL_SIDE_OF_DESK = [1, 2, 3, 4, 5, 6, 7, 8];
    public string $tableName = 'CheckerDesk';

    public function fillTheTable(): void
    {
        $this->fillTableByPieces();
        $this->fillByWhite();
        $this->fillByBlack();
    }

    public function clearTable(): void
    {
        $this->deleteAll($this->tableName);
    }

    public function fillTableByPieces(): void
    {
        for ($i = self::DESK_SIZE_START; $i <= self::DESK_SIZE_END; $i++) {
            $this->insert($this->tableName, ['cell' => ':a', 'team' => "''", 'figure' => "''"])->setParameters([':a' => 'a' . $i])->getQuery();
            $this->insert($this->tableName, ['cell' => ':b', 'team' => "''", 'figure' => "''"])->setParameters([':b' => 'b' . $i])->getQuery();
            $this->insert($this->tableName, ['cell' => ':c', 'team' => "''", 'figure' => "''"])->setParameters([':c' => 'c' . $i])->getQuery();
            $this->insert($this->tableName, ['cell' => ':d', 'team' => "''", 'figure' => "''"])->setParameters([':d' => 'd' . $i])->getQuery();
            $this->insert($this->tableName, ['cell' => ':e', 'team' => "''", 'figure' => "''"])->setParameters([':e' => 'e' . $i])->getQuery();
            $this->insert($this->tableName, ['cell' => ':f', 'team' => "''", 'figure' => "''"])->setParameters([':f' => 'f' . $i])->getQuery();
            $this->insert($this->tableName, ['cell' => ':g', 'team' => "''", 'figure' => "''"])->setParameters([':g' => 'g' . $i])->getQuery();
            $this->insert($this->tableName, ['cell' => ':h', 'team' => "''", 'figure' => "''"])->setParameters([':h' => 'h' . $i])->getQuery();
        }
    }

    public function fillByWhite(): void
    {
        foreach (self::START_PIECES_WHITE as $i) {
            $this->update($this->tableName, ['team' => ':w', 'figure' => ':c'])
                ->where('cell', "'$i'")
                ->setParameters([':w' => 'white', ':c' => 'checker'])
                ->getQuery();
        }
    }

    public function fillByBlack(): void
    {
        foreach (self::START_PIECES_BLACK as $i) {
            $this->update($this->tableName, ['team' => ':w', 'figure' => ':c'])
                ->where('cell', "'$i'")
                ->setParameters([':w' => 'black', ':c' => 'checker'])
                ->getQuery();
        }
    }

    public function attackOppositePlayer($playerColor, $playerFigure, $stepFrom, $stepTo): void
    {
        $futurePositionAfterBeat = $this->getFuturePositionAfterBeat($stepFrom, $stepTo);
        if ($futurePositionAfterBeat === '') {
            throw new \RuntimeException('Future position is out of the board');
        }
        $this->update($this->getTableName(), ['team' => ':t', 'figure' => ':f'])
            ->setParameters([':t' => $playerColor, ':f' => $playerFigure])
            ->where('cell', ':i')
            ->setParameters([':i' => $futurePositionAfterBeat])
            ->getQuery();

        $this->update($this->getTableName(), ['team' => ':t', 'figure' => ':f'])
            ->setParameters([':t' => '', ':f' => ''])
            ->where('cell', ':i')
            ->setParameters([':i' => $stepFrom])
            ->getQuery();

        $this->update($this->getTableName(), ['team' => ':t', 'figure' => ':f'])
            ->setParameters([':t' => '', ':f' => ''])
            ->where('cell', ':i')
            ->setParameters([':i' => $stepTo])
            ->getQuery();
    }

    public function walk($playerColor, $playerFigure, $stepFrom, $stepTo): void
    {
        $this->update($this->getTableName(), ['team' => ':t', 'figure' => ':f'])
            ->setParameters([':t' => $playerColor, ':f' => $playerFigure])
            ->where('cell', ':c')
            ->setParameters([':c' => $stepTo])
            ->getQuery();

        $this->update($this->getTableName(), ['team' => ':t', 'figure' => ':f'])
            ->setParameters([':t' => '', ':f' => ''])
            ->where('cell', ':c')
            ->setParameters([':c' => $stepFrom])
            ->getQuery();
    }

    public function isCheckerInTeam($stepFrom, $playerColor): bool
    {
        $team = $this->select($this->tableName, ['cell'])
            ->where('team', ':t')
            ->setParameters([':t' => $playerColor])
            ->getQuery()
            ->findAll();

        return in_array($stepFrom, $team);
    }

    public function isCellAfterAttackAvailable(string $stepFrom, string $stepTo): bool
    {
        $futurePositionAfterBeat = $this->getFuturePositionAfterBeat($stepFrom, $stepTo);

        return $this->select($this->getTableName(), ['team'])
                ->where('cell', ':i')
                ->setParameters([':i' => $futurePositionAfterBeat])
                ->getQuery()
                ->findOne()
            === '';

    }

    public function isCheckerInDesk($stepFrom, $stepTo): bool
    {
        return in_array($stepFrom, array_column($this->showAllItems(), 'cell'))
            && in_array($stepTo, array_column($this->showAllItems(), 'cell'));
    }

    public function isStepForMove($stepTo): bool
    {
        return $this->select($this->getTableName(), ['team'])
                ->where('cell', ':i')
                ->setParameters([':i' => $stepTo])
                ->getQuery()
                ->findOne()
            === '';
    }

    public function isStepForAttack(string $stepTo, string $oppositePlayer): bool
    {
        return $this->select($this->getTableName(), ['team'])
                ->where('cell', ':i')
                ->setParameters([':i' => $stepTo])
                ->getQuery()
                ->findOne()
            === $oppositePlayer;
    }

    public function isStepAfterAttackOnDesk(string $stepFrom, string $stepTo): bool
    {
        return in_array(
            $this->getFuturePositionAfterBeat($stepFrom, $stepTo),
            array_column($this->showAllItems(), 'cell'),
        );
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
        if (!in_array($cell, array_column($this->showAllItems(), 'cell'))) {
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
            array_column($this->showAllItems(), 'cell'));
    }

    public function showAllItems(): array
    {
        return $this->select($this->getTableName())->getQuery()->findAll();
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
}
