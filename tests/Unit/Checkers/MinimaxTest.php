<?php

namespace App\Tests\Unit\Checkers;

use App\Service\Game\Checkers\CheckerDesk;
use App\Service\Game\Checkers\CheckersGame;
use App\Service\Game\Checkers\Robot\Robot;
use App\Service\Game\Checkers\Team\Black;
use App\Service\Game\Checkers\Team\White;
use App\Service\Game\Move;
use App\Service\Game\MoveResult;
use PHPUnit\Framework\TestCase;

class MinimaxTest extends TestCase
{
    private CheckersGame $game;

    private MoveResult $startCondition;

    private Robot $minimax;

    protected function setUp(): void
    {
        $robot = new White(1, 'Comp');
        $player = new Black(2, 'Player');

        $this->startCondition = new MoveResult(CheckerDesk::START_DESK, true);

        $this->game = new CheckersGame($robot, $player);
        $this->minimax = new Robot($this->game, $robot, $player);
    }

    public function testMakeMove()
    {
        $this->assertEquals(0, $this->startCondition->getCheckerDesk()[1][3]);

        $move = new Move([0, 2], [1, 3]);
        $moveResult = $this->minimax->makeMove($this->startCondition, $move);

        $this->assertEquals(0, $moveResult->getCheckerDesk()[0][2]);
        $this->assertEquals(1, $moveResult->getCheckerDesk()[1][3]);
    }

    public function testRun(): void
    {
        $robot = new White(1, 'Comp');
        $player = new Black(2, 'Player');

        $result = $this->minimax->bestMove($robot, $player, $this->startCondition)[1];

        $this->assertInstanceOf(Move::class, $result);
    }
}
