<?php

namespace App\GameCore;

class WhiteTeam extends Player
{

    public int $moveDirection = Player::DIRECTION_UP;
    public string $color = Player::WHITE;
    public string $oppositeSide = Player::BLACK;
    public string $teamName;

    public function __construct($teamName)
    {
        $this->teamName = $teamName;
    }
}
