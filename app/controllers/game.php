<?php

use Src\Game;
use Src\Helpers\LogReader;
use Src\Team\Black;
use Src\Team\White;

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

view('game.view.php', [
    'logs' => $logs,
    'game' => $game,
    'queue' => $queue
]);