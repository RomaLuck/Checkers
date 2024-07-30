<?php

declare(strict_types=1);

namespace App\Service\Game\Robot;

use App\Entity\GameLaunch;
use App\Entity\User;
use App\Service\Game\Game;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class RobotService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function assignComputerPlayerToGame(GameLaunch $gameLaunch, UserInterface $computer): void
    {
        $gameLaunch->getWhiteTeamUser()
            ? $gameLaunch->setBlackTeamUser($computer)
            : $gameLaunch->setWhiteTeamUser($computer);

        $this->entityManager->flush();
    }

    public function getComputerPlayer(EntityManagerInterface $entityManager)
    {
        $computer = $entityManager->getRepository(User::class)->findOneByRole('ROLE_COMPUTER');
        if (!$computer) {
            $computer = new User();
            $computer->setUsername('computer');
            $computer->setRoles(['ROLE_COMPUTER']);
            $computer->setPassword('********');

            $entityManager->persist($computer);
            $entityManager->flush();
        }

        return $computer;
    }

    public function updateDesk(
        Game             $game,
        UserInterface    $computer,
        array            $desk,
        LoggerInterface $logger
    ): array
    {
        $white = $game->getWhite();
        $black = $game->getBlack();

        $computerTeam = $computer->getId() === $white->getId() ? $white : $black;
        $opponent = $computerTeam === $white ? $black : $white;

        $robot = new Robot($game, $computerTeam, $opponent);

        return $robot->run($desk, $logger);
    }
}
