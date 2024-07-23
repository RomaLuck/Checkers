<?php

declare(strict_types=1);

namespace App\Service\Game\Robot;

use App\Entity\GameLaunch;
use App\Entity\User;
use App\Service\Game\Game;
use App\Service\Game\Team\PlayerInterface;
use Doctrine\ORM\EntityManagerInterface;

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

    public function updateDesk(Game $game, PlayerInterface $white, PlayerInterface $black, array $desk): array
    {
        $computer = $this->entityManager->getRepository(User::class)->findOneByRole('ROLE_COMPUTER');

        $computerTeam = $computer->getId() === $white->getId() ? $white : $black;
        $opponent = $computerTeam === $white ? $black : $white;

        $robot = new Robot($game, $computerTeam, $opponent, 1);

        return $robot->run($desk);
    }
}
