<?php

declare(strict_types=1);

namespace App\Service\Game\Chess\Rule;

use App\Service\Game\BoardAbstract;
use App\Service\Game\Chess\Team\TeamInterface;
use App\Service\Game\Move;

class IsTrueDirectionRule implements RuleInterface
{
    public function check(TeamInterface $team, Move $move, BoardAbstract $board): bool
    {
        $step = abs($move->getTo()[1] - $move->getFrom()[1]);
        $direction = ($move->getTo()[1] - $move->getFrom()[1]) / $step;

        return $direction === $team->getDirection();
    }

    public function getMessage(): string
    {
        return 'The direction is wrong';
    }
}
