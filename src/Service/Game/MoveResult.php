<?php

namespace App\Service\Game;

class MoveResult
{
    /**
     * @var array<array>
     */
    private array $checkerDesk;

    private bool $currentTurn;

    private ?int $winnerId = null;

    /**
     * @param array<array> $checkerDesk
     * @param bool $currentTurn
     */
    public function __construct(array $checkerDesk, bool $currentTurn)
    {
        $this->checkerDesk = $checkerDesk;
        $this->currentTurn = $currentTurn;
    }


    /**
     * @return array<array>
     */
    public function getCheckerDesk(): array
    {
        return $this->checkerDesk;
    }

    /**
     * @param array<array> $checkerDesk
     * @return void
     */
    public function setCheckerDesk(array $checkerDesk): void
    {
        $this->checkerDesk = $checkerDesk;
    }

    public function getCurrentTurn(): bool
    {
        return $this->currentTurn;
    }

    public function setCurrentTurn(bool $currentTurn): void
    {
        $this->currentTurn = $currentTurn;
    }

    public function getWinnerId(): ?int
    {
        return $this->winnerId;
    }

    public function setWinnerId(int $winnerId): void
    {
        $this->winnerId = $winnerId;
    }
}
