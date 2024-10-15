<?php

declare(strict_types=1);

namespace App\Service\Game\Chess\Figure;

use App\Service\Game\Chess\MoveStrategy\BishopMoveStrategy;
use App\Service\Game\Chess\MoveStrategy\RookMoveStrategy;
use App\Service\Game\Chess\Rule\IsOpportunityForMoveRule;

class King implements FigureInterface
{
    public const STEP = 1;

    public function getId(): array
    {
        return FigureIds::KING;
    }

    public function getFigureRules(): array
    {
        return [
            new IsOpportunityForMoveRule(self::STEP),
        ];
    }

    public function getMoveStrategies(): array
    {
        return [
            new BishopMoveStrategy(),
            new RookMoveStrategy(),
        ];
    }

    public function getName(): string
    {
        return FigureNames::KING;
    }
}
