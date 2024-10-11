<?php

declare(strict_types=1);

namespace App\Service\Game;

use App\Service\Cache\UserCacheService;
use App\Service\Game\Checkers\Strategy\CheckersPlayerStrategy;
use App\Service\Game\Checkers\Strategy\CheckersRobotStrategy;
use App\Service\Game\Chess\Strategy\ChessPlayerStrategy;
use App\Service\Game\Chess\Strategy\ChessRobotStrategy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class GameStrategyFactory
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserCacheService       $userCacheService,
        private MessageBusInterface    $bus,
    )
    {
    }

    public function create(int $strategyId, int $gameTypeId): StrategyInterface
    {
        if ($gameTypeId === GameTypeIds::CHECKERS_TYPE) {
            return match ($strategyId) {
                GameStrategyIds::COMPUTER => new CheckersRobotStrategy(
                    $this->entityManager,
                    $this->userCacheService,
                    $this->bus,
                ),
                GameStrategyIds::MULTIPLAYER => new CheckersPlayerStrategy(
                    $this->entityManager,
                    $this->userCacheService,
                )
            };
        } else {
            return match ($strategyId) {
                GameStrategyIds::COMPUTER => new ChessRobotStrategy(
                    $this->entityManager,
                    $this->userCacheService,
                    $this->bus,
                ),
                GameStrategyIds::MULTIPLAYER => new ChessPlayerStrategy(
                    $this->entityManager,
                    $this->userCacheService,
                )
            };
        }
    }
}
