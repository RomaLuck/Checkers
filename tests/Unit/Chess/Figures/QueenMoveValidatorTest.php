<?php

namespace App\Tests\Unit\Chess\Figures;

use App\Service\Game\Chess\Figure\FigureInterface;
use App\Service\Game\Chess\Figure\Queen;

class QueenMoveValidatorTest extends MoveValidationTestAbstract
{
    protected function getStartDesk(): array
    {
        return [
            [4, 1, 0, 0, 0, 0, 7, 10],
            [3, 1, 0, 0, 0, 0, 7, 9],
            [2, 1, 0, 5, 0, 0, 7, 8],
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
            [true, [2, 3], [1, 2]],
            [true, [2, 3], [1, 4]],
            [true, [2, 3], [3, 4]],
            [true, [2, 3], [3, 2]],
            [true, [2, 3], [4, 5]],
            [true, [2, 3], [5, 6]],
            [true, [2, 3], [1, 3]],
            [true, [2, 3], [4, 3]],
            [true, [2, 3], [2, 2]],
            [true, [2, 3], [2, 4]],
            [false, [2, 3], [0, 1]],
            [false, [3, 0], [3, 2]],
        ];
    }

    protected function getFigure(): FigureInterface
    {
        return new Queen();
    }
}