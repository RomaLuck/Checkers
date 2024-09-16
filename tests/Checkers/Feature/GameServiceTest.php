<?php

namespace App\Tests\Checkers\Feature;

use App\Entity\GameLaunch;
use App\Entity\User;
use App\Service\Game\Checkers\CheckerDesk;
use App\Service\Game\Checkers\GameService;
use App\Service\Game\GameStrategyIds;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class GameServiceTest extends KernelTestCase
{
    private ?EntityManager $entityManager;

    private GameService $gameService;

    private UserInterface $user;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $user = new User();
        $user->setUsername('john.doe@example.com');
        $user->setPassword('12345678');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->user = $user;
        $this->gameService = new GameService($this->entityManager);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testCreateGameLaunch(): void
    {
        $game = $this->gameService->createGameLaunch($this->user, 'white');
        $this->assertNotNull($game->getWhiteTeamUser());
        $this->assertEquals('john.doe@example.com', $game->getWhiteTeamUser()->getUsername());
    }

    public function testJoinToGame(): void
    {
        $game = new GameLaunch();
        $game->setStrategyId(GameStrategyIds::MULTIPLAYER);
        $game->setTableData(CheckerDesk::START_DESK);
        $game->setIsActive(true);

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        $this->assertNull($game->getBlackTeamUser());

        $this->gameService->joinToGame($game, $this->user, 'black');

        $this->assertNotNull($game->getBlackTeamUser());
        $this->assertEquals('john.doe@example.com', $game->getBlackTeamUser()->getUsername());
    }

    public function testGetUserColor(): void
    {
        $game = new GameLaunch();
        $game->setStrategyId(GameStrategyIds::MULTIPLAYER);
        $game->setTableData(CheckerDesk::START_DESK);
        $game->setWhiteTeamUser($this->user);
        $game->setIsActive(true);

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        $color = $this->gameService->getUserColor($game, $this->user);
        $this->assertEquals('white', $color);
    }
}
