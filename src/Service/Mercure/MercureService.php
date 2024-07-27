<?php

declare(strict_types=1);

namespace App\Service\Mercure;

use App\Entity\GameLaunch;
use App\Service\Monolog\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final class MercureService
{
    public function publishData(
        GameLaunch             $gameLaunch,
        EntityManagerInterface $entityManager,
        HubInterface           $hub,
    ): void {
        $loggerService = new LoggerService();

        $update = new Update(
            '/chat',
            json_encode([
                'table' => $gameLaunch->getTableData(),
                'log' => $loggerService->getLastLogs($entityManager, $gameLaunch->getRoomId()),
            ])
        );

        $hub->publish($update);
    }
}
