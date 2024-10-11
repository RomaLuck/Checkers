<?php

declare(strict_types=1);

namespace App\Service\Game\Chess\Strategy;

use App\Entity\User;
use App\Entity\GameLaunch;
use App\Service\Cache\UserCacheService;
use App\Service\Game\Chess\ChessGame;
use App\Service\Game\Chess\Team\Black;
use App\Service\Game\Chess\Team\White;
use App\Service\Game\Move;
use App\Service\Game\MoveResult;
use App\Service\Game\StrategyInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

final class ChessPlayerStrategy implements StrategyInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserCacheService       $userCacheService,
    )
    {
    }

    public function run(GameLaunch $gameLaunch, string $roomId, Request $request, LoggerInterface $logger): void
    {
        $startCondition = new MoveResult($gameLaunch->getTableData(), $gameLaunch->getCurrentTurn());

        try {
            $whiteTeamUser = $this->userCacheService->getCachedWhiteTeamUser($gameLaunch, $roomId);
            $blackTeamUser = $this->userCacheService->getCachedBlackTeamUser($gameLaunch, $roomId);
        } catch (\RuntimeException) {
            $logger->info('Waiting for second player...');

            return;
        }

        $white = new White($whiteTeamUser['id'], $whiteTeamUser['username']);
        $black = new Black($blackTeamUser['id'], $blackTeamUser['username']);

        $game = new ChessGame($white, $black, $logger);

        if ($request->isMethod('POST') && $request->request->has('formData')) {
            $data = json_decode($request->request->get('formData'), true);
            $from = htmlspecialchars($data['form1']);
            $to = htmlspecialchars($data['form2']);

            if ($from && $to) {
                $move = Move::createMoveWithCellTransform($from, $to);
                $moveResult = $game->run($startCondition, $move);

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
