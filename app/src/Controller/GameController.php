<?php

namespace Src\Controller;

use Src\Entity\GameLaunch;
use Src\Entity\User;
use Src\Game\CheckerDesk;
use Src\Game\Game;
use Src\Game\Team\Black;
use Src\Game\Team\White;
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
        $game->setIsActive(true);

        $entityManager->persist($game);
        $entityManager->flush();

        $query = http_build_query(['room' => $game->getRoomId()]);
        return new RedirectResponse('/game?' . $query);
    }

    public function join(Request $request): Response
    {
        $session = $request->getSession();
        $userId = $session->get('user');

        $entityManager = EntityManagerFactory::create();
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

        $roomId = s($request->getRequestUri())->match('!room=(\w+)!iu')[1];
        if (!$roomId) {
            return new RedirectResponse('/');
        }

        $entityManager = EntityManagerFactory::create();
        $gameLaunch = $entityManager->getRepository(GameLaunch::class)->findOneBy(['room_id' => $roomId]);
        if (!$gameLaunch) {
            return new RedirectResponse('/');
        }

        $userId = $session->get('user');
        $user = $entityManager->find(User::class, $userId);
        if (!$user) {
            return new RedirectResponse('/login');
        }

        $userColor = null;
        $username = $user->getUsername();
        $whiteUserName = $gameLaunch->getWhiteTeamUser() ? $gameLaunch->getWhiteTeamUser()->getUsername() : '';
        $blackUserName = $gameLaunch->getBlackTeamUser() ? $gameLaunch->getBlackTeamUser()->getUsername() : '';
        if ($username === $whiteUserName) {
            $userColor = 'white';
        }
        if ($username === $blackUserName) {
            $userColor = 'black';
        }
        if (!$userColor) {
            return new RedirectResponse('/');
        }

        $session->set('room', $roomId);
        $session->set('whiteUserName', $whiteUserName);
        $session->set('blackUserName', $blackUserName);


        return $this->render('/game.view.php', [
            'color' => $userColor
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

        $white = new White($session->get('whiteUserName'));
        $black = new Black($session->get('blackUserName'));
        $desk = new CheckerDesk($gameLaunch->getTableData());
        $game = new Game($desk, $white, $black);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['formData'])) {
            $data = json_decode($_POST['formData'], true);
            $from = htmlspecialchars($data['form1']);
            $to = htmlspecialchars($data['form2']);

            if ($from && $to) {
                $updatedDesk = $game->run($from, $to);
                $gameLaunch->setTableData($updatedDesk);
                $entityManager->flush();
            }
        }

        return new JsonResponse([
            'table' => $gameLaunch->getTableData(),
            'log' => LogReader::getLastLogs(10)
        ]);
    }

    public function end(Request $request): Response
    {
        $session = $request->getSession();
        $session->clear();

        LogReader::deleteLogFiles();

        return $this->render('/end_game.view.php');
    }
}