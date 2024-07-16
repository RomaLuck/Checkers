<?php

namespace App\Service\Monolog;

use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;

class LoggerService
{
    /**
     * @return array<array>
     */
    public function getLastLogs(EntityManagerInterface $entityManager, mixed $roomId): array
    {
        #todo перевірити чому не працює в prod середовищі
        $logs = $entityManager->getRepository(Log::class)->findBy(
            ['channel' => $roomId],
            orderBy: ['id' => 'DESC'],
            limit: 10
        );

        return array_map(
            fn(Log $log) => [
                'message' => $log->getMessage(),
                'time' => $log->getTime()->format('d/m/y H:i:s')
            ], $logs
        );
    }
}