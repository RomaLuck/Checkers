<?php

namespace CheckersOOP\src;

class WhiteTeam extends CheckerObject
{
    public int $moveOpportunity = 1;
    public int $moveDirection = 1;
    public string $side = 'white';
    public string $oppositeSide = 'black';
    public string $teamName;

    public function __construct($checkerDesk, $teamName)
    {
        parent::__construct($checkerDesk);
        $this->teamName = $teamName;
    }
}
