<?php

namespace App\Service\Game\Chess\Team;

class Black extends Team
{
    public const DIRECTION_BLACK = -1;

    public const BLACK_NUMBERS = [7, 8, 9, 10, 11, 12];

    public function getTeamNumbers(): array
    {
        return self::BLACK_NUMBERS;
    }

    public function getDirection(): int
    {
        return self::DIRECTION_BLACK;
    }

    public function isTurnForTeam(bool $turn): bool
    {
        return $turn === false;
    }
}
