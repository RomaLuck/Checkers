<?php

namespace App\Tests\GameCoreTests;

use App\Db\CheckerObjectRepository;
use App\Db\Database;
use App\GameCore\BlackTeam;
use App\GameCore\Checker;
use App\GameCore\WhiteTeam;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    protected static BlackTeam $black;
    protected static WhiteTeam $white;
    protected static Checker $checker;
    protected static CheckerObjectRepository $repository;
    protected static ?\PDO $db;

    public static function setUpBeforeClass(): void
    {
        self::$db = (new Database())->connect();
        self::$repository = new CheckerObjectRepository(self::$db);
        self::$repository->setTableName('CheckerDesk');
        self::$repository->fillTheTable();
        self::$checker = new Checker(self::$repository);
        self::$black = new BlackTeam('Roman');
        self::$white = new WhiteTeam('Olena');
    }

    public static function tearDownAfterClass(): void
    {
        self::$repository->clearTable();
        self::$db = null;
    }

    public function testCheckerDesk():void
    {
        $cells = self::$repository->showAllItems();

    }
}