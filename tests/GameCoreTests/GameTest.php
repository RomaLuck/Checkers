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

    public function testCheckerCell(): void
    {
        $cells = self::$repository->showAllItems(['cell']);
        self::assertIsArray($cells);
        self::assertContains('a1', $cells);
        self::assertContains('h8', $cells);
    }

    public function testTeamName(): void
    {
        self::assertEquals('Olena', self::$white->getTeamName());
        self::assertEquals('Roman', self::$black->getTeamName());
    }

    /**
     * @throws \Exception
     */
    public function testMove(): void
    {
        self::$white->setFigureType(self::$checker)->move('a3', 'b4');
        self::$white->setFigureType(self::$checker)->move('b4', 'c5');
        self::$black->setFigureType(self::$checker)->move('h6', 'g5');
        self::assertEquals('', self::$repository->findOneBy(['cell' => 'a3'])->getTeam());
        self::assertEquals('white', self::$repository->findOneBy(['cell' => 'c5'])->getTeam());
        self::assertEquals('black', self::$repository->findOneBy(['cell' => 'g7'])->getTeam());
    }

    /**
     * @throws \Exception
     */
    public function testBeatEnemy(): void
    {
        self::assertEquals('white', self::$repository->findOneBy(['cell' => 'c5'])->getTeam());
        self::$black->setFigureType(self::$checker)->move('b6', 'c5');
        self::assertEquals('', self::$repository->findOneBy(['cell' => 'b6'])->getTeam());
        self::assertEquals('', self::$repository->findOneBy(['cell' => 'c5'])->getTeam());
        self::assertEquals('black', self::$repository->findOneBy(['cell' => 'd4'])->getTeam());
    }
}