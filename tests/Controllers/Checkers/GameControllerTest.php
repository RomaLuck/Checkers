<?php

declare(strict_types=1);

namespace App\Tests\Controllers\Checkers;

use App\Entity\GameLaunch;
use App\Entity\User;
use App\Service\Game\Checkers\CheckerDesk;
use App\Service\Game\GameStrategyIds;
use App\Service\Game\GameTypeIds;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class GameControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private ?ObjectManager $entityManager;

    private UserInterface $user;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setUsername('john.doe@example.com');
        $user->setPassword('12345678');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $this->user = $user;
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testIndex(): void
    {
        $gameList = $this->entityManager->getRepository(GameLaunch::class)->findBy(['is_active' => true]);
        $this->assertEquals([], $gameList);

        $game = new GameLaunch();
        $game->setStrategyId(GameStrategyIds::MULTIPLAYER);
        $game->setTypeId(GameTypeIds::CHECKERS_TYPE);
        $game->setTableData(CheckerDesk::START_DESK);
        $game->setIsActive(true);

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        $gameList = $this->entityManager->getRepository(GameLaunch::class)->findBy(['is_active' => true]);
        $this->assertCount(1, $gameList);
    }

    public function testCreate(): void
    {
        $this->client->request('POST', '/create', ['player' => 'test']);
        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects('/');
    }

    public function testJoin(): void
    {
        $game = new GameLaunch();
        $game->setStrategyId(GameStrategyIds::MULTIPLAYER);
        $game->setTypeId(GameTypeIds::CHECKERS_TYPE);
        $game->setTableData(CheckerDesk::START_DESK);
        $game->setIsActive(true);
        $game->setWhiteTeamUser($this->user);

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        $roomId = $game->getRoomId();

        $this->client->request('POST', '/join', [
            'player' => 'white',
            'room' => $roomId,
        ]);

        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects('/checkers/room/' . $roomId);
    }

    public function testUpdate(): void
    {
        $blackUser = new User();
        $blackUser->setUsername('jane.doe@example.com');
        $blackUser->setPassword('12345678');

        $this->entityManager->persist($blackUser);
        $this->entityManager->flush();

        $game = new GameLaunch();
        $game->setStrategyId(GameStrategyIds::MULTIPLAYER);
        $game->setTypeId(GameTypeIds::CHECKERS_TYPE);
        $game->setTableData(CheckerDesk::START_DESK);
        $game->setIsActive(true);
        $game->setWhiteTeamUser($this->user);
        $game->setBlackTeamUser($blackUser);

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        $this->client->request('GET', '/checkers/room/' . $game->getRoomId());
        $this->client->getRequest()->getSession()->set('room', $game->getRoomId());

        $this->client->xmlHttpRequest('POST', '/checkers/update', [
            'formData' => json_encode([
                'form1' => 'a3',
                'form2' => 'b4',
            ]),
        ]);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertIsString($this->client->getResponse()->getContent());
    }
}
