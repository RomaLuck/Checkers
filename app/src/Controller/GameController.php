<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\GameLaunch;
use App\Entity\Log;
use App\Entity\User;
use App\Service\Game\CheckerDesk;
use App\Service\Game\Game;
use App\Service\Game\GameService;
use App\Service\Game\GameStrategyIds;
use App\Service\Game\Robot\Robot;
use App\Service\Game\Team\Black;
use App\Service\Game\Team\White;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mercure\Discovery;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class GameController extends AbstractController
{
    public function __construct(private readonly GameService $gameService)
    {
    }

    #[Route('/', name: 'app_game_list', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
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
            'gamesCount' => $gamesCount,
            'winsCount' => $winsCount
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
    public function join(Request $request, EntityManagerInterface $entityManager): Response
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
        ]);
    }

    #[Route('/update', name: 'app_game_update', methods: ['GET', 'POST'])]
    public function update(
        Request                $request,
        Session                $session,
        EntityManagerInterface $entityManager,
        LoggerInterface        $logger,
        HubInterface           $hub
    ): Response
    {
        $roomId = $session->get('room');

        $logger = $logger->withName($roomId);

        $gameLaunch = $entityManager->getRepository(GameLaunch::class)->findOneBy(['room_id' => $roomId]);
        if (!$gameLaunch) {
            return $this->redirectToRoute('app_game_list');
        }

        $strategyId = $gameLaunch->getStrategyId();
        if ($strategyId === GameStrategyIds::COMPUTER) {
            $computer = $entityManager->getRepository(User::class)->findOneByRole('ROLE_COMPUTER');
            if ($computer) {
                $gameLaunch->getWhiteTeamUser()
                    ? $gameLaunch->setBlackTeamUser($computer)
                    : $gameLaunch->setWhiteTeamUser($computer);

                $entityManager->flush();
            }
        }

        $whiteTeamUser = $gameLaunch->getWhiteTeamUser();
        $blackTeamUser = $gameLaunch->getBlackTeamUser();

        if (!$whiteTeamUser || !$blackTeamUser) {
            $logger->info('Waiting for second player...');
            return $this->json([
                'table' => $gameLaunch->getTableData(),
                'log' => $this->getLastLogs($entityManager, $roomId),
            ]);
        }

        $white = new White($whiteTeamUser->getId(), $whiteTeamUser->getUsername());
        $black = new Black($blackTeamUser->getId(), $blackTeamUser->getUsername());

        $desk = new CheckerDesk($gameLaunch->getTableData());

        $game = new Game($desk, $white, $black, $logger);

        if ($request->isMethod('POST') && $request->request->has('formData')) {
            $data = json_decode($request->request->get('formData'), true);
            $from = htmlspecialchars($data['form1']);
            $to = htmlspecialchars($data['form2']);

            if ($from && $to) {
                $updatedDesk = $game->makeMove($from, $to, true);
                if ($strategyId === GameStrategyIds::COMPUTER) {
                    $computerTeam = $computer->getId() === $white->getId() ? $white : $black;
                    $robot = new Robot($game, $computerTeam, 10);
                    $updatedDesk = $robot->run();
                }

                $gameLaunch->setTableData($updatedDesk);
                $entityManager->flush();

                $session->set('advantagePlayer', $game->getAdvantagePlayer()->getId());

                $this->publishData($gameLaunch, $entityManager, $roomId, $hub);

                return $this->json('Done');
            }
        }

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

    #[Route('/discover', methods: ['GET'])]
    public function discover(Request $request, Discovery $discovery): Response
    {
        $discovery->addLink($request);

        return $this->json('Done');
    }

    /**
     * @return array<array>
     */
    public function getLastLogs(EntityManagerInterface $entityManager, mixed $roomId): array
    {
        #todo перевірити чому не працює в prod середовищі
        $logs = $entityManager->getRepository(Log::class)->findBy(
            ['channel' => $roomId],
            orderBy: ['id' => 'DESC'],
            limit: 10
        );

        return array_map(
            fn(Log $log) => [
                'message' => $log->getMessage(),
                'time' => $log->getTime()->format('d/m/y H:i:s')
            ], $logs
        );
    }

    public function publishData(
        GameLaunch             $gameLaunch,
        EntityManagerInterface $entityManager,
        string                 $roomId,
        HubInterface           $hub): void
    {
        $update = new Update(
            '/chat',
            json_encode([
                'table' => $gameLaunch->getTableData(),
                'log' => $this->getLastLogs($entityManager, $roomId),
            ])
        );

        $hub->publish($update);
    }
}
