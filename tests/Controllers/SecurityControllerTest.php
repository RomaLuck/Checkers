<?php

declare(strict_types=1);

namespace App\Tests\Controllers;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testAuth(): void
    {
        $client = self::createClient();
        $client->request('GET', '/');
        self::assertResponseRedirects('/login');

        $client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        $entityManager = self::getContainer()
            ->get('doctrine')
            ->getManager();

        $user = new User();
        $user->setUsername('john.doe@example.com');
        $user->setPassword('12345678');

        $entityManager->persist($user);
        $entityManager->flush();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'john.doe@example.com']);
        $client->loginUser($testUser);
        self::assertResponseIsSuccessful();
    }
}
