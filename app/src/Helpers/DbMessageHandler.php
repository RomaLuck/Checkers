<?php

namespace Src\Helpers;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Src\Entity\Log;

class DbMessageHandler extends AbstractProcessingHandler
{
    private EntityManagerInterface $entityManager;

    public function __construct(int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        $this->entityManager = EntityManagerFactory::create();
        parent::__construct($level, $bubble);
    }

    protected function write(LogRecord $record): void
    {
        $log = new Log();
        $log->setChannel($record['channel']);
        $log->setLevel($record['level']);
        $log->setMessage($record['message']);
        $log->setTime(DateTime::createFromImmutable($record['datetime']));

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}