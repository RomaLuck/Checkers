<?php

declare(strict_types=1);

namespace App\Service\Game\Strategy;

use App\Entity\GameLaunch;
use App\Entity\User;
use App\Message\UpdateDeskMessage;
use App\Service\Cache\UserCacheService;
use App\Service\Game\Game;
use App\Service\Game\MoveResult;
use App\Service\Game\Robot\RobotService;
use App\Service\Game\Team\Black;
use App\Service\Game\Team\White;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

final class RobotStrategy implements StrategyInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserCacheService $userCacheService,
        private RobotService $robotService,
        private MessageBusInterface $bus,
    ) {
    }

    public function run(GameLaunch $gameLaunch, string $roomId, Request $request, LoggerInterface $logger): void
    {
        $startCondition = new MoveResult($gameLaunch->getTableData(), $gameLaunch->getCurrentTurn());

        $computer = $this->entityManager->getRepository(User::class)->getComputerPLayer();

        try {
            $whiteTeamUser = $this->userCacheService->getCachedWhiteTeamUser($gameLaunch, $roomId);
            $blackTeamUser = $this->userCacheService->getCachedBlackTeamUser($gameLaunch, $roomId);
        } catch (\RuntimeException) {
            if ($gameLaunch->assignPlayerToGame($computer)) {
                $logger->info('Computer has joined the room');
                $this->entityManager->flush();
            }

            if (
                $gameLaunch->getWhiteTeamUser() === $computer
                && $gameLaunch->getCurrentTurn() === GameLaunch::WHITE_TURN
            ) {
                $white = new White($computer->getId(), $computer->getUsername());
                $black = new Black(
                    $gameLaunch->getBlackTeamUser()->getId(),
                    $gameLaunch->getBlackTeamUser()->getUsername()
                );

                $moveResult = $this->robotService->updateDesk(
                    new Game($white, $black),
                    $computer,
                    $startCondition,
                    $logger,
                    $gameLaunch->getComplexity()
                );

                $gameLaunch->setCurrentTurn($moveResult->getCurrentTurn());
                $gameLaunch->setTableData($moveResult->getCheckerDesk());

                $this->entityManager->flush();
            }

            return;
        }

        $white = new White($whiteTeamUser['id'], $whiteTeamUser['username']);
        $black = new Black($blackTeamUser['id'], $blackTeamUser['username']);

        $game = new Game($white, $black);

        if ($request->isMethod('POST') && $request->request->has('formData')) {
            $data = json_decode($request->request->get('formData'), true);
            $from = htmlspecialchars($data['form1']);
            $to = htmlspecialchars($data['form2']);

            if ($from && $to) {
                $moveResult = $game->makeMoveWithCellTransform($startCondition, $from, $to, $logger);

                $this->bus->dispatch(new UpdateDeskMessage($computer, $game, $moveResult, $roomId, $gameLaunch->getComplexity()));

                $gameLaunch->setCurrentTurn($moveResult->getCurrentTurn());
                $gameLaunch->setTableData($moveResult->getCheckerDesk());

                $winnerId = $moveResult->getWinnerId();
                if ($winnerId) {
                    $winner = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $winnerId]);
                    if ($winner) {
                        $gameLaunch->setWinner($winner);
                        $gameLaunch->setIsActive(false);
                    }
                }

                $this->entityManager->flush();
            }
        }
    }
}
