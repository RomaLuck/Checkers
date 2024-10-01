<?php

namespace App\Service\Game\Chess\Team;

class White extends Team
{
    const DIRECTION_WHITE = 1;

    const WHITE_NUMBERS = [1, 2, 3, 4, 5, 6];

    public function getTeamNumbers(): array
    {
        return self::WHITE_NUMBERS;
    }

    public function getDirection(): int
    {
        return self::DIRECTION_WHITE;
    }

    public function isTurnForTeam(bool $turn): bool
    {
        return $turn === true;
    }
}