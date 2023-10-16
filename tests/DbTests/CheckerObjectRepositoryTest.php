<?php

namespace App\Tests\DbTests;

use App\Db\CheckerObjectRepository;
use App\Db\SqlQueryBuilder;

class CheckerObjectRepositoryTest extends DbTestAbstract
{
    protected ?SqlQueryBuilder $sqlQueryBuilder;
    private static ?CheckerObjectRepository $checkerObject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$checkerObject = new CheckerObjectRepository(self::$pdo);
        self::$checkerObject->setTableName(self::$table);
        self::$checkerObject->fillTheTable();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->sqlQueryBuilder = new SqlQueryBuilder(self::$pdo);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->sqlQueryBuilder = null;
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::$checkerObject = null;
    }

    public function testTableName(): void
    {
        self::assertEquals('CheckerDeskTest', self::$checkerObject->getTableName());
    }

    public function testBoard(): void
    {
        $black = self::$checkerObject->findOneBy(['cell' => 'h6']);
        $white = self::$checkerObject->findOneBy(['cell' => 'd2']);
        self::assertEquals('black', $black->getTeam());
        self::assertEquals('white', $white->getTeam());
    }

    public function testFindOneBy(): void
    {
        $expected = $this->sqlQueryBuilder->select(self::$table)
            ->where('cell', ':c')
            ->setParameters([':c' => 'h6'])
            ->getQuery()
            ->findOne();

        $result = self::$checkerObject->findOneBy(['cell' => 'h6']);
        self::assertEquals($expected, $result);
    }

    public function testShowAllItems(): void
    {
        $result = self::$checkerObject->showAllItems();
        self::assertEquals(['id' => 1, 'cell' => 'a1', 'team' => 'white', 'figure' => 'checker'], $result[0]);
    }

    public function testGetAreaForWalk(): void
    {
        try {
            $result = self::$checkerObject->getAreaForWalk('c3', 1);
            self::assertEquals(['d4', 'b4', 'd2', 'b2'], $result);
        } catch (\RuntimeException $exception) {
            self::fail($exception->getMessage());
        }
    }

    public function testGetFuturePositionAfterBeat(): void
    {
        self::assertEquals('c3', self::$checkerObject->getFuturePositionAfterBeat('a1', 'b2'));
    }

    public function testIsStepAfterAttackOnDesk(): void
    {
        self::assertFalse(self::$checkerObject->isStepAfterAttackOnDesk('g3', 'h4'));
        self::assertFalse(self::$checkerObject->isStepAfterAttackOnDesk('b2', 'a1'));
        self::assertTrue(self::$checkerObject->isStepAfterAttackOnDesk('a1', 'b2'));
    }

    public function testIsStepForAttack(): void
    {
        self::assertTrue(self::$checkerObject->isStepForAttack('f8', 'black'));
        self::assertTrue(self::$checkerObject->isStepForAttack('e1', 'white'));
        self::assertFalse(self::$checkerObject->isStepForAttack('d6', 'white'));
    }

    public function testIsStepForMove(): void
    {
        self::assertTrue(self::$checkerObject->isStepForMove('d5'));
        self::assertFalse(self::$checkerObject->isStepForMove('f8'));
    }

    public function testIsCheckerOnDesk(): void
    {
        self::assertTrue(self::$checkerObject->isCheckerInDesk('a1', 'b2'));
        self::assertFalse(self::$checkerObject->isCheckerInDesk('a0', 'b2'));
    }

    public function testIsCellAfterAttackAvailable(): void
    {
        self::assertFalse(self::$checkerObject->isCellAfterAttackAvailable('d4', 'e5'));
        self::assertTrue(self::$checkerObject->isCellAfterAttackAvailable('c3', 'b4'));
    }

    public function testIsCheckerInTeam(): void
    {
        self::assertTrue(self::$checkerObject->isCheckerInTeam('b2', 'white'));
        self::assertFalse(self::$checkerObject->isCheckerInTeam('b3', 'white'));
    }

    public function testWalk(): void
    {
        self::$checkerObject->walk('a3', 'b4', 'white', 'checker');
        $stepAfterWalk = self::$checkerObject->findOneBy(['cell' => 'b4']);
        self::assertEquals('white', $stepAfterWalk->getTeam());
    }

    public function testAttackOppositePlayer(): void
    {
        self::$checkerObject->attackOppositePlayer('c3', 'd4', 'white', 'checker');
        $stepAfterWalk = self::$checkerObject->findOneBy(['cell' => 'e5']);
        self::assertEquals('white', $stepAfterWalk->getTeam());
    }
}