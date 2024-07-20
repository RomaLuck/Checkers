<?php

declare(strict_types=1);

namespace App\Service\Game;

use App\Service\Game\Figure\Checker;
use App\Service\Game\Figure\King;
use App\Service\Game\Team\Black;
use App\Service\Game\Team\White;

final class CheckerDeskService
{
    /**
     * @param array<int,int> $cellFrom
     */
    public function getSelectedTeamNumber(array $deskData, array $cellFrom): int
    {
        return $deskData[$cellFrom[0]][$cellFrom[1]];
    }

    /**
     * @param array<array> $figuresForBeat
     */
    public function clearCells(array $deskData, array $figuresForBeat): array
    {
        foreach ($figuresForBeat as $cell) {
            $deskData[$cell[0]][$cell[1]] = 0;
        }

        return $deskData;
    }

    /**
     * @param array<int> $cellFrom
     * @param array<int> $cellTo
     */
    public function updateDesk(array $deskData, array $cellFrom, array $cellTo, int $selectedTeamNumber): array
    {
        $deskData[$cellFrom[0]][$cellFrom[1]] = 0;
        $deskData[$cellTo[0]][$cellTo[1]] = $selectedTeamNumber;

        return $deskData;
    }

    public function updateFigures(array $deskData): array
    {
        $whiteChecker = current(array_intersect(White::WHITE_NUMBERS, Checker::CHECKER_NUMBERS));
        $blackChecker = current(array_intersect(Black::BLACK_NUMBERS, Checker::CHECKER_NUMBERS));
        $whiteKing = current(array_intersect(White::WHITE_NUMBERS, King::KING_NUMBERS));
        $blackKing = current(array_intersect(Black::BLACK_NUMBERS, King::KING_NUMBERS));

        $updatedDeskData = $deskData;

        foreach ($deskData as $rowKey => $row) {
            if ($row[Black::TRANSFORMATION_CELL_BLACK] === $blackChecker) {
                $updatedDeskData[$rowKey][Black::TRANSFORMATION_CELL_BLACK] = $blackKing;
            }
            if ($row[White::TRANSFORMATION_CELL_WHITE] === $whiteChecker) {
                $updatedDeskData[$rowKey][White::TRANSFORMATION_CELL_WHITE] = $whiteKing;
            }
        }

        return $updatedDeskData;
    }
}
