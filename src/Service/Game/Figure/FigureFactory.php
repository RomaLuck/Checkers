<?php

declare(strict_types=1);

namespace App\Service\Game\Figure;

final class FigureFactory
{
    public function __construct(private int $teamNumber) {}

    public function create(): FigureInterface
    {
        if (Checker::isChecker($this->teamNumber)) {
            return new Checker();
        }
        if (King::isKing($this->teamNumber)) {
            return new King();
        }
        throw new \RuntimeException('Figure is not selected');
    }
}
