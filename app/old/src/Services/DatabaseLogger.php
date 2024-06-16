<?php

namespace Src\Services;

use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;

class DatabaseLogger
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function log($level, $message, array $context = []): void
    {
        // Here you can create a new Log entity, set its properties and persist it
        $log = new Log();
        $log->setLevel($level);
        $log->setMessage($message);
        $log->setChannel($context);

        $this->em->persist($log);
        $this->em->flush();
    }
}