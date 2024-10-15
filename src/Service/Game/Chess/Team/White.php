<?php

declare(strict_types=1);

namespace App\Service\Game\Chess\Team;

class White extends Team
{
    public const DIRECTION_WHITE = 1;

    public const WHITE_NUMBERS = [1, 2, 3, 4, 5, 6];

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
