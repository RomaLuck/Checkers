<?php

declare(strict_types=1);

namespace App\Service\Game\Chess\Figure;

use App\Service\Game\Chess\MoveStrategy\MoveStrategyInterface;
use App\Service\Game\Chess\MoveStrategy\PawnMoveStrategy;
use App\Service\Game\Chess\Rule\IsOpportunityForMoveRule;
use App\Service\Game\Chess\Rule\IsTrueDirectionRule;
use App\Service\Game\Chess\Rule\RuleInterface;

class Pawn implements FigureInterface
{
    private int $step = 1;

    public function getStep(): int
    {
        return $this->step;
    }

    public function setStep(int $step): void
    {
        $this->step = $step;
    }

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
            new IsOpportunityForMoveRule($this->getStep()),
            new IsTrueDirectionRule(),
        ];
    }

    /**
     * @return MoveStrategyInterface[]
     */
    public function getMoveStrategies(): array
    {
        return [
            new PawnMoveStrategy(),
        ];
    }
}
