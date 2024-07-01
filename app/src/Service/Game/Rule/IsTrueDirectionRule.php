<?php

declare(strict_types=1);

namespace App\Service\Game\Rule;

use App\Service\Game\Team\Black;
use App\Service\Game\Team\PlayerInterface;
use App\Service\Game\Team\White;

class IsTrueDirectionRule implements RuleInterface
{

    public function check(PlayerInterface $player, array $from, array $to): bool
    {
        $playerDirection = $player->getDirection();
        $playerFigureDirections = $player->getFigure()->getAvailableDirections();
        $availableDirections = array_map(static function ($playerFigureDirection) use ($playerDirection) {
            return $playerFigureDirection * $playerDirection;
        }, $playerFigureDirections);

        return in_array($this->defineDirection($from, $to), $availableDirections);
    }

    private function defineDirection(array $from, array $to): int
    {
        $step = $this->defineStep($from, $to);
        if ($step > 0) {
            return White::DIRECTION_WHITE;
        }

        return Black::DIRECTION_BLACK;
    }

    private function defineStep(array $from, array $to): int
    {
        return $to[1] - $from[1];
    }

    public function getMessage(): string
    {
        return 'The direction is wrong';
    }
}