<?php

namespace App\Tests\Unit\Chess\Figures;

use App\Service\Game\Chess\Figure\FigureInterface;
use App\Service\Game\Chess\Figure\Pawn;

class PawnMoveValidatorTest extends MoveValidationTestAbstract
{

    protected function getFigure(): FigureInterface
    {
        return new Pawn();
    }

    protected function getStartDesk(): array
    {
        return [
            [4, 0, 1, 0, 0, 0, 7, 10],
            [3, 1, 0, 0, 0, 0, 7, 9],
            [2, 1, 0, 0, 0, 0, 7, 8],
            [5, 1, 0, 0, 0, 0, 7, 11],
            [6, 1, 0, 0, 0, 0, 7, 12],
            [2, 1, 0, 0, 0, 0, 7, 8],
            [3, 1, 0, 0, 0, 0, 7, 9],
            [4, 1, 0, 0, 0, 0, 7, 10],
        ];
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