<?php

namespace App\Service\Game\Chess\MoveStrategy;

class KnightMoveStrategy extends MoveStrategyAbstract
{

    /**
     * @return int[][]
     */
    protected function getPossibleDirections(): array
    {
        return [
            [2, 1],
            [2, -1],
            [-2, 1],
            [-2, -1],
            [1, 2],
            [1, -2],
            [-1, 2],
            [-1, -2],
        ];
    }

    public function getPossibleMoves(array $from): array
    {
        $possibleSteps = [];
        foreach ($this->getPossibleDirections() as $direction) {
            $dirX = $from[0] + $direction[0];
            $dirY = $from[1] + $direction[1];
            $possibleSteps[] = [$dirX, $dirY];
        }

        return $possibleSteps;
    }
}