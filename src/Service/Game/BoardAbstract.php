<?php

namespace App\Service\Game;

abstract class BoardAbstract
{
    /**
     * @var array<array<int>>
     */
    private array $boardData;

    public function __construct(array $board)
    {
        $this->boardData = $board;
    }

    public function getBoardData(): array
    {
        return $this->boardData;
    }

    /**
     * @param array<int> $cell
     */
    public function setFigureNumber(array $cell, int $figureNumber): void
    {
        $this->boardData[$cell[0]][$cell[1]] = $figureNumber;
    }

    /**
     * @param array<int> $cell
     */
    public function getFigureNumber(array $cell): ?int
    {
        return $this->boardData[$cell[0]][$cell[1]] ?? null;
    }

    public function update(Move $move): self
    {
        $from = $move->getFrom();
        $to = $move->getTo();

        $teamNumber = $this->getFigureNumber($from);

        $this->setFigureNumber($from, 0);
        $this->setFigureNumber($to, $teamNumber);

        return $this;
    }

    public function getEmptyCells(): array
    {
        $emptyCells = [];
        foreach ($this->boardData as $rowKey => $row) {
            foreach ($row as $key => $cell) {
                if ($cell === 0) {
                    $emptyCells[] = [$rowKey, $key];
                }
            }
        }

        return $emptyCells;
    }

    /**
     * @param array<int> $figureNumbers
     */
    public function countFigures(array $figureNumbers): int
    {
        $count = 0;

        foreach ($this->boardData as $row) {
            foreach ($row as $cell) {
                if (in_array($cell, $figureNumbers, true)) {
                    $count++;
                }
            }
        }

        return $count;
    }
}
