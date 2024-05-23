<?php

namespace Src\Helpers;

use Monolog\Handler\RotatingFileHandler;
use Psr\Log\LoggerInterface;

class LogReader
{
    public static function read(LoggerInterface $logger, int $linesNum): array
    {
        $handlers = $logger->getHandlers();

        $logFileName = null;
        foreach ($handlers as $handler) {
            if ($handler instanceof RotatingFileHandler) {
                $logFileName = $handler->getUrl();
                break;
            }
        }

        if ($logFileName === null) {
            return [];
        }

        $logLines = file($logFileName);

        return array_slice($logLines, -1 * $linesNum);
    }
}