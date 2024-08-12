<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\GameLaunch;
use App\Event\GameStatusUpdatedEvent;
use App\Event\JoinedGameEvent;
use App\Service\Game\GameService;
use App\Service\Game\GameStrategyIds;
use App\Service\Game\Strategy\GameStrategyFactory;
use App\Service\Mercure\MercureService;
use App\Service\Monolog\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mercure\Discovery;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[IsGranted('ROLE_USER')]
final class GameController extends AbstractController
{
    public function __construct(
        private readonly GameService    $gameService,
        private readonly LoggerService  $loggerService,
    )
    {
    }

    #[Route('/', name: 'app_game_list', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $rating = 0;

        $gameList = $entityManager->getRepository(GameLaunch::class)->findBy(['is_active' => true]);
        $winsCount = $user->getWonGames()->count();
        $gamesCount = $user->getWhiteTeamGames()->count() + $user->getBlackTeamGames()->count();
        if ($gamesCount > 0) {
            $rating = $winsCount / $gamesCount * 100;
        }

        return $this->render('game/index.html.twig', [
            'username' => $user->getUsername(),
            'gameList' => $gameList,
            'gamesCount' => $gamesCount,
            'winsCount' => $winsCount,
            'rating' => $rating
        ]);
    }

    #[Route('/create', name: 'app_game_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $color = $request->request->get('player');
        if (!in_array($color, ['white', 'black'])) {
            $this->addFlash('danger', 'Color is not set');
            return $this->redirectToRoute('app_game_list');
        }

        $strategyId = (int)$request->request->get('strategy');
        if (!in_array($strategyId, GameStrategyIds::allStrategyIds(), true)) {
            $this->addFlash('danger', 'Type of game is not set');
            return $this->redirectToRoute('app_game_list');
        }

        $game = $this->gameService->createGameLaunch($user, $color, $strategyId);

        return $this->redirectToRoute('app_game', ['room' => $game->getRoomId()]);
    }

    #[Route('/join', name: 'app_game_join', methods: ['POST'])]
    public function join(
        Request                  $request,
        EntityManagerInterface   $entityManager,
        EventDispatcherInterface $eventDispatcher,
    ): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $color = $request->request->get('player');
        $roomId = $request->request->get('room');
        if (!$roomId || !in_array($color, ['white', 'black'])) {
            $this->addFlash('danger', 'Color not set');
            return $this->redirectToRoute('app_game_list');
        }

        $gameLaunch = $entityManager->getRepository(GameLaunch::class)->findOneBy(['room_id' => $roomId]);
        if (!$gameLaunch) {
            $this->addFlash('danger', 'Game not found');
            return $this->redirectToRoute('app_game_list');
        }

        $this->gameService->joinToGame($gameLaunch, $user, $color);

        $event = new JoinedGameEvent($gameLaunch, $user->getUsername());
        $eventDispatcher->dispatch($event, 'JoinedGameEvent');

        return $this->redirectToRoute('app_game', ['room' => $roomId]);
    }

    #[Route('/game/{room}', name: 'app_game', methods: ['GET'])]
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

        return $this->render('game/game.html.twig', [
            'color' => $userColor,
            'room' => $room
        ]);
    }

    #[Route('/update', name: 'app_game_update', methods: ['GET', 'POST'])]
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

//        $this->mercureService->publishData($gameLaunch, $hub);

        return $this->json([
            'table' => $gameLaunch->getTableData(),
            'log' => $this->loggerService->getLastLogs($roomId),
        ]);
    }

    #[Route('/end', name: 'app_game_end', methods: ['GET'])]
    public function end(Session $session, EntityManagerInterface $entityManager): Response
    {
        $roomId = $session->get('room');
        $gameLaunch = $entityManager->getRepository(GameLaunch::class)->findOneBy(['room_id' => $roomId]);
        if (!$gameLaunch) {
            return $this->redirectToRoute('app_game_list');
        }

        $gameLaunch->setIsActive(false);
        $entityManager->flush();

        return $this->render('game/end-game.html.twig');
    }

    #[Route('/discover', methods: ['GET'])]
    public function discover(Request $request, Discovery $discovery): Response
    {
        $discovery->addLink($request);

        return $this->json('Done');
    }
}
