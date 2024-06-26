<?php

declare(strict_types=1);

namespace App\Service\Monolog;

use App\Entity\Log;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\LogRecord;

class DatabaseHandler extends AbstractProcessingHandler
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function write(LogRecord $record): void
    {
        $log = new Log();
        $log->setChannel($record['channel']);
        $log->setLevel(Logger::toMonologLevel($record['level'])->getName());
        $log->setMessage($record['message']);
        $log->setTime(DateTime::createFromImmutable($record['datetime']));

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}