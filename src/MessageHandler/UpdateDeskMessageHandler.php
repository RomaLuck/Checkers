<?php

namespace App\MessageHandler;

use App\Entity\GameLaunch;
use App\Message\UpdateDeskMessage;
use App\Service\Game\GameStrategyIds;
use App\Service\Game\Robot\RobotService;
use App\Service\Mercure\MercureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateDeskMessageHandler
{
    public function __construct(
        private RobotService           $robotService,
        private EntityManagerInterface $entityManager,
        private HubInterface           $hub,
    )
    {
    }

    public function __invoke(UpdateDeskMessage $message): void
    {
        #todo доробити логгер
        if ($message->getStrategyId() === GameStrategyIds::COMPUTER) {
            $updatedDesk = $this->robotService->updateDesk($message->getGame(), $message->getUpdatedDesk());

            $gameLaunch = $this->entityManager->getRepository(GameLaunch::class)->findOneBy(['room_id' => $message->getRoomId()]);
            if ($gameLaunch) {
                $gameLaunch->setTableData($updatedDesk);
                $this->entityManager->flush();
                (new MercureService())->publishData($gameLaunch, $this->entityManager, $this->hub);
            }
        }
    }
}