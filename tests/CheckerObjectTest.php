<?php

namespace CheckersOOP\tests;

use CheckersOOP\db\Database;
use CheckersOOP\src\BlackTeam;
use CheckersOOP\src\WhiteTeam;
use PHPUnit\Framework\TestCase;
use CheckersOOP\src\CheckerDesk;
use CheckersOOP\src\CheckerObject;

class CheckerObjectTest extends TestCase
{
    public function testCreateTeamWhite()
    {
        $db = new Database;
        $checkerDesk = new CheckerDesk($db);
        $checkerObject = new CheckerObject($checkerDesk);
        $teamName = 'white_team';
        $side = 'white';
        $team = $checkerObject->createTeam($teamName, $side);
        $this->assertInstanceOf(WhiteTeam::class, $team);
    }

    public function testCreateTeamBlack()
    {
        $db = new Database;
        $checkerDesk = new CheckerDesk($db);
        $checkerObject = new CheckerObject($checkerDesk);
        $teamName = 'black_team';
        $side = 'black';
        $team = $checkerObject->createTeam($teamName, $side);
        $this->assertInstanceOf(BlackTeam::class, $team);
    }

    public function testCreateTeamInvalidSide()
    {
        $this->expectException(\InvalidArgumentException::class);

        $db = new Database;
        $checkerDesk = new CheckerDesk($db);
        $checkerObject = new CheckerObject($checkerDesk);
        $teamName = 'invalid_team';
        $side = 'invalid';
        $checkerObject->createTeam($teamName, $side);
    }
}