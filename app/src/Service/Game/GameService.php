<?php

declare(strict_types=1);

namespace App\Service\Game;

use App\Entity\GameLaunch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class GameService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createGameLaunch(UserInterface $user, string $color): GameLaunch
    {
        $game = new GameLaunch();
        if ($color === 'white') {
            $game->setWhiteTeamUser($user);
        } elseif ($color === 'black') {
            $game->setBlackTeamUser($user);
        }

        $game->setTableData(CheckerDesk::START_DESK);
        $game->setIsActive(true);

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return $game;
    }

    public function joinToGame(GameLaunch $game, UserInterface $user, string $color): void
    {
        if ($color === 'white' && !$game->getWhiteTeamUser()) {
            $game->setWhiteTeamUser($user);
        } elseif ($color === 'black' && !$game->getBlackTeamUser()) {
            $game->setBlackTeamUser($user);
        }

        $this->entityManager->flush();
    }

    public function getUserColor(GameLaunch $game, UserInterface $user): ?string
    {
        $whiteTeamUser = $game->getWhiteTeamUser();
        if ($user === $whiteTeamUser) {
            return 'white';
        }
        $blackTeamUser = $game->getBlackTeamUser();
        if ($user === $blackTeamUser) {
            return 'black';
        }

        return null;
    }
}