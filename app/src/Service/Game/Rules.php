<?php

declare(strict_types=1);

namespace App\Service\Game;

use App\Service\Game\Team\Black;
use App\Service\Game\Team\PlayerInterface;
use App\Service\Game\Team\White;
use Psr\Log\LoggerInterface;

final class Rules
{
    /**
     * @var array<array>
     */
    private array $desk;
    /**
     * @var array<int>
     */
    private array $from;
    /**
     * @var array<int>
     */
    private array $to;
    private LoggerInterface $logger;
    private PlayerInterface $player;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param array<int> $from
     * @param array<int> $to
     */
    public function checkForMove(array $from, array $to): bool
    {
        $this->from = $from;
        $this->to = $to;

        return $this->isAvailableCell()
            && $this->isTrueDirection()
            && $this->isOpportunityForMove();
    }

    /**
     * @param array<int> $from
     * @param array<int> $to
     */
    public function checkForBeat(array $from, array $to): bool
    {
        $this->from = $from;
        $this->to = $to;

        return $this->isAvailableCell()
            && $this->isOpportunityForBeat();
    }

    /**
     * @param array<array> $desk
     */
    public function setDesk(array $desk): void
    {
        $this->desk = $desk;
    }

    public function setPlayer(PlayerInterface $player): void
    {
        $this->player = $player;
    }

    /**
     * @param array<int> $from
     * @param array<int> $to
     */
    public function findFiguresForBeat(array $from, array $to): array
    {
        $figuresCells = [];
        $letters = [$from[0], $to[0]];
        $numbers = [$from[1], $to[1]];

        for ($i = min($letters) + 1; $i < max($letters); $i++) {
            for ($j = min($numbers) + 1; $j < max($numbers); $j++) {
                if ($this->desk[$i][$j] > 0) {
                    $figuresCells[] = [$i, $j];
                }
            }
        }

        return $figuresCells;
    }

    private function isAvailableCell(): bool
    {
        $to = $this->to;
        if ($this->desk[$to[0]][$to[1]] === 0) {
            return true;
        }

        $this->logger->warning('Cell is not available');
        return false;
    }

    private function isTrueDirection(): bool
    {
        $playerDirection = $this->player->getDirection();
        $playerFigureDirections = $this->player->getFigure()->getAvailableDirections();
        $availableDirections = array_map(static function ($playerFigureDirection) use ($playerDirection) {
            return $playerFigureDirection * $playerDirection;
        }, $playerFigureDirections);
        if (in_array($this->defineDirection(), $availableDirections)) {
            return true;
        }

        $this->logger->warning('The direction is wrong');
        return false;
    }

    private function isOpportunityForMove(): bool
    {
        $step = $this->defineStep() * $this->defineDirection();
        if ($step <= $this->player->getFigure()->getStepOpportunityForMove()) {
            return true;
        }

        $this->logger->warning('You do not have ability to reach this cell');
        return false;
    }

    private function isOpportunityForBeat(): bool
    {
        $step = $this->defineStep() * $this->defineDirection();
        if ($step <= $this->player->getFigure()->getStepOpportunityForAttack()) {
            return true;
        }

        $this->logger->warning('You do not have an ability to reach this cell');
        return false;
    }

    private function defineDirection(): int
    {
        $step = $this->defineStep();
        if ($step > 0) {
            return White::DIRECTION_WHITE;
        }

        return Black::DIRECTION_BLACK;
    }

    private function defineStep(): int
    {
        return $this->to[1] - $this->from[1];
    }
}
