<?php

declare(strict_types=1);

namespace App\Service\Monolog;

final class LoggerService
{
    public function __construct(private RedisHandler $handler)
    {
    }

    /**
     * @return array<string>
     */
    public function getLastLogs(string $roomId): array
    {
        return $this->handler->getLastLogs($roomId);
    }
}
