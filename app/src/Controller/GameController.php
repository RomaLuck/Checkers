<?php

namespace Src\Controller;

use Monolog\Logger;
use Src\Entity\GameLaunch;
use Src\Entity\Log;
use Src\Entity\User;
use Src\Game\CheckerDesk;
use Src\Game\Game;
use Src\Game\Team\Black;
use Src\Game\Team\White;
use Src\Helpers\EntityManagerFactory;
use Src\Helpers\LoggerFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\String\s;

class GameController extends BaseController
{
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $userId = $session->get('user');
        if (!$userId) {
            return new RedirectResponse('/login');
        }

        $entityManager = EntityManagerFactory::create();
        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $userId]);
        if (!$user) {
            return new RedirectResponse('/login');
        }

        $gameList = $entityManager->getRepository(GameLaunch::class)->findAll();
        $userGames = [];
        foreach ($gameList as $game) {
            if (
                $game->getWhiteTeamUser()?->getUsername() === $user->getUsername()
                || $game->getBlackTeamUser()?->getUsername() === $user->getUsername()
            ) {
                $userGames[] = $game;
            }
        }

        return $this->render('/start_game.view.php', [
            'username' => $user->getUsername(),
            'gameList' => $gameList,
            'baseUrl' => $request->getUri(),
            'session' => $session,
            'gamesCount' => count($userGames)
        ]);
    }

    public function create(Request $request): Response
    {
        $session = $request->getSession();
        $flashes = $session->getFlashBag();
        $userId = $session->get('user');

        $entityManager = EntityManagerFactory::create();
        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $userId]);
        if (!$user) {
            return new RedirectResponse('/login');
        }

        $color = $request->request->get('player');
        if (!$color) {
            $flashes->add('danger', 'Color is not set');
            return new RedirectResponse('/');
        }

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

        $query = http_build_query(['room' => $game->getRoomId()]);
        return new RedirectResponse('/game?' . $query);
    }

    public function join(Request $request): Response
    {
        $session = $request->getSession();
        $flashes = $session->getFlashBag();

        $userId = $session->get('user');

        $entityManager = EntityManagerFactory::create();
        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $userId]);
        if (!$user) {
            return new RedirectResponse('/login');
        }

        $color = $request->request->get('player');
        $roomId = $request->request->get('room');
        if (!$color || !$roomId) {
            $flashes->add('danger', 'Color not set');
            return new RedirectResponse('/');
        }

        $gameLaunch = $entityManager->getRepository(GameLaunch::class)->findOneBy(['room_id' => $roomId]);
        if (!$gameLaunch) {
            $flashes->add('danger', 'Game not found');
            return new RedirectResponse('/');
        }

        if ($color === 'white' && !$gameLaunch->getWhiteTeamUser()) {
            $gameLaunch->setWhiteTeamUser($user);
        } elseif ($color === 'black' && !$gameLaunch->getBlackTeamUser()) {
            $gameLaunch->setBlackTeamUser($user);
        }

        $entityManager->flush();

        $query = http_build_query(['room' => $roomId]);
        return new RedirectResponse('/game?' . $query);
    }

    public function game(Request $request): Response
    {
        $session = $request->getSession();
        $flashes = $session->getFlashBag();

        $roomId = s($request->getRequestUri())->match('!room=(\w+)!iu')[1];
        if (!$roomId) {
            $flashes->add('danger', 'Room not found');
            return new RedirectResponse('/');
        }

        $entityManager = EntityManagerFactory::create();
        $gameLaunch = $entityManager->getRepository(GameLaunch::class)->findOneBy(['room_id' => $roomId]);
        if (!$gameLaunch) {
            $flashes->add('danger', 'Game not found');
            return new RedirectResponse('/');
        }

        $userId = $session->get('user');
        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $userId]);
        if (!$user) {
            return new RedirectResponse('/login');
        }

        $userColor = null;
        $whiteTeamUser = $gameLaunch->getWhiteTeamUser();
        if ($user === $whiteTeamUser) {
            $userColor = 'white';
        }
        $blackTeamUser = $gameLaunch->getBlackTeamUser();
        if ($user === $blackTeamUser) {
            $userColor = 'black';
        }
        if (!$userColor) {
            $flashes->add('danger', 'This room is occupied');
            return new RedirectResponse('/');
        }

        $session->set('room', $roomId);
        $session->set('whiteUserName', $whiteTeamUser ? $whiteTeamUser->getUsername() : '');
        $session->set('blackUserName', $blackTeamUser ? $blackTeamUser->getUsername() : '');

        return $this->render('/game.view.php', [
            'color' => $userColor
        ]);
    }

    /**
     * @throws \JsonException
     */
    public function update(Request $request): Response
    {
        $session = $request->getSession();

        $entityManager = EntityManagerFactory::create();
        $roomId = $session->get('room');
        $gameLaunch = $entityManager->getRepository(GameLaunch::class)->findOneBy(['room_id' => $roomId]);
        if (!$gameLaunch) {
            return new RedirectResponse('/');
        }

        $white = new White($session->get('whiteUserName'));
        $black = new Black($session->get('blackUserName'));
        $desk = new CheckerDesk($gameLaunch->getTableData());
        $logger = LoggerFactory::getLogger($roomId);
        $game = new Game($desk, $white, $black, $logger);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['formData'])) {
            $data = json_decode($_POST['formData'], true, 512, JSON_THROW_ON_ERROR);
            $from = htmlspecialchars($data['form1']);
            $to = htmlspecialchars($data['form2']);

            if ($from && $to) {
                $updatedDesk = $game->run($from, $to);
                $gameLaunch->setTableData($updatedDesk);
                $entityManager->flush();
            }
        }

        $logs = $entityManager->getRepository(Log::class)->findBy(['channel' => $roomId]);
        $lastLogs = array_slice($logs, -10);
        $lastLogs = array_map(
            fn(Log $log) => [
                'logLevel' => Logger::toMonologLevel($log->getLevel())->getName(),
                'message' => $log->getMessage()
            ], $lastLogs
        );

        return new JsonResponse([
            'table' => $gameLaunch->getTableData(),
            'log' => $lastLogs
        ]);
    }

    public function end(Request $request): Response
    {
        $session = $request->getSession();

        $entityManager = EntityManagerFactory::create();
        $roomId = $session->get('room');
        $gameLaunch = $entityManager->getRepository(GameLaunch::class)->findOneBy(['room_id' => $roomId]);
        if (!$gameLaunch) {
            return new RedirectResponse('/');
        }

        $gameLaunch->setIsActive(false);
        $entityManager->flush();

        return $this->render('/end_game.view.php');
    }
}