<?php

namespace App\Service\Game\Chess\Figure;

use App\Service\Game\Chess\MoveStrategy\BishopMoveStrategy;
use App\Service\Game\Chess\Rule\IsClearWayRule;

class Bishop implements FigureInterface
{

    public function getId(): array
    {
        return FigureIds::BISHOP;
    }

    public function getFigureRules(): array
    {
        return [
            new IsClearWayRule()
        ];
    }

    public function getMoveStrategies(): array
    {
        return [
          new BishopMoveStrategy()
        ];
    }
}