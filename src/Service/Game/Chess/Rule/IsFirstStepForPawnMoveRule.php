<?php

namespace App\Service\Game\Chess\Rule;

use App\Service\Game\BoardAbstract;
use App\Service\Game\Chess\Figure\FigureIds;
use App\Service\Game\Chess\Team\Black;
use App\Service\Game\Chess\Team\TeamInterface;
use App\Service\Game\Chess\Team\White;
use App\Service\Game\Move;

class IsFirstStepForPawnMoveRule implements RuleInterface
{
    public function check(TeamInterface $team, Move $move, BoardAbstract $board): bool
    {
        if ($team->getFigure()->getId() !== FigureIds::PAWN) {
            return false;
        }

        return $team->getTeamNumbers() === White::WHITE_NUMBERS && $move->getFrom()[1] === 1
            || $team->getTeamNumbers() === Black::BLACK_NUMBERS && $move->getFrom()[1] === 6;
    }

    public function getMessage(): string
    {
        return 'You do not have ability to reach this cell';
    }
}
