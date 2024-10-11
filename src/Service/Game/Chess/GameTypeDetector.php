<?php

namespace App\Service\Game\Chess;

use App\Service\Game\Checkers\CheckersGame;
use App\Service\Game\GameTypeIds;
use App\Service\Game\GameTypeInterface;

class GameTypeDetector
{
    public function __construct(private $white, private $black)
    {
    }

    public function detect(int $gameTypeId): GameTypeInterface
    {
        return match ($gameTypeId) {
            GameTypeIds::CHECKERS_TYPE => new CheckersGame($this->white, $this->black),
            GameTypeIds::CHESS_TYPE => new ChessGame($this->white, $this->black)
        };
    }
}