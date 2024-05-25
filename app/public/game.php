<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';

use Src\Game;
use Src\Teams\Black;
use Src\Teams\White;

if (isset($_POST['white'], $_POST['black'])) {
    $_SESSION['white'] = $_POST['white'];
    $_SESSION['black'] = $_POST['black'];
}

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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Input Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<div class="container mt-5">
    <form method="POST" class="form d-flex justify-content-center d-none" autocomplete="off">
        <div class="col-md-4">
            <input type="text" class="form-control" name="from" id="form1"
                   placeholder="choose a checker" autocomplete="off">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control" name="to" id="form2" placeholder="select a cell"
                   autocomplete="off">
        </div>
        <div class="col-md-2">
            <input type="submit" class="form-control btn btn-primary" name="submit" id="submit"
                   value="enter">
        </div>
    </form>
</div>

<div class="container">
    <div class="row" id="table-responsive">
        <div class="d-flex justify-content-center">
            <div class="table-responsive text-center">
                <table class="chess-board">
                    <?php
                    $letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
                    $i = 0;
                    foreach ($game->getDesk() as $deskRow) {
                        $j = 1;
                        ?>
                        <tr>
                            <?php foreach ($deskRow as $cell) { ?>
                                <td id="<?= $letters[$i] . $j++ ?>"><?= $cell ?></td>
                            <?php } ?>
                        </tr>
                        <?php
                        $i++;
                    } ?>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <a href="end_game.php" class="btn btn-danger mt-2 float-end">Finish game</a>
    </div>
</div>

<script src="js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
