<?php

declare(strict_types=1);

namespace App\Service\Game\Checkers\Strategy;

use App\Service\Cache\UserCacheService;
use App\Service\Game\GameStrategyIds;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class GameStrategyFactory
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserCacheService $userCacheService,
        private MessageBusInterface $bus,
    ) {
    }

    public function create(int $strategyId): StrategyInterface
    {
        return match ($strategyId) {
            GameStrategyIds::COMPUTER => new RobotStrategy(
                $this->entityManager,
                $this->userCacheService,
                $this->bus,
            ),
            GameStrategyIds::MULTIPLAYER => new PlayerStrategy(
                $this->entityManager,
                $this->userCacheService,
            )
        };
    }
}
