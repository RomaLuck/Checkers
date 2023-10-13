<?php

namespace App\GameCore;

class WhiteTeam extends Player
{
    public function __construct(string $teamName)
    {
        parent::__construct($teamName);
        $this->moveDirection  = Player::DIRECTION_UP;
        $this->color = Player::WHITE;
        $this->oppositeSideColor = Player::BLACK;
    }
}
