<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DatabaseTest extends KernelTestCase
{
    public function testConnection(): void
    {
        $kernel = self::bootKernel();

        $entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        try {
            $db = $entityManager->getConnection();
            $db->executeQuery('SELECT 1');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail("Can not connect to: {$e->getMessage()}");
        }
    }
}
