<?php

namespace App\Service\Game\Chess\MoveStrategy;

abstract class MoveStrategyAbstract implements MoveStrategyInterface
{
    abstract protected function getPossibleDirections();

    /**
     * @param int[] $from
     *
     * @return int[][]
     */
    public function getPossibleMoves(array $from): array
    {
        $possibleSteps = [];
        for ($i = 1; $i <= 8; $i++) {
            foreach ($this->getPossibleDirections() as $direction) {
                $dirX = $from[0] + $direction[0] * $i;
                $dirY = $from[1] + $direction[1] * $i;
                $possibleSteps[] = [$dirX, $dirY];
            }
        }

        return $possibleSteps;
    }
}
