<?php

declare(strict_types=1);

namespace Src\Helpers;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LoggerFactory
{
    private static string $textFormat = "[%channel%] [%level_name%] [%datetime%]: %message% \n";
    private static string $dateFormat = 'Y-m-d H:i:s';
    public static string $logFile = __DIR__ . '/../../logs/app.log';

    public static function getLogger($loggerName): LoggerInterface
    {
        $logger = new Logger($loggerName);

        $fileHandler = new RotatingFileHandler(self::$logFile, 7, Level::Debug);
        $fileHandler->setFormatter(new LineFormatter(self::$textFormat, self::$dateFormat));
        $logger->pushHandler($fileHandler);

        return $logger;
    }
}