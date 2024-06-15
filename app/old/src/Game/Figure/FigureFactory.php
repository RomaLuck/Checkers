<?php

declare(strict_types=1);

namespace Src\Game\Figure;

use RuntimeException;

class FigureFactory
{
    public function __construct(private int $teamNumber)
    {
    }

    public function create(): FigureInterface
    {
        if (in_array($this->teamNumber, Checker::CHECKER_NUMBERS)) {
            return new Checker();
        }
        if (in_array($this->teamNumber, King::KING_NUMBERS)) {
            return new King();
        }
        throw new RuntimeException('Figure is not selected');
    }
}
