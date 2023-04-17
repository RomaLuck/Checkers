<?php

namespace CheckersOOP\src;

class BlackTeam extends CheckerObject
{
    public int $moveOpportunity = 1;
    public int $moveDirection = -1;
    public string $side = 'black';
    public string $oppositeSide = 'white';
    public string $teamName;

    public function __construct($checkerDesk, $teamName)
    {
        parent::__construct($checkerDesk);
        $this->teamName = $teamName;
    }
}