<?php

namespace App\Message;

use App\Service\Game\Game;

class UpdateDeskMessage
{
    public function __construct(
        private int             $strategyId,
        private Game            $game,
        private array           $updatedDesk,
        private string          $roomId,
    )
    {
    }

    public function getStrategyId(): int
    {
        return $this->strategyId;
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