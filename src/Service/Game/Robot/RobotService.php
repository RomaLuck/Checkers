<?php

declare(strict_types=1);

namespace App\Service\Game\Robot;

use App\Service\Game\Game;
use App\Service\Game\MoveResult;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class RobotService
{
    public function updateDesk(
        Game            $game,
        UserInterface   $computer,
        MoveResult      $moveResult,
        LoggerInterface $logger
    ): MoveResult
    {
        $white = $game->getWhite();
        $black = $game->getBlack();

        $computerTeam = $computer->getId() === $white->getId() ? $white : $black;
        $opponent = $computerTeam === $white ? $black : $white;

        $robot = new Robot($game, $computerTeam, $opponent);

        return $robot->run($moveResult, $logger);
    }
}
