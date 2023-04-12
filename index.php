<?php

use CheckersOOP\db\Database;
use CheckersOOP\src\CheckerDesk;
use CheckersOOP\src\RuleChecker;
use CheckersOOP\src\Player;

require_once __DIR__.'/vendor/autoload.php';

$db = new Database();
$checkerDesk = new CheckerDesk($db);
$white = new Player ($db,'WhiteCheckersTeam');
$rules = new RuleChecker($white, $checkerDesk);
// $white->setRules($rules);
// $white->move('a4', 'a5');
$checkerDesk->fillTableByPieces();


