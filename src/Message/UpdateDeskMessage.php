<?php

namespace App\Message;

use App\Service\Game\Game;
use App\Service\Game\MoveResult;
use Symfony\Component\Security\Core\User\UserInterface;

class UpdateDeskMessage
{
    public function __construct(
        private UserInterface $computer,
        private Game          $game,
        private MoveResult    $moveResult,
        private string        $roomId,
    )
    {
    }

    public function getComputer(): UserInterface
    {
        return $this->computer;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function getMoveResult(): MoveResult
    {
        return $this->moveResult;
    }

    public function getRoomId(): string
    {
        return $this->roomId;
    }
}