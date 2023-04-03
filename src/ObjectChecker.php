<?php
require_once __DIR__ . '/../db/Database.php';
require_once __DIR__ . '/../db/DbObject.php';
require_once __DIR__ . '/../db/DbObjectMove.php';
require_once __DIR__ . '/RulesChecker.php';

class ObjectChecker extends RulesChecker
{
    public $team_name;
    public $team;
    public static $move_direction;
    public static $move_opportunity = 1;

    public function setTeam($name)
    {
        $this->team->setTableName($name);
    }

    public function showAllTeam()
    {
        return $this->team->showAllTeam();
    }
}

$db = new Database;
$move = new DbObjectMove($db);
$team = new DbObject($db);
$white = new ObjectChecker($team);
$white->setTeam('WhiteCheckersTeam');
var_dump($white->showAllTeam());
