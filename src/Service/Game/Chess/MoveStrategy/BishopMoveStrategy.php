<?php

namespace App\Service\Game\Chess\MoveStrategy;

class BishopMoveStrategy extends MoveStrategyAbstract
{

    /**
     * @return int[][]
     */
    protected function getPossibleDirections():array
    {
        return [
            [1, 1],
            [-1, 1],
            [1, -1],
            [-1, -1],
        ];
    }
}