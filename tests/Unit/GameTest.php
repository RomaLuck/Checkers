<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Service\Game\CheckerDesk;
use App\Service\Game\Game;
use App\Service\Game\MoveResult;
use App\Service\Game\Team\Black;
use App\Service\Game\Team\White;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    private Game $game;
    private White $white;
    private Black $black;
    private MoveResult $startMoveResult;

    protected function setUp(): void
    {
        $this->white = new White(1, 'Roma');
        $this->black = new Black(2, 'Olena');
        $this->startMoveResult = new MoveResult(CheckerDesk::START_DESK, true);
        $this->game = new Game($this->white, $this->black);
    }

    public function testRun(): void
    {
        $this->assertEquals(0, $this->startMoveResult->getCheckerDesk()[1][3]);

        $moveResult = $this->game->makeMoveWithCellTransform($this->startMoveResult, 'a3', 'b4');

        $this->assertEquals(0, $moveResult->getCheckerDesk()[0][2]);
        $this->assertEquals(1, $moveResult->getCheckerDesk()[1][3]);
    }

    public function testRunInFalseDirection(): void
    {
        $firstMoveResult = $this->game->makeMoveWithCellTransform(
            $this->startMoveResult,
            'a3',
            'b4',
        );

        $secondMoveResult = $this->game->makeMoveWithCellTransform(
            $firstMoveResult,
            'b4',
            'a3',
        );

        $this->assertEquals(0, $secondMoveResult->getCheckerDesk()[0][2]);
        $this->assertEquals(1, $secondMoveResult->getCheckerDesk()[1][3]);
    }

    public function testRunWithoutPossibility(): void
    {
        $moveResult = $this->game->makeMoveWithCellTransform($this->startMoveResult, 'a3', 'c5');

        $this->assertEquals(1, $moveResult->getCheckerDesk()[0][2]);
        $this->assertEquals(0, $moveResult->getCheckerDesk()[2][4]);
    }
}
