<?php

declare(strict_types=1);

namespace App\Service\Game\Chess\MoveStrategy;

interface MoveStrategyInterface
{
    /**
     * @param int[] $from
     *
     * @return int[][]
     */
    public function getPossibleMoves(array $from): array;
}
