<?php

namespace CheckersOOP\src;

class BlackTeam extends CheckerObject
{
    public $moveOpportunity = 1;
    public $moveDirection = -1;
    public $side = 'black';
    public $oppositeSide = 'white';
    public $teamName;

    public function __construct($checkerDesk, $teamName)
    {
        parent::__construct($checkerDesk);
        $this->teamName = $teamName;
    }
}