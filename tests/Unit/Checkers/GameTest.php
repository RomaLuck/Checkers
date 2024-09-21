<?php

declare(strict_types=1);

namespace App\Tests\Unit\Checkers;

use App\Service\Game\Checkers\CheckerDesk;
use App\Service\Game\Checkers\CheckersGame;
use App\Service\Game\Checkers\Team\Black;
use App\Service\Game\Checkers\Team\White;
use App\Service\Game\Move;
use App\Service\Game\MoveResult;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    private CheckersGame $game;

    private White $white;

    private Black $black;

    private MoveResult $startMoveResult;

    protected function setUp(): void
    {
        $this->white = new White(1, 'Roma');
        $this->black = new Black(2, 'Olena');
        $this->startMoveResult = new MoveResult(CheckerDesk::START_DESK, true);
        $this->game = new CheckersGame($this->white, $this->black);
    }

    public function testRun(): void
    {
        $this->assertEquals(0, $this->startMoveResult->getCheckerDesk()[1][3]);

        $move = Move::createMoveWithCellTransform('a3', 'b4');
        $moveResult = $this->game->run($this->startMoveResult, $move);

        $this->assertEquals(0, $moveResult->getCheckerDesk()[0][2]);
        $this->assertEquals(1, $moveResult->getCheckerDesk()[1][3]);
    }

    public function testRunInFalseDirection(): void
    {
        $move = Move::createMoveWithCellTransform('a3', 'b4');
        $firstMoveResult = $this->game->run($this->startMoveResult, $move);

        $move = Move::createMoveWithCellTransform('b4', 'a3');
        $secondMoveResult = $this->game->run($firstMoveResult, $move);

        $this->assertEquals(0, $secondMoveResult->getCheckerDesk()[0][2]);
        $this->assertEquals(1, $secondMoveResult->getCheckerDesk()[1][3]);
    }

    public function testRunWithoutPossibility(): void
    {
        $move = Move::createMoveWithCellTransform('a3', 'c5');
        $moveResult = $this->game->run($this->startMoveResult, $move);

        $this->assertEquals(1, $moveResult->getCheckerDesk()[0][2]);
        $this->assertEquals(0, $moveResult->getCheckerDesk()[2][4]);
    }
}
