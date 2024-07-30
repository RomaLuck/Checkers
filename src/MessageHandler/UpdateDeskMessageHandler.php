<?php

namespace App\MessageHandler;

use App\Entity\GameLaunch;
use App\Message\UpdateDeskMessage;
use App\Service\Game\GameStrategyIds;
use App\Service\Game\Robot\RobotService;
use App\Service\Mercure\MercureService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateDeskMessageHandler
{
    public function __construct(
        private RobotService           $robotService,
        private EntityManagerInterface $entityManager,
        private HubInterface           $hub,
        private LoggerInterface        $logger
    )
    {
    }

    public function __invoke(UpdateDeskMessage $message): void
    {
        $roomId = $message->getRoomId();
        $logger = $this->logger->withName($roomId);

        $updatedDesk = $this->robotService->updateDesk(
            $message->getGame(),
            $message->getComputer(),
            $message->getUpdatedDesk(),
            $logger
        );

        $gameLaunch = $this->entityManager->getRepository(GameLaunch::class)->findOneBy(['room_id' => $roomId]);
        if ($gameLaunch) {
            $gameLaunch->setTableData($updatedDesk);
            $this->entityManager->flush();
            (new MercureService())->publishData($gameLaunch, $this->entityManager, $this->hub);
        }
    }
}