<?php

use CheckersOOP\db\Database;
use CheckersOOP\db\DbObject;
use CheckersOOP\src\BlackTeam;
use CheckersOOP\src\CheckerDesk;
use CheckersOOP\src\CheckerObject;
use CheckersOOP\src\WhiteTeam;

require_once __DIR__ . '/vendor/autoload.php';


    $db = new Database();
try {
    $dbObj = new DbObject($db);
    $checkerDesk = new CheckerDesk($dbObj);
    $white = new WhiteTeam('roman');
    $black = new BlackTeam('olena');
    $whiteObject = new CheckerObject($dbObj, $white);
    $whiteObject->createFigure('checker');
//    $whiteObject->createFigure('queen');
//    $whiteObject->move('c1', 'a8');
$checkerDesk->fillTheTable();
} catch (Exception $e) {
    echo $e->getMessage();
}




