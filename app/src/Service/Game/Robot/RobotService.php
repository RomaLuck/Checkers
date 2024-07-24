<?php

declare(strict_types=1);

namespace App\Service\Game\Robot;

use App\Entity\GameLaunch;
use App\Entity\User;
use App\Service\Game\Game;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final class RobotService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function assignComputerPlayerToGame(GameLaunch $gameLaunch): void
    {
        $computer = $this->entityManager->getRepository(User::class)->findOneByRole('ROLE_COMPUTER');
        if ($computer) {
            $gameLaunch->getWhiteTeamUser()
                ? $gameLaunch->setBlackTeamUser($computer)
                : $gameLaunch->setWhiteTeamUser($computer);

            $this->entityManager->flush();
        }
    }

    public function updateDesk(
        Game            $game,
        array           $desk,
        LoggerInterface $logger
    ): array
    {
        $computer = $this->entityManager->getRepository(User::class)->findOneByRole('ROLE_COMPUTER');

        $white = $game->getWhite();
        $black = $game->getBlack();

        $computerTeam = $computer->getId() === $white->getId() ? $white : $black;
        $opponent = $computerTeam === $white ? $black : $white;

        $robot = new Robot($game, $computerTeam, $opponent, 1);

        return $robot->run($desk, $logger);
    }
}
