<?php

namespace App\Service\Game\Chess\Figure;

use App\Service\Game\Chess\Rule\PawnRules\IsOpportunityForMoveRule;
use App\Service\Game\Chess\Rule\PawnRules\IsTrueDirectionRule;

class Pawn implements FigureInterface
{
    public const STEP = 1;

    public function getId(): array
    {
        return FigureIds::PAWN;
    }

    public function getFigureRules(): array
    {
        return [
            new IsOpportunityForMoveRule(),
            new IsTrueDirectionRule(),
        ];
    }
}