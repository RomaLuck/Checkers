<?php

namespace App\Tests\Unit\Chess\Figures;

use App\Service\Game\Chess\ChessBoard;
use App\Service\Game\Chess\Figure\Pawn;

class PawnMoveValidatorTest extends MoveValidationTestAbstract
{
    public const START_DESK = [
        [4, 0, 1, 0, 0, 0, 7, 10],
        [3, 1, 0, 0, 0, 0, 7, 9],
        [2, 1, 0, 0, 0, 0, 7, 8],
        [5, 1, 0, 0, 0, 0, 7, 11],
        [6, 1, 0, 0, 0, 0, 7, 12],
        [2, 1, 0, 0, 0, 0, 7, 8],
        [3, 1, 0, 0, 0, 0, 7, 9],
        [4, 1, 0, 0, 0, 0, 7, 10],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->board = new ChessBoard(self::START_DESK);
        $this->team->setFigure(new Pawn());
    }

    public static function getStepDataProvider(): array
    {
        return [
            [true, [1, 1], [1, 2]],
            [false, [1, 1], [1, 3]],
            [false, [1, 1], [2, 2]],
            [false, [2, 2], [2, 3]],
            [false, [1, 1], [2, 1]],
            [false, [1, 1], [0, 0]],
            [false, [0, 2], [0, 1]],
            [false, [0, 2], [1, 2]],
        ];
    }
}