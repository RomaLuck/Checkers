<?php

namespace Src\Figure;

class Checker implements FigureInterface
{
    public const CHECKER_NUMBERS = [1, 2];
    public const CHECKER_DIRECTIONS = [1];
    public const CHECKER_STEP_MOVE = 1;
    public const CHECKER_STEP_BEAT = 2;

    public function getAvailableCommandNumbers(): array
    {
        return self::CHECKER_NUMBERS;
    }

    public function getAvailableDirections(): array
    {
        return self::CHECKER_DIRECTIONS;
    }

    public function getStepOpportunityForMove(): int
    {
        return self::CHECKER_STEP_MOVE;
    }

    public function getStepOpportunityForAttack(): int
    {
        return self::CHECKER_STEP_BEAT;
    }
}