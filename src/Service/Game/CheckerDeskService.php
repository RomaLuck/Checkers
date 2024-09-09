<?php

declare(strict_types=1);

namespace App\Service\Game;

use App\Service\Game\Checkers\Figure\Checker;
use App\Service\Game\Checkers\Figure\King;
use App\Service\Game\Team\Black;
use App\Service\Game\Team\PlayerInterface;
use App\Service\Game\Team\White;

final class CheckerDeskService
{
    /**
     * @param array<int> $cellFrom
     */
    public function getSelectedTeamNumber(array $deskData, array $cellFrom): int
    {
        return $deskData[$cellFrom[0]][$cellFrom[1]];
    }

    public function updateData(
        array $desk,
        Move $move,
    ): array {
        $selectedTeamNumber = $this->getSelectedTeamNumber($desk, $move->getFrom());
        $updatedDesk = $this->updateDesk($desk, $move, $selectedTeamNumber);

        return $this->updateFigures($updatedDesk);
    }

    /**
     * @param array<array<int>> $figuresForBeat
     */
    public function clearCells(array $deskData, array $figuresForBeat): array
    {
        foreach ($figuresForBeat as $cell) {
            $deskData[$cell[0]][$cell[1]] = 0;
        }

        return $deskData;
    }

    public function updateDesk(array $deskData, Move $move, int $selectedTeamNumber): array
    {
        $deskData[$move->getFrom()[0]][$move->getFrom()[1]] = 0;
        $deskData[$move->getTo()[0]][$move->getTo()[1]] = $selectedTeamNumber;

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
     * @param array<array<int>> $deskData
     */
    public function findFiguresForBeat(PlayerInterface $player, array $deskData, Move $move): array
    {
        $from = $move->getFrom();
        $to = $move->getTo();

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
