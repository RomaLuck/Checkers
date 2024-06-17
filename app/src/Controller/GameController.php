<?php

namespace App\Controller;

use App\Entity\GameLaunch;
use App\Entity\Log;
use App\Entity\User;
use App\Service\Game\CheckerDesk;
use App\Service\Game\Game;
use App\Service\Game\Team\Black;
use App\Service\Game\Team\White;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class GameController extends AbstractController
{
    #[Route('/', name: 'app_game_list', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $gameList = $entityManager->getRepository(GameLaunch::class)->findBy(['is_active' => true]);
        $gamesCount = $user->getWhiteTeamGames()->count() + $user->getBlackTeamGames()->count();
        $winsCount = $user->getWonGames()->count();

        return $this->render('game/index.html.twig', [
            'username' => $user->getUsername(),
            'gameList' => $gameList,
            'baseUrl' => $request->getUri(),
            'gamesCount' => $gamesCount,
            'winsCount' => $winsCount
        ]);
    }

    #[Route('/create', name: 'app_game_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $color = $request->request->get('player');
        if (!$color) {
            $this->addFlash('danger', 'Color is not set');
            return $this->redirectToRoute('app_game_list');
        }

        $user = $this->getUser();
        $game = new GameLaunch();
        if ($color === 'white') {
            $game->setWhiteTeamUser($user);
        } elseif ($color === 'black') {
            $game->setBlackTeamUser($user);
        }

        $game->setTableData(CheckerDesk::START_DESK);
        $game->setIsActive(true);

        $entityManager->persist($game);
        $entityManager->flush();

        return $this->redirectToRoute('app_game', ['room' => $game->getRoomId()]);
    }

    #[Route('/join', name: 'app_game_join', methods: ['POST'])]
    public function join(Request $request, EntityManagerInterface $entityManager): Response
    {
        $color = $request->request->get('player');
        $roomId = $request->request->get('room');
        if (!$color || !$roomId) {
            $this->addFlash('danger', 'Color not set');
            return $this->redirectToRoute('app_game_list');
        }

        $gameLaunch = $entityManager->getRepository(GameLaunch::class)->findOneBy(['room_id' => $roomId]);
        if (!$gameLaunch) {
            $this->addFlash('danger', 'Game not found');
            return $this->redirectToRoute('app_game_list');
        }

        $user = $this->getUser();
        if ($color === 'white' && !$gameLaunch->getWhiteTeamUser()) {
            $gameLaunch->setWhiteTeamUser($user);
        } elseif ($color === 'black' && !$gameLaunch->getBlackTeamUser()) {
            $gameLaunch->setBlackTeamUser($user);
        }

        $entityManager->flush();

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

        $userColor = null;
        $user = $this->getUser();
        $whiteTeamUser = $gameLaunch->getWhiteTeamUser();
        if ($user === $whiteTeamUser) {
            $userColor = 'white';
        }
        $blackTeamUser = $gameLaunch->getBlackTeamUser();
        if ($user === $blackTeamUser) {
            $userColor = 'black';
        }
        if (!$userColor) {
            $this->addFlash('danger', 'This room is occupied');
            return $this->redirectToRoute('app_game_list');
        }

        $session->set('room', $room);
        $session->set('whiteUserName', $whiteTeamUser ? $whiteTeamUser->getUsername() : '');
        $session->set('blackUserName', $blackTeamUser ? $blackTeamUser->getUsername() : '');

        return $this->render('game/game.html.twig', [
            'color' => $userColor,
        ]);
    }

    #[Route('/update', name: 'app_game_update', methods: ['GET', 'POST'])]
    public function update(Request $request, Session $session, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        $roomId = $session->get('room');
        $gameLaunch = $entityManager->getRepository(GameLaunch::class)->findOneBy(['room_id' => $roomId]);
        if (!$gameLaunch) {
            return $this->redirectToRoute('app_game_list');
        }

        $whiteTeamUser = $gameLaunch->getWhiteTeamUser();
        $blackTeamUser = $gameLaunch->getBlackTeamUser();

        if (!$whiteTeamUser || !$blackTeamUser) {
            return $this->json([]);
        }

        $white = new White($whiteTeamUser->getId(), $whiteTeamUser->getUsername());
        $black = new Black($blackTeamUser->getId(), $blackTeamUser->getUsername());

        $desk = new CheckerDesk($gameLaunch->getTableData());

        $logger = $logger->withName($roomId);
        $game = new Game($desk, $white, $black, $logger);

        if ($request->isMethod('POST') && $request->request->has('formData')) {
            $data = json_decode($request->request->get('formData'), true, 512, JSON_THROW_ON_ERROR);
            $from = htmlspecialchars($data['form1']);
            $to = htmlspecialchars($data['form2']);

            if ($from && $to) {
                $updatedDesk = $game->run($from, $to);
                $gameLaunch->setTableData($updatedDesk);
                $entityManager->flush();
            }
        }

        $session->set('advantagePlayer', $game->getAdvantagePlayer()->getId());

        return $this->json([
            'table' => $gameLaunch->getTableData(),
            'log' => $this->getLastLogs($entityManager, $roomId),
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

        $winnerId = $session->get('advantagePlayer');
        $winner = $entityManager->getRepository(User::class)->findOneBy(['id' => $winnerId]);
        if ($winner) {
            $gameLaunch->setWinner($winner);
        }
        $gameLaunch->setIsActive(false);
        $entityManager->flush();

        return $this->render('game/end-game.html.twig');
    }

    /**
     * @return array<array>
     */
    public function getLastLogs(EntityManagerInterface $entityManager, mixed $roomId): array
    {
        $logs = $entityManager->getRepository(Log::class)->findBy(['channel' => $roomId], limit: 10);

        return array_map(
            fn(Log $log) => [
                'logLevel' => Logger::toMonologLevel($log->getLevel())->getName(),
                'message' => $log->getMessage()
            ], $logs
        );
    }
}
