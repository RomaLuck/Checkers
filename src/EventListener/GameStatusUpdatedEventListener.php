<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\Mercure\MercureService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mercure\HubInterface;

final class GameStatusUpdatedEventListener
{
    public function __construct(
        private MercureService $mercureService,
        private HubInterface $hub
    ) {
    }

    #[AsEventListener(event: 'GameStatusUpdatedEvent')]
    public function onGameStatusUpdatedEvent($event): void
    {
        $this->mercureService->publishData($event->getGameLaunch(), $this->hub);
    }
}
