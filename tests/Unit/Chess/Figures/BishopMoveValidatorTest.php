<?php

namespace App\Tests\Unit\Chess\Figures;

use App\Service\Game\Chess\Figure\Bishop;
use App\Service\Game\Chess\Figure\FigureInterface;

class BishopMoveValidatorTest extends MoveValidationTestAbstract
{
    protected function getFigure(): FigureInterface
    {
        return new Bishop();
    }

    protected function getStartDesk(): array
    {
        return [
            [4, 1, 0, 0, 0, 0, 7, 10],
            [3, 1, 0, 0, 0, 0, 7, 9],
            [2, 1, 0, 2, 0, 0, 7, 8],
            [5, 1, 0, 0, 0, 0, 7, 11],
            [6, 1, 0, 0, 0, 0, 7, 12],
            [2, 1, 0, 0, 0, 0, 7, 8],
            [3, 1, 0, 0, 0, 0, 7, 9],
            [4, 1, 0, 0, 0, 0, 7, 10],
        ];
    }

    protected static function getStepDataProvider(): array
    {
        return [
            [true, [2, 3], [1, 2]],
            [true, [2, 3], [1, 4]],
            [true, [2, 3], [3, 4]],
            [true, [2, 3], [3, 2]],
            [true, [2, 3], [4, 5]],
            [true, [2, 3], [5, 6]],
            [false, [2, 3], [1, 3]],
            [false, [2, 3], [0, 1]],
            [false, [2, 0], [0, 2]],
        ];
    }
}