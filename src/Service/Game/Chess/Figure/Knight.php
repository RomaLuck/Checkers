<?php

declare(strict_types=1);

namespace App\Service\Game\Chess\Figure;

use App\Service\Game\Chess\MoveStrategy\KnightMoveStrategy;

class Knight implements FigureInterface
{
    public function getId(): array
    {
        return FigureIds::KNIGHT;
    }

    public function getFigureRules(): array
    {
        return [];
    }

    public function getMoveStrategies(): array
    {
        return [
            new KnightMoveStrategy(),
        ];
    }
}
