<?php

namespace App\Tests\Unit;

use App\Service\Game\CheckerDesk;
use App\Service\Game\Game;
use App\Service\Game\Robot\Robot;
use App\Service\Game\Team\Black;
use App\Service\Game\Team\White;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class MinimaxTest extends TestCase
{
    private $logger;
    private Game $game;
    /**
     * @var array[]
     */
    private array $desk;
    private Robot $minimax;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);

        $robot = new White(1, 'Comp');
        $player = new Black(2, 'Player');

        $this->desk = CheckerDesk::START_DESK;

        $this->game = new Game($robot, $player, $this->logger);
        $this->minimax = new Robot($this->game, $robot, $player, 1);
    }

    public function testMakeMove()
    {
        $this->assertEquals(0, $this->desk[1][3]);

        $updatedDesk = $this->minimax->makeMove($this->desk, [[0, 2], [1, 3]]);

        $this->assertEquals(0, $updatedDesk[0][2]);
        $this->assertEquals(1, $updatedDesk[1][3]);
    }

    public function testRun(): void
    {
        $robot = new White(1, 'Comp');
        $player = new Black(2, 'Player');

        $result = $this->minimax->bestMove($robot, $player, CheckerDesk::START_DESK)[1];

        $this->assertIsArray($result);
    }
}
