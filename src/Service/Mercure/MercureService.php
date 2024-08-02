<?php

declare(strict_types=1);

namespace App\Service\Mercure;

use App\Entity\GameLaunch;
use App\Service\Monolog\LoggerService;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final class MercureService
{
    public function __construct(private LoggerService $loggerService)
    {
    }

    public function publishData(
        GameLaunch   $gameLaunch,
        HubInterface $hub,
    ): void
    {
        $update = new Update(
            '/chat',
            json_encode([
                'table' => $gameLaunch->getTableData(),
                'log' => $this->loggerService->getLastLogs($gameLaunch->getRoomId()),
            ])
        );

        $hub->publish($update);
    }
}
