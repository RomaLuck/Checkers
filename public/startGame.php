<?php
session_start();

use CheckersOOP\src\db\DbObject;
use App\Db\CheckerObjectRepository;

require_once 'vendor/autoload.php';

$dbObj = new DbObject();
$checkerDesk = new CheckerObjectRepository($dbObj);
if (isset($_POST['white']) && isset($_POST['black'])) {
    if ($dbObj->showAllItems('id') !== []) {
        $checkerDesk->clearTable();
    }
    $checkerDesk->fillTheTable();
    header("Location: public/game.php");
}

$_SESSION['white'] = $_POST['white'];
$_SESSION['black'] = $_POST['black'];

?>