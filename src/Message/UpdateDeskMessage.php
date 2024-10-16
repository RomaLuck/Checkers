<?php

declare(strict_types=1);

namespace App\Message;

use App\Service\Game\GameTypeInterface;
use App\Service\Game\MoveResult;
use Symfony\Component\Security\Core\User\UserInterface;

class UpdateDeskMessage
{
    public function __construct(
        private UserInterface $computer,
        private GameTypeInterface $game,
        private MoveResult $moveResult,
        private string $roomId,
        private ?int $complexity,
    ) {
    }

    public function getComputer(): UserInterface
    {
        return $this->computer;
    }

    public function getGame(): GameTypeInterface
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

    public function getComplexity(): ?int
    {
        return $this->complexity;
    }
}
