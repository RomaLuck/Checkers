<?php

declare(strict_types=1);

namespace App\Service\Game\Checkers\Figure;

final class FigureFactory
{
    public static function create(int $teamNumber): FigureInterface
    {
        if (Checker::isChecker($teamNumber)) {
            return new Checker();
        }
        if (King::isKing($teamNumber)) {
            return new King();
        }
        throw new \RuntimeException('Figure is not selected');
    }
}
