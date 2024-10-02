<?php

namespace App\Service\Game\Chess\Figure;

use App\Service\Game\Chess\MoveStrategy\RookMoveStrategy;
use App\Service\Game\Chess\Rule\IsClearWayRule;

class Rook implements FigureInterface
{

    public function getId(): array
    {
        return FigureIds::ROOK;
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
            new RookMoveStrategy()
        ];
    }
}