<?php

namespace App\Service\Game\Chess\Rule;

use App\Service\Game\BoardAbstract;
use App\Service\Game\Chess\Team\TeamInterface;
use App\Service\Game\Move;

class IsClearWayRule implements RuleInterface
{
    public function check(TeamInterface $team, Move $move, BoardAbstract $board): bool
    {
        return count($this->getFiguresOnWay($move, $team, $board)) === 0;
    }

    public function getMessage(): string
    {
        return 'Way is not clear';
    }

    private function getFiguresOnWay(Move $move, TeamInterface $team, BoardAbstract $board): array
    {
        $from = $move->getFrom();
        $to = $move->getTo();
        $board = $board->getBoardData();

        $figuresCells = [];
        $directionX = $to[0] - $from[0] > 0 ? 1 : -1;
        $directionY = $to[1] - $from[1] > 0 ? 1 : -1;
        $currentX = $from[0] + $directionX;
        $currentY = $from[1] + $directionY;

        while ($currentX !== $to[0] && $currentY !== $to[1]) {
            if (isset($board[$currentX][$currentY])
                && $board[$currentX][$currentY] > 0
            ) {
                $figuresCells[] = [$currentX, $currentY];
            }
            $currentX += $directionX;
            $currentY += $directionY;
        }

        return $figuresCells;
    }
}