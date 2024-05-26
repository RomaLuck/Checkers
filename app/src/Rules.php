<?php

namespace Src;

use Psr\Log\LoggerInterface;
use Src\Team\TeamPlayerInterface;

class Rules
{
    private array $desk;
    private array $from;
    private array $to;
    private LoggerInterface $logger;
    private TeamPlayerInterface $player;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function check(array $from, array $to): bool
    {
        $this->from = $from;
        $this->to = $to;

        return $this->isAvailableCell()
            && $this->isTrueDirection()
            && $this->isOpportunityForMove();
    }

    public function setDesk(array $desk): void
    {
        $this->desk = $desk;
    }

    public function setPlayer(TeamPlayerInterface $player): void
    {
        $this->player = $player;
    }

    private function isAvailableCell(): bool
    {
        $to = $this->to;
        if ($this->desk[$to[0]][$to[1]] === 0) {
            return true;
        }

        $this->logger->error('Cell is not available');
        return false;
    }

    private function isTrueDirection(): bool
    {
        $playerDirection = $this->player->getDirection();
        $playerFigureDirections = $this->player->getFigure()->getAvailableDirections();
        $availableDirections = array_map(function ($playerFigureDirection) use ($playerDirection) {
            return $playerFigureDirection * $playerDirection;
        }, $playerFigureDirections);
        if (in_array($this->defineDirection(), $availableDirections)) {
            return true;
        }

        $this->logger->error('The direction is wrong');
        return false;
    }

    private function isOpportunityForMove(): bool
    {
        $step = $this->defineStep() * $this->defineDirection();
        if ($step <= $this->player->getFigure()->getStepOpportunityForMove()) {
            return true;
        }

        $this->logger->error('You do not have ability to reach this cell');
        return false;
    }

    private function defineDirection(): int
    {
        $step = $this->defineStep();
        if ($step > 0) {
            return 1;
        }

        return -1;
    }

    private function defineStep(): int
    {
        return $this->to[1] - $this->from[1];
    }
}