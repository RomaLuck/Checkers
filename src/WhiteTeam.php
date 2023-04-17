<?php

namespace CheckersOOP\src;

class WhiteTeam extends Player
{

    public int $moveDirection = 1;
    public string $color = 'white';
    public string $oppositeSide = 'black';
    public string $teamName;

    public function __construct($teamName)
    {
        $this->teamName = $teamName;
    }
}
