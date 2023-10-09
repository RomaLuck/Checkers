<?php

namespace App\GameCore;

class BlackTeam extends Player
{
    public int $moveDirection = Player::DIRECTION_DOWN;
    public string $color = Player::BLACK;
    public string $oppositeSide = Player::WHITE;
    public string $teamName;

    public function __construct($teamName)
    {
        $this->teamName = $teamName;
    }
}