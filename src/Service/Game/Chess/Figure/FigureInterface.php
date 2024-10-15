<?php

declare(strict_types=1);

namespace App\Service\Game\Chess\Figure;

use App\Service\Game\Chess\MoveStrategy\MoveStrategyInterface;
use App\Service\Game\Chess\Rule\RuleInterface;

interface FigureInterface
{
    public function getName(): string;

    /**
     * @return int[]
     */
    public function getId(): array;

    /**
     * @return RuleInterface[]
     */
    public function getFigureRules(): array;

    /**
     * @return MoveStrategyInterface[]
     */
    public function getMoveStrategies(): array;
}
