<?php

namespace CheckersOOP\src;

class BlackTeam extends Player
{
    public int $moveDirection = -1;
    public string $color = 'black';
    public string $oppositeSide = 'white';
    public string $teamName;

    public function __construct($teamName)
    {
        $this->teamName = $teamName;
    }
}