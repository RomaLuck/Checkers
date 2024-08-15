<?php

declare(strict_types=1);

namespace App\Service\Game\Rule;

use App\Service\Game\Move;
use App\Service\Game\Team\PlayerInterface;

final class IsTrueDirectionRule implements RuleInterface
{
    public function check(PlayerInterface $player, Move $move): bool
    {
        $from = $move->getFrom();
        $to = $move->getTo();
        if ($this->defineStep($from, $to) === 0) {
            return false;
        }

        $playerDirection = $player->getDirection();
        $figure = $player->getFigure();
        $playerFigureDirections = $figure->getAvailableDirections();
        $availableDirections = array_map(static function ($playerFigureDirection) use ($playerDirection) {
            return $playerFigureDirection * $playerDirection;
        }, $playerFigureDirections);

        $direction = $this->defineDirection($from, $to);

        return in_array($direction, $availableDirections);
    }

    public function getMessage(): string
    {
        return 'The direction is wrong';
    }

    private function defineDirection(array $from, array $to): int
    {
        return ($to[1] - $from[1]) / abs($to[1] - $from[1]);
    }

    private function defineStep(array $from, array $to): int
    {
        return abs($to[1] - $from[1]);
    }
}
