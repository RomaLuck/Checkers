<?php

namespace Src\Controller;

use Src\Game;
use Src\Helpers\LogReader;
use Src\Team\Black;
use Src\Team\White;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class GameController extends BaseController
{
    public function index(): Response
    {
        return $this->render('/start_game.view.php');
    }

    public function game(): Response
    {
        $white = new White($_SESSION['white']);
        $black = new Black($_SESSION['black']);
        $game = new Game($white, $black);
        $queue = $game->getQueue();

        $rawLogs = LogReader::read(10);

        $logs = [];
        foreach ($rawLogs as $rawLog) {
            if (preg_match('!^\[checkers] \[(?<logLevel>\w+)] (?<message>.+)!iu', $rawLog, $rawLogMatch)) {
                $logs[] = $rawLogMatch;
            }
        }
        return $this->render('/game.view.php', [
            'logs' => $logs,
            'game' => $game,
            'queue' => $queue,
        ]);
    }

    public function update(): Response
    {
        $white = new White($_SESSION['white']);
        $black = new Black($_SESSION['black']);
        $game = new Game($white, $black);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['formData'])) {
            $data = json_decode($_POST['formData'], true);
            $from = htmlspecialchars($data['form1']);
            $to = htmlspecialchars($data['form2']);

            if ($from && $to) {
                $game->run($from, $to);
            }
        }

        return new RedirectResponse('/game');
    }

    public function end(): Response
    {
        LogReader::deleteLogFiles();

        return $this->render('/end_game.view.php');
    }
}