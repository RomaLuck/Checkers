<?php

namespace App\Service\Game\Strategy;

use App\Service\Cache\UserCacheService;
use App\Service\Game\GameStrategyIds;
use App\Service\Game\Robot\RobotService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class GameStrategyFactory
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserCacheService       $userCacheService,
        private RobotService           $robotService,
        private MessageBusInterface    $bus,
    )
    {
    }

    public function create(int $strategyId): StrategyInterface
    {
        return match ($strategyId) {
            GameStrategyIds::COMPUTER => new RobotStrategy(
                $this->entityManager,
                $this->userCacheService,
                $this->robotService,
                $this->bus,
            ),
            GameStrategyIds::MULTIPLAYER => new PlayerStrategy(
                $this->entityManager,
                $this->userCacheService,
            )
        };
    }
}