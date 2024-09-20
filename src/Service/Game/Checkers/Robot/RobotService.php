<?php

declare(strict_types=1);

namespace App\Service\Game\Checkers\Robot;

use App\Service\Game\Checkers\CheckersGame;
use App\Service\Game\MoveResult;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class RobotService
{
    public function updateDesk(
        CheckersGame    $game,
        UserInterface   $computer,
        MoveResult      $moveResult,
        LoggerInterface $logger,
        ?int            $complexity
    ): MoveResult {
        $white = $game->getWhite();
        $black = $game->getBlack();

        $computerTeam = $computer->getId() === $white->getId() ? $white : $black;
        $opponent = $computerTeam === $white ? $black : $white;

        $robot = new Robot($game, $computerTeam, $opponent);
        if ($complexity !== null) {
            $robot->setMaxDepth($complexity);
        }

        return $robot->run($moveResult, $logger);
    }
}
