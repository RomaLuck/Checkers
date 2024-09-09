<?php

namespace App\Service\Game\Chess\Rule\PawnRules;

use App\Service\Game\Move;
use App\Service\Game\Team\PlayerInterface;

class IsTrueDirectionRule
{
    public function check(Move $move, PlayerInterface $player): bool
    {
        $direction = $move->getTo()[1] - $move->getFrom()[1];

        return $direction === $player->getDirection();
    }
}