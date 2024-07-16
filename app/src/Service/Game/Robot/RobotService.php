<?php

declare(strict_types=1);

namespace App\Service\Game\Robot;

use App\Entity\GameLaunch;
use App\Entity\User;
use App\Service\Game\Game;
use App\Service\Game\Team\PlayerInterface;
use Doctrine\ORM\EntityManagerInterface;

class RobotService
{
    private mixed $computer;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->computer = $this->entityManager->getRepository(User::class)->findOneByRole('ROLE_COMPUTER');
    }

    public function assignComputerPlayerToGame(GameLaunch $gameLaunch): void
    {
        if ($this->computer) {
            $gameLaunch->getWhiteTeamUser()
                ? $gameLaunch->setBlackTeamUser($this->computer)
                : $gameLaunch->setWhiteTeamUser($this->computer);

            $this->entityManager->flush();
        }
    }

    public function updateDesk(Game $game, PlayerInterface $white, PlayerInterface $black): array
    {
        $computerTeam = $this->computer->getId() === $white->getId() ? $white : $black;
        $robot = new Robot($game, $computerTeam, 10);

        return $robot->run();
    }
}
