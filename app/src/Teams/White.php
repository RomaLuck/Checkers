<?php

namespace Src\Teams;

class White implements TeamPlayer
{
    public const WHITE_CHECKER = 1;
    public const DIRECTION_WHITE = -1;

    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTeamNumber(): int
    {
        return self::WHITE_CHECKER;
    }
}