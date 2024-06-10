<?php

namespace Src\Controller;

use Src\Entity\GameLaunch;
use Src\Entity\User;
use Src\Game\CheckerDesk;
use Src\Game\Game;
use Src\Helpers\EntityManagerFactory;
use Src\Helpers\LogReader;
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

        $user = $entityManager->find(User::class, $userId);
        if (!$user) {
            return new RedirectResponse('/login');
        }

        $gameList = $entityManager->getRepository(GameLaunch::class)->findAll();

        return $this->render('/start_game.view.php', [
            'username' => $user->getUsername(),
            'gameList' => $gameList,
            'baseUrl' => $request->getUri()
        ]);
    }

    public function create(Request $request): Response
    {
        $session = $request->getSession();
        $flashes = $session->getFlashBag();
        $userId = $session->get('user');

        $entityManager = EntityManagerFactory::create();

        $user = $entityManager->find(User::class, $userId);
        if (!$user) {
            return new RedirectResponse('/login');
        }

        $color = $request->request->get('player');
        if (!$color) {
            $flashes->add('warning', 'Color is not set');

            return new RedirectResponse('/');
        }

        $game = new GameLaunch();
        if ($color === 'white') {
            $game->setWhiteTeamUser($user);
        } elseif ($color === 'black') {
            $game->setBlackTeamUser($user);
        }

        $game->setTableData(CheckerDesk::START_DESK);

        $entityManager->persist($game);
        $entityManager->flush();

        $query = http_build_query(['room' => $game->getRoomId()]);
        return new RedirectResponse('/game?' . $query);
    }

    public function join(Request $request): Response
    {
        $entityManager = EntityManagerFactory::create();

        $session = $request->getSession();
        $userId = $session->get('user');
        $user = $entityManager->find(User::class, $userId);
        if (!$user) {
            return new RedirectResponse('/login');
        }

        $color = $request->request->get('player');
        $roomId = $request->request->get('room');
        if (!$color || !$roomId) {
            return new RedirectResponse('/');
        }

        $gameLaunch = $entityManager->getRepository(GameLaunch::class)->findOneBy(['room_id' => $roomId]);
        if (!$gameLaunch) {
            return new RedirectResponse('/');
        }

        if ($color === 'white') {
            $gameLaunch->setWhiteTeamUser($user);
        } elseif ($color === 'black') {
            $gameLaunch->setBlackTeamUser($user);
        }

        $entityManager->flush();

        $query = http_build_query(['room' => $roomId]);
        return new RedirectResponse('/game?' . $query);
    }

    public function game(Request $request): Response
    {
        $roomId = s($request->getRequestUri())->match('!room=(\w+)!iu')[1];
        if (!$roomId) {
            return new RedirectResponse('/');
        }

        $session = $request->getSession();
        $session->set('room', $roomId);

        $entityManager = EntityManagerFactory::create();
        $gameLaunch = $entityManager->getRepository(GameLaunch::class)->findOneBy(['room_id' => $roomId]);
        if (!$gameLaunch) {
            return new RedirectResponse('/');
        }

        $game = new Game($gameLaunch, $entityManager);

        $rawLogs = LogReader::read(10);

        $logs = [];
        foreach ($rawLogs as $rawLog) {
            if (preg_match(
                '!^\[checkers] \[(?<logLevel>\w+)] (?<message>.+)!iu',
                $rawLog,
                $rawLogMatch
            )) {
                $logs[] = $rawLogMatch;
            }
        }
        return $this->render('/game.view.php', [
            'logs' => $logs,
            'game' => $game,
        ]);
    }

    public function update(Request $request): Response
    {
        $session = $request->getSession();

        $entityManager = EntityManagerFactory::create();
        $gameLaunch = $entityManager->getRepository(GameLaunch::class)
            ->findOneBy(['room_id' => $session->get('room')]);
        if (!$gameLaunch) {
            return new RedirectResponse('/');
        }

        $game = new Game($gameLaunch, $entityManager);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['formData'])) {
            $data = json_decode($_POST['formData'], true);
            $from = htmlspecialchars($data['form1']);
            $to = htmlspecialchars($data['form2']);

            if ($from && $to) {
                $game->run($from, $to);
            }
        }

        return new JsonResponse($gameLaunch->getTableData());
    }

    public function end(Request $request): Response
    {
        $session = $request->getSession();
        $session->clear();

        LogReader::deleteLogFiles();

        return $this->render('/end_game.view.php');
    }
}