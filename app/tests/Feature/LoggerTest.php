<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Entity\Log;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LoggerTest extends KernelTestCase
{
    public function testLogger(): void
    {
        $kernel = self::bootKernel();
        $handler = $kernel->getContainer()->get('app.monolog.database.handler');
        $logger = new Logger('test', [$handler]);
        $logger->info('Test log message');

        $entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $lastLog = $entityManager->getRepository(Log::class)->findOneBy(['channel' => 'test'], ['id' => 'DESC']);
        $this->assertSame('Test log message', $lastLog->getMessage());
    }
}
