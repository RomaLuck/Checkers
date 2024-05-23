<?php

namespace Src\Teams;

class Black implements TeamPlayer
{
    public const BLACK = 2;
    public const DIRECTION_WHITE = 1;

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
        return self::BLACK;
    }
}