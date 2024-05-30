<?php

declare(strict_types=1);

namespace Src\Figure;

final class King implements FigureInterface
{
    public const KING_NUMBERS = [3, 4];
    public const KING_DIRECTIONS = [1, -1];
    public const KING_STEP_MOVE = 8;
    public const KING_STEP_BEAT = 8;

    public function getAvailableCommandNumbers(): array
    {
        return self::KING_NUMBERS;
    }

    public function getAvailableDirections(): array
    {
        return self::KING_DIRECTIONS;
    }

    public function getStepOpportunityForMove(): int
    {
        return self::KING_STEP_MOVE;
    }

    public function getStepOpportunityForAttack(): int
    {
        return self::KING_STEP_BEAT;
    }
}