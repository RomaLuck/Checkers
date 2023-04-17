<?php

use CheckersOOP\db\Database;  
use CheckersOOP\src\CheckerDesk;
use CheckersOOP\src\CheckerObject;

require_once __DIR__.'/vendor/autoload.php';

$db = new Database();
$checkerDesk = new CheckerDesk($db);
$object = new CheckerObject($checkerDesk);
$checker = $object->createChecker();
$queen = $object->createQueen();
$white = Player::createWhite('Roman','white');
$white->checker->move('a1','a2');

$white = $checker->createTeam('Roman','white');
var_dump($white);


