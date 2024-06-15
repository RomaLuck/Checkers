<?php

declare(strict_types=1);

namespace Src\Game;

use Src\Game\Figure\Checker;
use Src\Game\Figure\King;
use Src\Game\Team\Black;
use Src\Game\Team\White;

final class CheckerDesk
{
    public const START_DESK = [
        [1, -1, 1, -1, 0, -1, 2, -1],
        [-1, 1, -1, 0, -1, 2, -1, 2],
        [1, -1, 1, -1, 0, -1, 2, -1],
        [-1, 1, -1, 0, -1, 2, -1, 2],
        [1, -1, 1, -1, 0, -1, 2, -1],
        [-1, 1, -1, 0, -1, 2, -1, 2],
        [1, -1, 1, -1, 0, -1, 2, -1],
        [-1, 1, -1, 0, -1, 2, -1, 2],
    ];

    private array $deskData;

    public function __construct(array $deskData)
    {
        $this->deskData = $deskData;
    }

    public function getDeskData(): array
    {
        return $this->deskData;
    }

    /**
     * @param array<int> $cellFrom
     */
    public function getSelectedTeamNumber(array $cellFrom): int
    {
        return $this->deskData[$cellFrom[0]][$cellFrom[1]];
    }

    /**
     * @param array<array> $figuresForBeat
     */
    public function clearCells(array $figuresForBeat): void
    {
        foreach ($figuresForBeat as $cell) {
            $this->deskData[$cell[0]][$cell[1]] = 0;
        }
    }

    /**
     * @param array<int> $cellFrom
     * @param array<int> $cellTo
     */
    public function updateDesk(array $cellFrom, array $cellTo, int $selectedTeamNumber): void
    {
        $this->deskData[$cellFrom[0]][$cellFrom[1]] = 0;
        $this->deskData[$cellTo[0]][$cellTo[1]] = $selectedTeamNumber;
    }

    public function updateFigures(): void
    {
        $whiteChecker = current(array_intersect(White::WHITE_NUMBERS, Checker::CHECKER_NUMBERS));
        $blackChecker = current(array_intersect(Black::BLACK_NUMBERS, Checker::CHECKER_NUMBERS));
        $whiteKing = current(array_intersect(White::WHITE_NUMBERS, King::KING_NUMBERS));
        $blackKing = current(array_intersect(Black::BLACK_NUMBERS, King::KING_NUMBERS));

        foreach ($this->getDeskData() as $rowKey => $row) {
            if ($row[Black::TRANSFORMATION_CELL_BLACK] === $blackChecker) {
                $this->deskData[$rowKey][Black::TRANSFORMATION_CELL_BLACK] = $blackKing;
            }
            if ($row[White::TRANSFORMATION_CELL_WHITE] === $whiteChecker) {
                $this->deskData[$rowKey][White::TRANSFORMATION_CELL_WHITE] = $whiteKing;
            }
        }
    }
}
