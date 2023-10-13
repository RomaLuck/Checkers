<?php

namespace App\GameCore;

class BlackTeam extends Player
{
    public function __construct(string $teamName)
    {
        parent::__construct($teamName);
        $this->moveDirection = Player::DIRECTION_DOWN;
        $this->color = Player::BLACK;
        $this->oppositeSideColor = Player::WHITE;
    }
}