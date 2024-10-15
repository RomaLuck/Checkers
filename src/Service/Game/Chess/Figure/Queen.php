<?php

declare(strict_types=1);

namespace App\Service\Game\Chess\Figure;

use App\Service\Game\Chess\MoveStrategy\BishopMoveStrategy;
use App\Service\Game\Chess\MoveStrategy\RookMoveStrategy;
use App\Service\Game\Chess\Rule\IsClearWayRule;

class Queen implements FigureInterface
{
    public function getId(): array
    {
        return FigureIds::QUEEN;
    }

    public function getFigureRules(): array
    {
        return [
            new IsClearWayRule(),
        ];
    }

    public function getMoveStrategies(): array
    {
        return [
            new BishopMoveStrategy(),
            new RookMoveStrategy(),
        ];
    }
}
