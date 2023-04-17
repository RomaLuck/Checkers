<?php

namespace CheckersOOP\src;

class WhiteTeam extends CheckerObject
{
    public $moveOpportunity = 1;
    public $moveDirection = 1;
    public $side = 'white';
    public $oppositeSide = 'black';
    public $teamName;

    public function __construct($checkerDesk, $teamName)
    {
        parent::__construct($checkerDesk);
        $this->teamName = $teamName;
    }
}
