<?php

namespace App\Tests\Unit\Chess\Figures;

use App\Service\Game\Chess\Figure\FigureInterface;
use App\Service\Game\Chess\Figure\Knight;

class KnightMoveValidatorTest extends MoveValidationTestAbstract
{
    protected function getFigure(): FigureInterface
    {
        return new Knight();
    }

    protected function getStartDesk(): array
    {
        return [
            [4, 1, 0, 0, 0, 0, 7, 10],
            [3, 1, 0, 0, 0, 0, 7, 9],
            [2, 1, 0, 0, 3, 0, 7, 8],
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
            [true, [2, 4], [0, 3]],
            [true, [2, 4], [3, 2]],
            [true, [2, 4], [4, 5]],
            [true, [2, 4], [1, 6]],
            [true, [1, 0], [0, 2]],
            [false, [1, 0], [1, 2]],
        ];
    }
}