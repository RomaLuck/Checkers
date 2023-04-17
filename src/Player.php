<?php

namespace CheckersOOP\src;

class Player
{
    public $moveDirection;
    public $side;
    public $oppositeSide;
    public $teamName;

    public function createWhite()
    {
        return new WhiteTeam();
    }
}