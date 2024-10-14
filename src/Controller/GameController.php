<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\GameLaunch;
use App\Event\JoinedGameEvent;
use App\Service\Game\Checkers\GameService;
use App\Service\Game\GameStrategyIds;
use App\Service\Game\GameTypeIds;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mercure\Discovery;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[IsGranted('ROLE_USER')]
final class GameController extends AbstractController
{
    public function __construct(
        private readonly GameService $gameService,
    ) {
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
            'rating' => $rating,
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

        $gameTypeId = $request->request->getInt('type');

        $strategyId = $request->request->getInt('strategy');
        if (!in_array($strategyId, GameStrategyIds::allStrategyIds(), true)) {
            $this->addFlash('danger', 'Type of game is not set');

            return $this->redirectToRoute('app_game_list');
        }

        $complexity = $request->request->getInt('complexity');

        $game = $this->gameService->createGameLaunch($user, $color, $gameTypeId, $strategyId, $complexity);
        if ($gameTypeId === GameTypeIds::CHECKERS_TYPE) {
            return $this->redirectToRoute('checkers_game', ['room' => $game->getRoomId()]);
        }

        return $this->redirectToRoute('chess_game', ['room' => $game->getRoomId()]);
    }

    #[Route('/join', name: 'app_game_join', methods: ['POST'])]
    public function join(
        Request $request,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
    ): Response {
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

        $gameTypeId = $gameLaunch->getTypeId();
        if ($gameTypeId === GameTypeIds::CHECKERS_TYPE) {
            return $this->redirectToRoute('checkers_game', ['room' => $roomId]);
        }

        return $this->redirectToRoute('chess_game', ['room' => $roomId]);
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
