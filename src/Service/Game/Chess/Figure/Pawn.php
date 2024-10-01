<?php

namespace App\Service\Game\Chess\Figure;

use App\Service\Game\Chess\MoveStrategy\MoveStrategyInterface;
use App\Service\Game\Chess\MoveStrategy\PawnMoveStrategy;
use App\Service\Game\Chess\Rule\IsOpportunityForMoveRule;
use App\Service\Game\Chess\Rule\IsTrueDirectionRule;
use App\Service\Game\Chess\Rule\RuleInterface;

class Pawn implements FigureInterface
{
    public const STEP = 1;

    public function getId(): array
    {
        return FigureIds::PAWN;
    }

    /**
     * @return RuleInterface[]
     */
    public function getFigureRules(): array
    {
        return [
            new IsOpportunityForMoveRule(self::STEP),
            new IsTrueDirectionRule(),
        ];
    }

    /**
     * @return MoveStrategyInterface[]
     */
    public function getMoveStrategies(): array
    {
        return [
            new PawnMoveStrategy()
        ];
    }
}