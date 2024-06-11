<?php

declare(strict_types=1);

namespace Src\Helpers;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

final class LoggerFactory
{
    public static function getLogger($loggerName): LoggerInterface
    {
        $textFormat = "[%channel%] [%level_name%] [%datetime%]: %message%\n";
        $dateFormat = "Y-m-d H:i:s";

        $logger = new Logger($loggerName);

        $consoleHandler = new StreamHandler('php://stdout', Level::Debug);
        $consoleHandler->setFormatter(new LineFormatter($textFormat, $dateFormat));
        $logger->pushHandler($consoleHandler);

        $dbHandler = new DbMessageHandler();
        $logger->pushHandler($dbHandler);

        return $logger;
    }
}
