<?php

declare(strict_types=1);

namespace App\Tests\Controllers\Checkers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegistration(): void
    {
        $client = self::createClient();
        $client->request('GET', '/register');
        self::assertResponseIsSuccessful();
        $client->submitForm('Register', [
            'username' => 'john.doe@example.com',
            'password' => '12345678',
        ]);
        self::assertResponseRedirects('/');
    }
}
