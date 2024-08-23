<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\Mercure\MercureService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mercure\HubInterface;

final class UserJoinedGameEventListener
{
    public function __construct(
        private LoggerInterface $logger,
        private MercureService $mercureService,
        private HubInterface $hub
    ) {}

    #[AsEventListener(event: 'JoinedGameEvent')]
    public function onJoinedGameEvent($event): void
    {
        $gameLaunch = $event->getGameLaunch();
        $username = $event->getUsername();

        $logger = $this->logger->withName($gameLaunch->getRoomId());
        $logger->info("User {$username} has joined the room");

        $this->mercureService->publishData($gameLaunch, $this->hub);
    }
}
