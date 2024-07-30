<?php

namespace App\Message;

use App\Service\Game\Game;
use Symfony\Component\Security\Core\User\UserInterface;

class UpdateDeskMessage
{
    public function __construct(
        private UserInterface $computer,
        private Game          $game,
        private array         $updatedDesk,
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

    public function getUpdatedDesk(): array
    {
        return $this->updatedDesk;
    }

    public function getRoomId(): string
    {
        return $this->roomId;
    }
}