<?php

declare(strict_types=1);


namespace App\Service\Game;

class GameStrategyIds
{
    public const COMPUTER = 1;
    public const MULTIPLAYER = 2;

    public static function allStrategyIds(): array
    {
        return [
            self::COMPUTER,
            self::MULTIPLAYER
        ];
    }
}