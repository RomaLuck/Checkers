<?php

use CheckersOOP\db\Database;  
use CheckersOOP\src\CheckerDesk;
use CheckersOOP\src\CheckerObject;

require_once __DIR__.'/vendor/autoload.php';

$db = new Database();
$checkerDesk = new CheckerDesk($db);
$checker = new CheckerObject($checkerDesk);
$white = $checker->createTeam('Roman','white');
var_dump($white);



