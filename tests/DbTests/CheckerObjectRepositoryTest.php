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
        $black = $this->sqlQueryBuilder->select(self::$table, ['team'])
            ->where('cell', ':c')
            ->setParameters([':c' => 'h6'])
            ->getQuery()
            ->findOne();

        $white = $this->sqlQueryBuilder->select(self::$table, ['team'])
            ->where('cell', ':c')
            ->setParameters([':c' => 'd2'])
            ->getQuery()
            ->findOne();

        self::assertEquals('black', $black);
        self::assertEquals('white', $white);
    }

    public function testSplitCell(): void
    {
        $result = self::$checkerObject->getSplitCell('a1', 1);
        self::assertEquals('1', $result);
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

    public function testGetPositionOnDesk(): void
    {
        self::assertEquals('7', self::$checkerObject->getPositionOnDesk('h1', self::$checkerObject::HORIZONTAL_SIDE_OF_DESK));
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
}