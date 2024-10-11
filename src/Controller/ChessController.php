<?php

namespace App\Controller;

use App\Entity\GameLaunch;
use App\Event\GameStatusUpdatedEvent;
use App\Service\Game\Checkers\GameService;
use App\Service\Game\Checkers\Strategy\GameStrategyFactory;
use App\Service\Monolog\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route('/chess')]
class ChessController extends AbstractController
{
    public function __construct(
        private readonly GameService   $gameService,
        private readonly LoggerService $loggerService,
    )
    {
    }

    #[Route('/room/{room}', name: 'chess_game', methods: ['GET'])]
    public function game(EntityManagerInterface $entityManager, Session $session, string $room): Response
    {
        $gameLaunch = $entityManager->getRepository(GameLaunch::class)->findOneBy(['room_id' => $room]);
        if (!$gameLaunch) {
            $this->addFlash('danger', 'Game not found');

            return $this->redirectToRoute('app_game_list');
        }

        $userColor = $this->gameService->getUserColor($gameLaunch, $this->getUser());
        if (!$userColor) {
            $this->addFlash('danger', 'This room is occupied');

            return $this->redirectToRoute('app_game_list');
        }

        $session->set('room', $room);

        return $this->render('game/chess.html.twig', [
            'color' => $userColor,
            'room' => $room,
        ]);
    }

    #[Route('/update', name: 'chess_game_update', methods: ['GET', 'POST'])]
    public function update(
        GameStrategyFactory      $gameStrategyFactory,
        Request                  $request,
        Session                  $session,
        EntityManagerInterface   $entityManager,
        LoggerInterface          $logger,
        EventDispatcherInterface $eventDispatcher,
    ): Response
    {
        $roomId = $session->get('room');

        /** @var LoggerInterface $logger */
        $logger = $logger->withName($roomId);

        $gameLaunch = $entityManager->getRepository(GameLaunch::class)->findOneBy(['room_id' => $roomId]);
        if (!$gameLaunch) {
            return $this->redirectToRoute('app_game_list');
        }

        $strategyId = $gameLaunch->getStrategyId();
        $game = $gameStrategyFactory->create($strategyId);
        $game->run($gameLaunch, $roomId, $request, $logger);

        $event = new GameStatusUpdatedEvent($gameLaunch);
        $eventDispatcher->dispatch($event, 'GameStatusUpdatedEvent');

        return $this->json([
            'table' => $gameLaunch->getTableData(),
            'log' => $this->loggerService->getLastLogs($roomId),
        ]);
    }
}