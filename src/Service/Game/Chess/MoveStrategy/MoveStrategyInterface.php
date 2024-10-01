<?php

namespace App\Service\Game\Chess\MoveStrategy;

interface MoveStrategyInterface
{
    /**
     * @param int[] $from
     * @return int[][]
     */
    public function getPossibleMoves(array $from): array;
}