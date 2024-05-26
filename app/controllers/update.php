<?php

use Src\Game;
use Src\Team\Black;
use Src\Team\White;

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

redirect('/game');