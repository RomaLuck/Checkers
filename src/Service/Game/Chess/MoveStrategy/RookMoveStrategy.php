<?php

namespace App\Service\Game\Chess\MoveStrategy;

class RookMoveStrategy extends MoveStrategyAbstract
{

    /**
     * @return int[][]
     */
    protected function getPossibleDirections(): array
    {
        return [
            [1, 0],
            [-1, 0],
            [0, -1],
            [0, 1],
        ];
    }
}