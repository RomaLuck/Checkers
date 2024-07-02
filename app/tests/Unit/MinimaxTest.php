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
    public const START_DESK = [
        [1, -1, 1, -1, 0, -1, 2, -1],
        [-1, 1, -1, 0, -1, 2, -1, 2],
        [1, -1, 1, -1, 0, -1, 2, -1],
        [-1, 1, -1, 0, -1, 2, -1, 2],
        [1, -1, 1, -1, 0, -1, 2, -1],
        [-1, 1, -1, 0, -1, 2, -1, 2],
        [1, -1, 1, -1, 0, -1, 2, -1],
        [-1, 1, -1, 0, -1, 2, -1, 2],
    ];

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    public function testRun(): void
    {
        $desk = new CheckerDesk(self::START_DESK);
        $robot = new White(1, 'Comp');
        $player = new Black(2, 'Player');
        $game = new Game($desk, $robot, $player, $this->logger);
        $minimax = new Robot($game, $robot, 10);
        $result = $minimax->bestMove()[1];

        $this->assertEquals([[2, 2], [0, 4]], $result);
    }
}
