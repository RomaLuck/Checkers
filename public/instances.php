<?php

use App\Db\CheckerObjectRepository;
use App\Db\Database;
use App\GameCore\Checker;

require_once '../vendor/autoload.php';

$db = (new Database())->connect();
$repository = new CheckerObjectRepository($db);
$repository->setTableName('CheckerDesk');
$checker = new Checker($repository);
