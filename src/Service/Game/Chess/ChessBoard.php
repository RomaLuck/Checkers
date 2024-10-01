<?php

declare(strict_types=1);

namespace App\Service\Game\Chess;

use App\Service\Game\BoardAbstract;

class ChessBoard extends BoardAbstract
{
    public const START_DESK = [
        [4, 1, 0, 0, 0, 0, 7, 10],
        [3, 1, 0, 0, 0, 0, 7, 9],
        [2, 1, 0, 0, 0, 0, 7, 8],
        [5, 1, 0, 0, 0, 0, 7, 11],
        [6, 1, 0, 0, 0, 0, 7, 12],
        [2, 1, 0, 0, 0, 0, 7, 8],
        [3, 1, 0, 0, 0, 0, 7, 9],
        [4, 1, 0, 0, 0, 0, 7, 10],
    ];
}
