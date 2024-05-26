<?php

use Src\Game;
use Src\Helpers\LogReader;
use Src\Team\Black;
use Src\Team\White;

$white = new White($_SESSION['white']);
$black = new Black($_SESSION['black']);
$game = new Game($white, $black);

$logs = LogReader::read(10);

view('game.view.php', [
    'logs' => $logs,
    'game' => $game
]);