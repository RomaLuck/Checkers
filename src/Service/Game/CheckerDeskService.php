<?php

declare(strict_types=1);

namespace App\Service\Game;

use App\Service\Game\Figure\Checker;
use App\Service\Game\Figure\King;
use App\Service\Game\Team\Black;
use App\Service\Game\Team\PlayerInterface;
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
     * @param array<int,int> $cellFrom
     * @param array<int,int> $cellTo
     */
    public function updateData(
        array            $desk,
        array            $cellFrom,
        array            $cellTo,
    ): array
    {
        $selectedTeamNumber = $this->getSelectedTeamNumber($desk, $cellFrom);
        $updatedDesk = $this->updateDesk($desk, $cellFrom, $cellTo, $selectedTeamNumber);
        return $this->updateFigures($updatedDesk);
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

    /**
     * @param array<array> $deskData
     * @param array<int,int> $from
     * @param array<int,int> $to
     */
    public function findFiguresForBeat(PlayerInterface $player, array $deskData, array $from, array $to): array
    {
        $figuresCells = [];
        $directionX = $to[0] - $from[0] > 0 ? 1 : -1;
        $directionY = $to[1] - $from[1] > 0 ? 1 : -1;
        $currentX = $from[0] + $directionX;
        $currentY = $from[1] + $directionY;

        while ($currentX !== $to[0] && $currentY !== $to[1]) {
            if (isset($deskData[$currentX][$currentY])
                && $deskData[$currentX][$currentY] > 0
                && !in_array($deskData[$currentX][$currentY], $player->getTeamNumbers())
            ) {
                $figuresCells[] = [$currentX, $currentY];
            }
            $currentX += $directionX;
            $currentY += $directionY;
        }

        return $figuresCells;
    }
}
