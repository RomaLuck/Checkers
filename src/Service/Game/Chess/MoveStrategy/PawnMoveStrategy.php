<?php

declare(strict_types=1);

namespace App\Service\Game\Chess\MoveStrategy;

class PawnMoveStrategy extends MoveStrategyAbstract
{
    /**
     * @return int[][]
     */
    protected function getPossibleDirections(): array
    {
        return [
            [0, -1],
            [0, 1],
        ];
    }
}
