<?php

namespace App\MessageHandler;

use App\Entity\GameLaunch;
use App\Message\UpdateDeskMessage;
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
        private LoggerInterface        $logger,
        private MercureService         $mercureService,
    )
    {
    }

    public function __invoke(UpdateDeskMessage $message): void
    {
        $roomId = $message->getRoomId();
        $logger = $this->logger->withName($roomId);

        $moveResult = $this->robotService->updateDesk(
            $message->getGame(),
            $message->getComputer(),
            $message->getMoveResult(),
            $logger
        );

        $gameLaunch = $this->entityManager->getRepository(GameLaunch::class)->findOneBy(['room_id' => $roomId]);
        if ($gameLaunch) {
            $gameLaunch->setTableData($moveResult->getCheckerDesk());
            $gameLaunch->setCurrentTurn($moveResult->getCurrentTurn());
            $this->entityManager->flush();
            $this->mercureService->publishData($gameLaunch, $this->hub);
        }
    }
}
