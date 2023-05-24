<?php
session_start();

use CheckersOOP\db\DbObject;
use CheckersOOP\src\CheckerDesk;

require_once __DIR__ . '/../vendor/autoload.php';

$dbObj = new DbObject();
$checkerDesk = new CheckerDesk($dbObj);
if (isset($_POST['white']) && isset($_POST['black'])) {
    if ($dbObj->showAllItems('id') !== []) {
        $checkerDesk->clearTable();
    }
    $checkerDesk->fillTheTable();
    header("Location: ../game/game.php");
}

$_SESSION['white'] = $_POST['white'];
$_SESSION['black'] = $_POST['black'];

?>