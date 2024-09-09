<?php

namespace App\Service\Game\Chess\Rule\PawnRules;

use App\Service\Game\Chess\Figure\Pawn;
use App\Service\Game\Move;

class IsOpportunityForMoveRule
{
    public function check(Move $move): bool
    {
        $step = abs($move->getTo()[1] - $move->getFrom()[1]);

        return $step === Pawn::STEP;
    }
}