<?php

declare(strict_types=1);

namespace App\Service\Game;

interface GameTypeInterface
{
    public function run(MoveResult $currentCondition, Move $move): MoveResult;
}
